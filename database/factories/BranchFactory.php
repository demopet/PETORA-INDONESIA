<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => 'Cabang '.fake()->city(),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'email' => fake()->companyEmail(),
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
            'receipt_header' => null,
            'receipt_footer' => null,
            'settings' => [],
        ];
    }
}
