<?php

namespace Database\Factories;

use App\Models\UserTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTask>
 */
class UserTaskFactory extends Factory
{
    protected $model = UserTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = [
            UserTask::STATUS_TAKEN,
            UserTask::STATUS_PENDING_VERIFICATION_1,
            UserTask::STATUS_PENDING_VERIFICATION_2,
            UserTask::STATUS_COMPLETED,
            UserTask::STATUS_CANCELLED,
            UserTask::STATUS_FAILED,
        ];

        $status = $this->faker->randomElement($statuses);
        // Use recent time (within last 5 minutes) so proof 1 deadline won't expire
        $takenAt = now()->subMinutes($this->faker->numberBetween(1, 5));

        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'status' => $status,
            'taken_at' => $takenAt,
            'deadline_at' => now()->addHours($this->faker->numberBetween(1, 24)),
            'cancelled_at' => $status === UserTask::STATUS_CANCELLED ? now()->subMinutes($this->faker->numberBetween(10, 60)) : null,
            'failed_count' => $this->faker->numberBetween(0, 3),
            'verification_1_status' => in_array($status, [
                UserTask::STATUS_PENDING_VERIFICATION_1,
                UserTask::STATUS_PENDING_VERIFICATION_2,
                UserTask::STATUS_COMPLETED
            ]) ? 'Submitted at ' . now()->subMinutes(5)->format('Y-m-d H:i:s') . '. Files: screenshot_member_list.jpg, bukti_join_grup.jpg. Description: Sudah berhasil mengajak ' . rand(5, 15) . ' orang join ke grup WhatsApp. Screenshot terlampir menunjukkan daftar member baru dan konfirmasi mereka di grup.' : null,
            'verification_2_status' => in_array($status, [
                UserTask::STATUS_PENDING_VERIFICATION_2,
                UserTask::STATUS_COMPLETED
            ]) ? 'Submitted at ' . now()->subMinutes(3)->format('Y-m-d H:i:s') . '. Files: bukti_member_aktif.jpg, screenshot_chat.jpg. Description: Member sudah aktif di grup selama 3 hari. Bukti screenshot chat dan interaksi mereka terlampir.' : null,
            'payment_status' => $status === UserTask::STATUS_COMPLETED
                ? $this->faker->randomElement([UserTask::PAYMENT_SUCCESS, UserTask::PAYMENT_PENDING])
                : UserTask::PAYMENT_PENDING,
            'payment_amount' => $status === UserTask::STATUS_COMPLETED
                ? $this->faker->randomElement([10000, 15000, 20000, 25000, 30000, 35000])
                : null,
            'payment_verified_by_admin_id' => null,
        ];
    }

    /**
     * Indicate that the user task is taken.
     */
    public function taken(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => UserTask::STATUS_TAKEN,
            'taken_at' => now()->subMinutes(2), // 2 minutes ago, still within 10 min deadline
            'verification_1_status' => null,
            'verification_2_status' => null,
        ]);
    }

    /**
     * Indicate that the user task is completed.
     */
    public function completed(): static
    {
        $takenAt = now()->subHours(2);
        $proof1SubmittedAt = $takenAt->copy()->addMinutes(5);
        $proof2SubmittedAt = $proof1SubmittedAt->copy()->addMinutes(30);
        $completedAt = $proof2SubmittedAt->copy()->addMinutes(10);

        return $this->state(fn(array $attributes) => [
            'status' => UserTask::STATUS_COMPLETED,
            'taken_at' => $takenAt,
            'completed_at' => $completedAt,
            'verification_1_status' => 'Submitted at ' . $proof1SubmittedAt->format('Y-m-d H:i:s') . '. Files: proof1_screenshot.jpg, proof1_evidence.jpg. Description: Berhasil mengajak 10 orang join grup. - Approved by admin at ' . $proof1SubmittedAt->copy()->addMinutes(5)->format('Y-m-d H:i:s'),
            'verification_2_status' => 'Submitted at ' . $proof2SubmittedAt->format('Y-m-d H:i:s') . '. Files: proof2_screenshot.jpg, proof2_chat.jpg. Description: Member sudah aktif 3 hari. - Approved by admin at ' . $completedAt->format('Y-m-d H:i:s'),
            'verification_1_approved_at' => $proof1SubmittedAt->copy()->addMinutes(5),
            'verification_2_approved_at' => $completedAt,
            'payment_status' => UserTask::PAYMENT_SUCCESS,
            'payment_amount' => $this->faker->randomElement([10000, 15000, 20000, 25000, 30000, 35000]),
        ]);
    }

    /**
     * Indicate that the user task is pending verification.
     */
    public function pendingVerification(): static
    {
        $takenAt = now()->subMinutes(15);
        $submittedAt = $takenAt->copy()->addMinutes(8);

        return $this->state(fn(array $attributes) => [
            'status'                => UserTask::STATUS_PENDING_VERIFICATION_1,
            'taken_at'              => $takenAt,
            'verification_1_status' => 'Submitted at ' . $submittedAt->format('Y-m-d H:i:s') . '. Description: Target rekrutmen anggota sudah terpenuhi. Screenshot daftar member baru dan konfirmasi join di grup sudah terlampir. Menunggu pengecekan dari admin.',
            'verification_2_status' => null,
        ]);
    }
}
