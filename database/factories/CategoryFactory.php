<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $whatsappCategories = [
            [
                'name' => 'WhatsApp Community',
                'description' => 'Ajak orang untuk bergabung ke berbagai grup WhatsApp komunitas. Dapatkan reward untuk setiap member yang berhasil join dan aktif.',
            ],
            [
                'name' => 'WhatsApp Business',
                'description' => 'Promosikan grup WhatsApp untuk keperluan bisnis seperti reseller, dropshipper, dan supplier. Komisi menarik menanti!',
            ],
            [
                'name' => 'WhatsApp Marketing',
                'description' => 'Bantu tingkatkan jumlah member di grup WhatsApp marketing dan sales. Cocok untuk yang suka networking.',
            ],
            [
                'name' => 'WhatsApp Learning',
                'description' => 'Rekrut member untuk grup belajar dan edukasi di WhatsApp. Berbagi ilmu sambil dapat reward.',
            ],
            [
                'name' => 'WhatsApp Network',
                'description' => 'Kembangkan jaringan bisnis melalui grup WhatsApp networking profesional.',
            ],
        ];

        $category = $this->faker->randomElement($whatsappCategories);

        return [
            'name' => $category['name'],
            'description' => $category['description'],
            'expired_at' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category is expired.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'expired_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}
