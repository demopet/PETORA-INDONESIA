<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class SystemHealth extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup = 'Platform';

    protected static ?string $navigationLabel = 'Kesehatan Sistem';

    protected static ?string $title = 'Kesehatan Sistem';

    protected string $view = 'filament.pages.system-health';

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $checks = [
            'app' => ['ok' => true, 'message' => 'Aplikasi berjalan'],
        ];

        try {
            DB::connection()->getPdo();
            $checks['database'] = ['ok' => true, 'message' => 'Koneksi basis data OK'];
        } catch (\Throwable $e) {
            $checks['database'] = ['ok' => false, 'message' => 'Basis data tidak tersedia: '.$e->getMessage()];
        }

        try {
            Cache::put('health_check', 'ok', 10);
            $checks['cache'] = ['ok' => Cache::get('health_check') === 'ok', 'message' => 'Cache dapat diakses'];
        } catch (\Throwable $e) {
            $checks['cache'] = ['ok' => false, 'message' => 'Cache bermasalah: '.$e->getMessage()];
        }

        $checks['queue'] = ['ok' => true, 'message' => 'Driver antrean: '.config('queue.default')];
        $checks['storage'] = ['ok' => is_writable(storage_path()), 'message' => is_writable(storage_path()) ? 'Storage dapat ditulis' : 'Storage tidak dapat ditulis'];

        return ['checks' => $checks];
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('platform.health.view');
    }
}
