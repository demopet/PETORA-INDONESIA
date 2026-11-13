# PROGRESS — PETORA INDONESIA

Pembaruan: 2026-11-13 (Fase 1 — konteks cabang/izin tim, proteksi Owner, scoping urutan dokumen, audit log model awal)

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
- [x] **Audit log model awal:** `Branch`, `Setting`, `User` memakai `LogsActivity` (atribut terbatas, tanpa rahasia/kredensial/sandi) dengan uji `tests/Feature/ActivityLogTest.php`; `ActivityLogResource` baca-saja tersedia.
- [x] **Wizard & kesehatan sistem (awal):** wizard menampilkan pemeriksaan app/DB/URL publik (pesan jujur, bukan uji jaringan webhook) dan opsi muat data contoh; `SystemHealth` dapat menjalankan pemeriksaan endpoint publik terkonfigurasi dan memuat/menghapus data contoh (`app/Support/SampleData.php`) dengan uji fitur.
- [x] **Fondasi peran per cabang:** helper `app/Support/BranchRoleSync.php` dan UI `UserResource` (penugasan peran per cabang via repeater) untuk sinkronisasi/baca peran per cabang (team-scoped roles), dengan uji.
- [x] **Kebijakan MFA per peran (awal):** `app/Support/MultiFactorPolicy.php` + pengaturan di `ManageSettings` dan enforcement `isRequired` di panel Filament; MFA app-based dapat diwajibkan berdasarkan peran (lintas cabang), dengan uji.
- [x] **UX/audit awal:** filter log audit (log/peristiwa/tanggal) + pencarian global untuk Branch/User/ActivityLog resource.
- [x] Uji Pest fitur platform inti, konteks/proteksi Owner, urutan dokumen, audit log, data contoh, peran per cabang, kebijakan MFA, proteksi peran Owner, dan pencarian resource — **28 tes/83 asersi lulus** lokal (SQLite fallback D-0009). Pint & PHPStan bersih lokal pada perubahan terverifikasi.
- [ ] Belum lengkap: alur Fortify/panel 2FA penuh & pemulihan/pemaksaan menyeluruh, RBAC lengkap (cakupan data semua/cabang/milik sendiri, batas/persetujuan/PIN, pengecualian per orang + alasan, penyembunyian data sensitif server-side, matriks + pencarian/peringatan/"lihat sebagai peran", klon peran lanjutan, proteksi Owner komprehensif di semua aksi/policy), tenancy/branch scope Eloquent `branch_id` & mode gabungan sah end-to-end (seluruh model/tabel bercakupan) + uji isolasi, penomoran per jenis/cabang terintegrasi + uji konkurensi, audit/log UI & cakupan peristiwa lebih kaya, wizard komprehensif penuh, kesehatan sistem lengkap (integrasi/antrean/backup detail), i18n UI menyeluruh, palet perintah & kerangka UX, migrasi/uji PostgreSQL lokal ber-ekstensi lengkap.

## Langkah berikutnya
1. Pastikan CI commit terbaru hijau; siapkan env PostgreSQL+ekstensi lengkap untuk verifikasi penuh dan uji konkurensi.
2. Lengkapi RBAC (cakupan data semua/cabang/milik sendiri, batas/persetujuan/PIN, pengecualian per orang beralasan, penyembunyian data sensitif server-side, matriks/pencarian/peringatan/"lihat sebagai peran") dan proteksi Owner/policy menyeluruh dengan uji.
3. Terapkan tenancy/scoping `branch_id` end-to-end + uji isolasi & mode gabungan sah, penomoran per jenis/cabang + uji konkurensi, audit/log & kesehatan sistem lebih kaya, wizard/UX/i18n/palet perintah; `composer verify` PostgreSQL; perbarui `TRACEABILITY.md` dan tutup gerbang Fase 1.
