# PROGRESS — PETORA INDONESIA

Pembaruan: 2026-11-13 (Fase 1 — perbaikan konteks cabang, proteksi Owner, scoping urutan dokumen)

## Fase aktif
**Fase 1 — Platform inti** (berjalan, fondasi platform inti sebagian terimplementasi; baseline uji lokal hijau)

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
- `phpstan.neon`: ignore `method.childParameterType` untuk `BranchScope` (kontrak Scope Eloquent); ignore `trait.unused` untuk `BelongsToBranch` dihapus setelah trait dipakai `DocumentSequence`.

## Hambatan terbuka
- Tidak ada kredensial nyata penyedia (Midtrans, Xendit, Meta/WhatsApp, R2, Resend). Driver palsu/sandbox & HTTP fake (A-0006).

## Perintah penting
- `composer install && npm ci && cp .env.example .env && php artisan key:generate`
- Uji lokal (tanpa pdo_pgsql): `DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --compact`
- `vendor/bin/pint --test` dan `vendor/bin/phpstan analyse --memory-limit=1G`
- Uji/CI target (PostgreSQL): set `DB_CONNECTION=pgsql` ke DB uji `*_test` lalu `composer verify`

## Progres Fase 1 (berjalan)
- [x] Skema/model inti platform: `branches`, `settings`, `document_sequences`, `branch_user`, konteks cabang aktif pada `users`; factories dasar.
- [x] Spatie Permission `teams` diaktifkan (konteks cabang), seeder peran bawaan (Owner–Keuangan) & izin platform dasar; user aktif/nonaktif.
- [x] Layanan inti awal: `Settings` (typed), `DocumentNumberService` (alokasi nomor transaksional/lock, bypass `BranchScope` untuk target `branch_id` eksplisit), `BranchContext`, `SetupState`.
- [x] Panel Filament: login/reset/profile, branding PETORA, global search, nav grup, atribusi wajib footer "Powered by PETORA INDONESIA" (uji), middleware konteks tim permission & setup; hook UI switcher cabang.
- [x] Halaman/resource awal: `SetupWizard` (akun Owner+cabang+pengaturan awal), `SystemHealth`, `ManageSettings`, `BranchResource`, `UserResource`, `RoleResource`.
- [x] i18n dasar `lang/id|en/app.php`; endpoint health `/.well-known/petora-health`.
- [x] MFA/2FA app-based Filament diaktifkan di panel (kolom `app_authentication_*`, `User` implements MFA Filament); Fortify 2FA tetap terpasang (integrasi/kebijakan lengkap menyusul).
- [x] Kontrol cabang awal: `POST /admin/branch-switch/{branch}` (`BranchSwitcherController`), `SetPermissionTeamContext` middleware, penyimpanan cabang aktif sesi/user.
- [x] RBAC/policy awal: `UserPolicy`, `BranchPolicy`, `RolePolicy` terdaftar; `OwnerProtection` (invarian minimal satu Owner aktif, sadar team-scoped `model_has_roles`), pengamanan hapus/nonaktifkan/pencabutan peran Owner di alur resource/page.
- [x] **Konteks cabang & izin tim (2026-11-13):** `BranchContext::isAllBranches()` hanya sah dengan izin `platform.branches.view_all`; `permissionTeamId()` menyelesaikan tim izin saat mode gabungan (cabang aktif/pertama pengguna) agar peran/izin tim tetap efektif; `SetPermissionTeamContext` memakai `permissionTeamId()`.
- [x] **Scoping bercabang awal:** `DocumentSequence` memakai `BelongsToBranch`; pengujian scope cabang dan perilaku nomor dokumen eksplisit lintas konteks (`tests/Feature/DocumentSequenceBranchScopeTest.php`).
- [x] Uji Pest fitur platform inti & konteks/proteksi Owner/urutan dokumen — **19 tes/46 asersi lulus** lokal (SQLite fallback D-0009). Pint & PHPStan bersih lokal pada perubahan ini.
- [ ] Belum lengkap: kebijakan 2FA wajib per peran & alur Fortify/panel penuh, RBAC lengkap (cakupan data semua/cabang/milik sendiri, batas/persetujuan/PIN, pengecualian per orang + alasan, penyembunyian data sensitif server-side, matriks + pencarian/peringatan/"lihat sebagai peran", klon peran, proteksi Owner komprehensif di semua aksi/policy), tenancy/branch scope Eloquent `branch_id` & mode gabungan sah end-to-end (seluruh model/tabel bercakupan), penugasan peran per cabang end-to-end + uji isolasi, penomoran per jenis/cabang terintegrasi + uji konkurensi, audit/log UI kaya, wizard komprehensif (pemeriksaan publik jujur, data contoh muat/hapus), kesehatan sistem lengkap, i18n UI menyeluruh, palet perintah & kerangka UX, migrasi/uji PostgreSQL lokal ber-ekstensi lengkap.

## Langkah berikutnya
1. Commit & dorong perubahan terverifikasi (konteks cabang/izin tim, `OwnerProtection` teams-aware, `DocumentSequence` scoped, uji baru, perbaikan `phpstan.neon`) dan pastikan CI hijau.
2. Terapkan `BelongsToBranch`/scope pada model bercakupan cabang berikutnya secara end-to-end + uji isolasi & mode gabungan sah; perkuat penugasan peran per cabang (teams) di UI/resource & kebijakan 2FA per peran.
3. Lengkapi RBAC (cakupan/penyembunyian data, batas/persetujuan/PIN, pengecualian beralasan, matriks/"lihat sebagai peran"), wizard (pemeriksaan app/DB/alamat publik jujur, data contoh), audit/log, kesehatan sistem, penomoran terintegrasi + uji konkurensi, i18n/UX/palet perintah; `composer verify` di PostgreSQL+ekstensi lengkap; perbarui `TRACEABILITY.md`.
