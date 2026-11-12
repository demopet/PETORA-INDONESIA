# PROGRESS — PETORA INDONESIA

Pembaruan: 2026-11-12 (Fase 0 — wiring & baseline fondasi)

## Fase aktif
**Fase 0 — Fondasi** (baseline hijau: lokal SQLite & CI GitHub Actions PostgreSQL+Redis lulus; validasi ekstensi PHP lengkap di lingkungan dev lokal masih tertunda)

## Tugas
- [x] Baca `agent.md` dan `README.md`; pahami hierarki sumber kebenaran.
- [x] Salin spesifikasi produk ke `docs/SPEC.md`.
- [x] Buat dokumen dasar: `DECISIONS.md`, `ASSUMPTIONS.md`, `ARCHITECTURE.md`, `TRACEABILITY.md`, `BUSINESS_RULES.md`, `TESTING.md`, `INTEGRATIONS.md`, `INSTALLATION.md`, `OPERATIONS.md`, `SECURITY.md`, panduan per peran (`docs/guides/*.md`).
- [x] Tulis ulang bagian teknologi/instalasi README sesuai stack Laravel tanpa menghapus isi produk.
- [x] Buat `CHANGELOG.md`, `LICENSE` (draf), `.env.example`, `.gitignore`.
- [x] Inisialisasi proyek Laravel 13.42.0 (PHP 8.4) di root.
- [x] Pasang tooling dev: Pest 4.7.8, pest-plugin-laravel/arch, Larastan 3.15 (PHPStan 2.3.5), Pint 1.32.3. Konfigurasi `pint.json`, `phpstan.neon`.
- [x] Pasang paket inti: Filament 5.11.2, Livewire 4.4.8, Fortify 1.42, Sanctum 4.4, Horizon 5.51, Spatie permission 8.5, multitenancy 4.3, activitylog 5.1, medialibrary 11.23, predis 3.7. Autoload `Modules\`.
- [x] Wiring paket awal: publish config/migrasi (permission, activitylog, medialibrary, multitenancy, sanctum), instal Horizon & Filament panel admin, instal Fortify; `Model::shouldBeStrict(!production)`; `User` memakai `HasApiTokens`, `HasRoles`, `TwoFactorAuthenticatable`, `FilamentUser`; Horizon dikonfigurasi antrean `critical,default,notifications,reports`.
- [x] Konversi uji ke Pest (`tests/Pest.php`), baseline uji lulus lokal, endpoint health `/.well-known/petora-health` + uji.
- [x] Kerangka modular monolith `modules/*` (19 modul, skeleton direktori + README) dan arch test Pest awal.
- [x] CI GitHub Actions (`.github/workflows/ci.yml`) dengan PostgreSQL 17 & Redis; `docker-compose.yml` dev (app/db/redis/horizon/scheduler) dan `Dockerfile` dasar.
- [x] `composer verify` hijau lokal (lint, analisis, uji, 6 tes/8 asersi, fallback SQLite/D-0009) dan **CI GitHub Actions hijau** (PostgreSQL 17 + Redis, run `43263102997`).
- [x] Commit fondasi dibuat (`523f7cc`) dan tag `v0.0.0` ditandai/push; perbaikan CI lockfile dipush (`972ccfd`).
- [x] Validasi hijau di CI GitHub (PostgreSQL+Redis). Tertunda di lingkungan lokal: ekstensi PHP lengkap (pcntl, exif, intl, zip, pdo_pgsql, redis) dan migrasi/uji PostgreSQL lokal.

## Utang teknis disadari
- Ekstensi PHP belum tersedia di lingkungan dev saat ini: `pcntl`, `exif`, `intl`, `zip`, `pdo_pgsql`/`pgsql`, `redis` (ext). Instal composer awal memakai `--ignore-platform-reqs`; image/Dockerfile sudah mendeklarasikan ekstensi target. Horizon (pcntl), medialibrary (exif), Filament/Spout (intl/zip), runtime (pgsql, redis ext) memerlukan ini.
- Baseline uji lokal memakai SQLite in-memory (D-0009, `.env.testing`) karena `pdo_pgsql` absen; phpunit env DB tidak dipaksa agar CI dapat override ke PostgreSQL. Uji konkurensi/constraint PostgreSQL wajib dijalankan di CI/env siap.
- Migrasi belum dieksekusi terhadap PostgreSQL di lingkungan ini; `migrate:status` lokal gagal karena driver pgsql tidak ada (kontainer postgres tersedia).

## Hambatan terbuka
- Tidak ada kredensial nyata penyedia (Midtrans, Xendit, Meta/WhatsApp, R2, Resend). Driver palsu/sandbox & HTTP fake (A-0006).

## Perintah penting
- `composer install && npm ci && cp .env.example .env && php artisan key:generate`
- Uji lokal (tanpa pdo_pgsql): `cp .env.testing .env.testing.local` opsional, atau jalankan `DB_CONNECTION=sqlite DB_DATABASE=:memory: composer verify`
- Uji/CI target (PostgreSQL): set `DB_CONNECTION=pgsql` ke DB uji `*_test` lalu `composer verify`

## Langkah berikutnya
1. Siapkan lingkungan dev/image dengan ekstensi PHP lengkap (pcntl, exif, intl, zip, pdo_pgsql, redis) dan jalankan migrasi + `composer verify` terhadap PostgreSQL nyata secara lokal.
2. Tutup gerbang Fase 0 (rapikan utang teknis baseline bila perlu), lalu mulai **Fase 1 — Platform inti**: Identity (Fortify/Filament auth+2FA), RBAC Spatie Permission (teams/cabang), tenancy/branch scope, pengaturan, penomoran, audit, branding/atribusi, i18n, UI kerangka, palet perintah, wizard, kesehatan sistem.
