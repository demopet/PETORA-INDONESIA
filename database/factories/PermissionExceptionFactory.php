<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PermissionExceptionMode;
use App\Models\PermissionException;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Permission;

/**
 * @extends Factory<PermissionException>
 */
class PermissionExceptionFactory extends Factory
{
    protected $model = PermissionException::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'permission_id' => fn (): int => (int) Permission::query()->create([
                'name' => 'test.permission.'.uniqid('', true),
                'guard_name' => 'web',
            ])->id,
            'mode' => PermissionExceptionMode::Revoke,
            'reason' => 'Contoh alasan pengecualian',
            'created_by' => null,
        ];
    }

    public function grant(): static
    {
        return $this->state(fn (array $attributes): array => [
            'mode' => PermissionExceptionMode::Grant,
        ]);
    }

    public function revoke(): static
    {
        return $this->state(fn (array $attributes): array => [
            'mode' => PermissionExceptionMode::Revoke,
        ]);
    }
}
