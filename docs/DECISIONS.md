## D-0009 — Baseline uji lokal: SQLite in-memory saat `pdo_pgsql` tidak tersedia, PostgreSQL di CI
Tanggal: 2026-11-12   Fase: 0   Status: diterima (sementara)
Konteks: Lingkungan kerja saat ini tidak menyediakan ekstensi PHP `pdo_pgsql/pgsql` (juga `pcntl`, `exif`, `intl`, `zip`, `redis` ext) sehingga uji lokal tidak dapat memakai PostgreSQL nyata. agent.md §8 mensyaratkan PostgreSQL sungguhan untuk uji.
Keputusan: `phpunit.xml` bawaan memakai SQLite in-memory untuk baseline lokal; CI GitHub Actions menjalankan `composer verify` dengan PostgreSQL 17 & Redis (env override) sesuai spesifikasi. `.env.testing` disediakan untuk menjalankan uji lokal. Target: beralih penuh ke PostgreSQL uji saat ekstensi tersedia di image/dev.
Alasan: Memungkinkan verifikasi lint/analisis/uji berjalan di lingkungan terbatas tanpa menghapus persyaratan PostgreSQL di CI. Risiko: perbedaan perilaku SQL/constraint; mitigasi dengan menjalankan CI berbasis PostgreSQL dan uji konkurensi/constraint di Fase berikut saat env siap.

## D-0001 — Versi inti: Laravel 13, PHP 8.4, ekosistem stabil terbaru kompatibel
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: Stack inti ditetapkan (Laravel/Filament/Livewire/Spatie/dll). Lingkungan memiliki PHP 8.4.27 dan Composer 2.10; Laravel 13 adalah rilis stabil saat ini dan mensyaratkan PHP 8.3+. Perlu mengunci set paket yang saling kompatibel.
Keputusan: Gunakan Laravel 13.x (stable) pada PHP 8.4, Filament 5.x, Livewire 4.x, Tailwind CSS 4.x, Pest 4.x, dan rilis stabil terbaru paket Spatie (laravel-permission, laravel-multitenancy, activitylog, medialibrary), Laravel Fortify, Sanctum, Horizon, dan Pint. Kompatibilitas diverifikasi saat instalasi; versi dikunci lewat `composer.lock` dan `package-lock.json`.
Alasan: Memenuhi mandat "versi stabil terbaru yang saling kompatibel", memanfaatkan platform PHP 8.4 yang tersedia, dan meminimalkan utang upgrade dini. Risiko: kompatibilitas minor antar paket Spatie/Filament akan ditangani dengan pin versi dan uji saat fondasi dibangun.

## D-0002 — Horizon dan Redis ditambahkan ke Docker/stack
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: Horizon membutuhkan Redis; agent.md 3.2 menetapkan Redis bagian stack.
Keputusan: Sediakan layanan Redis di Docker Compose (dev & prod) dan konfigurasikan Horizon dengan antrean terpisah (`critical`, `default`, `notifications`, `reports`).
Alasan: Persyaratan eksplisit Horizon dan keandalan antrean. Konsekuensi: satu layanan infrastruktur tambahan, dipantau dari layar kesehatan sistem.

## D-0003 — Driver notifikasi: kontrak dengan Cloud API + gateway WhatsApp pihak ketiga, Resend + SMTP
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: README 9 mensyaratkan WhatsApp resmi dan gateway pihak ketiga (mis. Fonnte), serta email via Resend dan SMTP. Stack inti menunjuk Cloud API dan Resend sebagai utama.
Keputusan: Bangun kontrak `MessageChannel`/driver per kategori. Utama: WhatsApp Cloud API dan Resend. Tambahan wajib: gateway WhatsApp pihak ketiga (generik, contoh Fonnte) dan SMTP. Tanpa kredensial nyata, sediakan driver palsu untuk uji.
Alasan: Spesifikasi produk menang atas tabel stack inti; kontrak menjaga modul bisnis bebas dari penyedia.

## D-0004 — Multi-cabang: satu DB, Branch sebagai tenant Spatie Multitenancy + scope `branch_id`
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: Data bersama (pelanggan, hewan, produk, template, peran, integrasi) vs terpisah per cabang (stok, kasir, shift, jadwal, laporan) dengan mode gabungan pemilik (agent.md 6.1).
Keputusan: Satu basis data PostgreSQL. Model `Branch` sebagai tenant Spatie Multitenancy; tabel bercakupan cabang memakai `branch_id` dan global scope/trait `BelongsToBranch`; tabel global tanpa scope. Mode gabungan melalui `BranchScope::withAllBranches()` yang eksplisit dan memeriksa hak akses. Job, jadwal, webhook tenant-aware.
Alasan: Sesuai arahan 6.1 dan kebutuhan data bersama lintas cabang. Jika tidak cocok setelah investigasi, fallback ke custom scope tetap dengan paket konteks (akan dicatat sebagai keputusan baru).

## D-0005 — Autentikasi: Fortify untuk staf (password/reset/2FA) + guard `customer` OTP WhatsApp + Sanctum
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: Fortify ditetapkan; aplikasi pelanggan memakai login nomor HP + OTP WhatsApp, guard terpisah, Livewire PWA; Sanctum untuk token API.
Keputusan: Staf melalui Fortify terintegrasi dengan login Filament, 2FA TOTP dapat diwajibkan per peran. Pelanggan memakai guard `customer` dengan alur OTP WhatsApp (rate-limited). Sanctum dipakai untuk token API dan kebutuhan sesi yang relevan.
Alasan: Memenuhi 3.2 dan keamanan (rate limit, 2FA). OTP pelanggan bergantung driver WhatsApp (palsu saat dev/tanpa kredensial).

## D-0006 — POS sebagai halaman Livewire khusus (ramah sentuh + pintasan keyboard)
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: agent.md 3.2 menyatakan POS bukan sekadar CRUD Filament.
Keputusan: Implementasi POS sebagai halaman Livewire khusus dengan komponen interaktif, layout sentuh, dan pintasan keyboard; tetap berada dalam ekosistem panel/aplikasi yang sama dan tunduk pada Policy/cakupan cabang.
Alasan: Kebutuhan kecepatan kasir, shift, split payment, dan interaksi real-time tidak cocok dengan CRUD generik.

## D-0007 — Storage media: R2 utama (S3-compatible) dengan opsi disk lokal, privat via signed URL
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: R2 ditetapkan dan data harus milik pemilik serta mudah dipasang.
Keputusan: Spatie Media Library mendukung disk R2 dan lokal (dapat dipilih). Berkas privat selalu diakses lewat URL bertanda tangan sementara; unggahan divalidasi (jenis, ukuran, MIME nyata) dan diproses (thumbnail) lewat antrean.
Alasan: Memenuhi 3.2/6.13 dan portabilitas self-host.

## D-0008 — Format & analisis statis: Pint + Larastan; `composer verify` sebagai gerbang tunggal
Tanggal: 2026-11-12   Fase: 0   Status: diterima
Konteks: Definisi selesai mensyaratkan format/analisis lulus dan satu pintu verifikasi.
Keputusan: `composer verify` menjalankan Pint (cek), Larastan, dan Pest. Level Larastan dimulai setinggi yang realistis untuk proyek baru dan dinaikkan bertahap (dicatat). CI menjalankan perintah sama dengan PostgreSQL & Redis.
Alasan: Kualitas dan konsistensi sejak Fase 0.
