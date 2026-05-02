<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $difficulties = [Task::DIFFICULTY_EASY, Task::DIFFICULTY_MEDIUM, Task::DIFFICULTY_HARD];

        $groupNames = [
            'Komunitas Freelancer',
            'Grup Bisnis Online',
            'Belajar Digital Marketing',
            'Reseller Fashion',
            'Dropshipper Indonesia',
            'Tim Marketing Pro',
            'Affiliate Marketer',
            'Pengusaha Muda',
            'Startup Community',
            'E-commerce Seller',
        ];

        $difficulty = $this->faker->randomElement($difficulties);
        $memberTarget = match ($difficulty) {
            Task::DIFFICULTY_EASY => $this->faker->numberBetween(3, 7),
            Task::DIFFICULTY_MEDIUM => $this->faker->numberBetween(8, 12),
            Task::DIFFICULTY_HARD => $this->faker->numberBetween(13, 20),
        };

        $groupName = $this->faker->randomElement($groupNames);

        return [
            'category_id' => Category::factory(),
            'admin_id' => User::factory(),
            'title' => $groupName . ' ' . $this->faker->randomElement(['Network', 'Group', 'Community', 'Team']),
            'vcf_data' => null,
            'description' => "Ajak orang untuk bergabung ke grup WhatsApp {$groupName}.\n\nTarget:\n- Minimal {$memberTarget} member baru\n- Upload screenshot bukti join grup\n- Member harus konfirmasi dengan mention admin\n- Pastikan member aktif minimal 2 hari\n\nReward akan diberikan setelah semua syarat terpenuhi.",
            'whatsapp_group_link' => 'https://chat.whatsapp.com/' . strtoupper($this->faker->bothify('???###???')),
            'difficulty_level' => $difficulty,
            'expired_at' => $this->faker->dateTimeBetween('now', '+3 weeks'),
            'is_expired' => false,
            'priority_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the task is easy.
     */
    public function easy(): static
    {
        return $this->state(fn(array $attributes) => [
            'difficulty_level' => Task::DIFFICULTY_EASY,
        ]);
    }

    /**
     * Indicate that the task is medium difficulty.
     */
    public function medium(): static
    {
        return $this->state(fn(array $attributes) => [
            'difficulty_level' => Task::DIFFICULTY_MEDIUM,
        ]);
    }

    /**
     * Indicate that the task is hard.
     */
    public function hard(): static
    {
        return $this->state(fn(array $attributes) => [
            'difficulty_level' => Task::DIFFICULTY_HARD,
        ]);
    }

    /**
     * Indicate that the task is expired.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'expired_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'is_expired' => true,
        ]);
    }
}
