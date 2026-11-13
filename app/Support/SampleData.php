<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class SampleData
{
    public const FLAG_KEY = 'platform.sample_data_loaded';

    public static function isLoaded(): bool
    {
        return (bool) Settings::get(self::FLAG_KEY, false);
    }

    public static function load(): void
    {
        if (self::isLoaded()) {
            return;
        }

        if (! SetupState::isCompleted()) {
            throw new \RuntimeException('Selesaikan pengaturan awal sebelum memuat data contoh.');
        }

        $registrar = app(PermissionRegistrar::class);

        $hq = Branch::query()->where('code', 'HQ')->first() ?? Branch::query()->orderBy('id')->first();
        if (! $hq) {
            throw new \RuntimeException('Tidak ada cabang untuk memuat data contoh.');
        }

        $demoBranch = Branch::query()->firstOrCreate(
            ['code' => 'DEMO'],
            [
                'name' => 'Cabang Contoh',
                'address' => 'Alamat contoh cabang demo',
                'phone' => '081234567890',
                'email' => 'demo@petora.test',
                'timezone' => $hq->timezone ?? 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        $demoUsers = [
            ['name' => 'Manajer Contoh', 'email' => 'manajer.demo@petora.test', 'role' => 'Manajer Cabang'],
            ['name' => 'Kasir Contoh', 'email' => 'kasir.demo@petora.test', 'role' => 'Kasir'],
            ['name' => 'Dokter Contoh', 'email' => 'dokter.demo@petora.test', 'role' => 'Dokter Hewan'],
        ];

        foreach ($demoUsers as $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'active_branch_id' => $demoBranch->id,
                ]
            );
            $user->branches()->syncWithoutDetaching([$demoBranch->id]);
            if (! $user->active_branch_id) {
                $user->forceFill(['active_branch_id' => $demoBranch->id])->save();
            }

            $registrar->setPermissionsTeamId($demoBranch->id);
            $registrar->forgetCachedPermissions();
            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }

        $registrar->forgetCachedPermissions();
        Settings::set(self::FLAG_KEY, true, 'bool', 'platform');
        activity('platform')->withProperties(['sample_data' => 'loaded'])->log('sample_data.loaded');
    }

    public static function remove(): void
    {
        $registrar = app(PermissionRegistrar::class);

        $emails = ['manajer.demo@petora.test', 'kasir.demo@petora.test', 'dokter.demo@petora.test'];
        $users = User::query()->whereIn('email', $emails)->get();

        foreach ($users as $user) {
            $branchIds = $user->branches()->pluck('branches.id')->all();
            foreach ($branchIds as $branchId) {
                $registrar->setPermissionsTeamId((int) $branchId);
                $registrar->forgetCachedPermissions();
                $user->syncRoles([]);
            }
            $user->branches()->detach();
            $user->delete();
        }

        $demoBranch = Branch::query()->where('code', 'DEMO')->first();
        if ($demoBranch && $demoBranch->code === 'DEMO') {
            // Only remove if no other users remain attached (besides deleted demo users).
            $remaining = $demoBranch->users()->count();
            if ($remaining === 0) {
                $demoBranch->delete();
            }
        }

        $registrar->forgetCachedPermissions();
        Settings::set(self::FLAG_KEY, false, 'bool', 'platform');
        activity('platform')->withProperties(['sample_data' => 'removed'])->log('sample_data.removed');
    }
}
