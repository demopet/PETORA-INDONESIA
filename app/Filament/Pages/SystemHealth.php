<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Support\SampleData;
use App\Support\SetupState;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use UnitEnum;

class SystemHealth extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup = 'Platform';

    protected static ?string $navigationLabel = 'Kesehatan Sistem';

    protected static ?string $title = 'Kesehatan Sistem';

    protected string $view = 'filament.pages.system-health';

    /** @var array<string, mixed> */
    public array $publicCheck = [];

    public function mount(): void
    {
        $this->publicCheck = [];
    }

    public function runPublicUrlCheck(): void
    {
        $url = (string) config('app.url', '');
        $result = [
            'url' => $url,
            'ok' => false,
            'message' => '',
            'note' => 'Pemeriksaan ini dilakukan dari server aplikasi ke URL yang dikonfigurasi, bukan dari jaringan penyedia webhook.',
        ];

        if ($url === '' || ! str_starts_with($url, 'https://')) {
            $result['message'] = 'PUBLIC_APP_URL/APP_URL harus berupa URL HTTPS publik yang valid untuk pemeriksaan ini.';
            $this->publicCheck = $result;

            return;
        }

        try {
            $endpoint = rtrim($url, '/').'/.well-known/petora-health';
            $response = Http::timeout(5)->acceptJson()->get($endpoint);
            $result['ok'] = $response->successful();
            $result['message'] = $result['ok']
                ? 'Endpoint kesehatan merespons melalui URL publik terkonfigurasi.'
                : 'Endpoint kesehatan tidak merespons sukses (HTTP '.$response->status().').';
        } catch (\Throwable $e) {
            $result['message'] = 'Gagal menghubungi URL publik: '.$e->getMessage();
        }

        $this->publicCheck = $result;
    }

    public function loadSampleData(): void
    {
        try {
            SampleData::load();
            Notification::make()->title('Data contoh dimuat')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Gagal memuat data contoh')->body($e->getMessage())->danger()->send();
        }
    }

    public function removeSampleData(): void
    {
        try {
            SampleData::remove();
            Notification::make()->title('Data contoh dihapus')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Gagal menghapus data contoh')->body($e->getMessage())->danger()->send();
        }
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $checks = [
            'app' => ['ok' => true, 'message' => 'Aplikasi berjalan ('.config('app.env').')'],
        ];

        try {
            DB::connection()->getPdo();
            $checks['database'] = ['ok' => true, 'message' => 'Koneksi basis data OK ('.config('database.default').')'];
        } catch (\Throwable $e) {
            $checks['database'] = ['ok' => false, 'message' => 'Basis data tidak tersedia: '.$e->getMessage()];
        }

        try {
            Cache::put('health_check', 'ok', 10);
            $checks['cache'] = ['ok' => Cache::get('health_check') === 'ok', 'message' => 'Cache dapat diakses ('.config('cache.default').')'];
        } catch (\Throwable $e) {
            $checks['cache'] = ['ok' => false, 'message' => 'Cache bermasalah: '.$e->getMessage()];
        }

        $checks['queue'] = ['ok' => true, 'message' => 'Driver antrean: '.config('queue.default').' (Horizon/worker harus berjalan untuk antrean async)'];
        $checks['storage'] = ['ok' => is_writable(storage_path()), 'message' => is_writable(storage_path()) ? 'Storage dapat ditulis' : 'Storage tidak dapat ditulis'];
        $checks['setup'] = ['ok' => SetupState::isCompleted(), 'message' => SetupState::isCompleted() ? 'Pengaturan awal selesai' : 'Pengaturan awal belum selesai'];

        return [
            'checks' => $checks,
            'sampleLoaded' => SampleData::isLoaded(),
            'publicCheck' => $this->publicCheck,
        ];
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('platform.health.view');
    }
}
