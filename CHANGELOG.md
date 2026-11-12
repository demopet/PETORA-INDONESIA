# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di berkas ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.1.0/) dan versi mengikuti [Semantic Versioning](https://semver.org/lang/id/).

## [Unreleased]
### Added
- Fase 1 (awal): migrasi/model platform inti (branches, settings, document_sequences, branch_user, konteks cabang aktif), Settings helper, DocumentNumberService, BranchContext, SetupState.
- RBAC awal: Spatie Permission teams aktif, seeder peran bawaan & izin platform, User dengan cabang/roles/is_active.
- Panel Filament: middleware konteks tim & setup, atribusi wajib footer, halaman SetupWizard/SystemHealth/ManageSettings, resource Cabang/Pengguna/Peran.
- Uji Pest fitur platform inti (13 tes/25 asersi) dan i18n dasar id/en.

### Added
- Fondasi Fase 0: `docs/SPEC.md`, matriks `docs/TRACEABILITY.md`, `docs/BUSINESS_RULES.md`, `docs/DECISIONS.md`, `docs/ASSUMPTIONS.md`, `docs/ARCHITECTURE.md`, `docs/TESTING.md`, `docs/INTEGRATIONS.md`, `docs/INSTALLATION.md`, `docs/OPERATIONS.md`, `docs/SECURITY.md`, panduan per peran, `PROGRESS.md`, `LICENSE` (draf).
- Proyek Laravel 13.42.0 (PHP 8.4) dengan Filament 5, Livewire 4, Fortify, Sanctum, Horizon, Spatie (permission, multitenancy, activitylog, medialibrary), predis; tooling Pest 4, Larastan, Pint.
- Kerangka modular monolith `modules/*`, arch test awal, endpoint health `/.well-known/petora-health`, CI GitHub Actions (PostgreSQL/Redis), `docker-compose.yml` dev, `Dockerfile` dasar.
- Baseline `composer verify` (lint, analisis, uji Pest) dan konversi uji ke Pest.

### Changed
- README: bagian teknologi/memulai pengembangan diperbarui ke stack Laravel; isi produk dipertahankan.
