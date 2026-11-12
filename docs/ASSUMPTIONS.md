# ASSUMPTIONS — PETORA INDONESIA

Asumsi yang diambil saat spesifikasi bercelah. Format: A-xxxx.

## A-0001 — Teks LICENSE adalah draf operasional, perlu tinjauan hukum
Celah: agent.md 10.3 meminta LICENSE dengan ketentuan tertentu namun menyatakan teks perlu ditinjau ahli hukum.
Asumsi: Draf LICENSE saat ini memenuhi ketentuan operasional yang diminta; distribusi komersial final memerlukan peninjauan ahli hukum oleh pemilik/PETORA.
Alasan: Tidak menghentikan pekerjaan dan tetap mencatat kewajiban atribusi. Terkait: agent.md 10.3, 14.

## A-0002 — Versi paket dikunci ke set stabil terbaru yang lulus verifikasi kompatibilitas
Celah: Versi spesifik Laravel/Filament/Spatie tidak ditentukan angka.
Asumsi: Laravel 13 + Filament 5 + Livewire 4 + Tailwind 4 + Pest 4 pada PHP 8.4, dengan paket Spatie stabil terbaru yang saling kompatibel (D-0001).
Alasan: Lingkungan menyediakan PHP 8.4; dipilih set stabil terbaru. Terkait: agent.md 3.2.

## A-0003 — Identifikasi cabang aktif: sesi web + konteks tenant yang eksplisit
Celah: Mekanisme teknis "pindah cabang satu klik" tidak dirinci.
Asumsi: Cabang aktif disimpan di sesi pengguna; semua query bercakupan cabang dan tindakan tulis terikat cabang aktif (atau cabang yang dipilih eksplisit) kecuali mode gabungan yang sah. Perpindahan memvalidasi hak akses.
Alasan: Perilaku paling umum dan aman untuk multi-cabang berbagi data induk. Terkait: README 5, agent.md 6.1.

## A-0004 — Konversi nama app dari identitas bisnis untuk slug/teknis
Celah: "Nama aplikasi dapat diganti" (branding) vs kebutuhan identifier teknis.
Asumsi: Branding (nama tampilan, logo, warna) dapat diubah bebas; identifier teknis internal, skema, dan atribusi wajib tidak terpengaruh.
Alasan: Menghindari kerusakan sistem sambil memenuhi branding. Terkait: README 11, 15.18.

## A-0005 — Mode offline tidak didukung; POS memerlukan koneksi
Celah: Tidak ada. Dicatat untuk kejelasan operasional.
Asumsi: Semua modul termasuk POS dan PWA pelanggan membutuhkan koneksi jaringan/server; tidak ada antrian offline lokal.
Alasan: README 17 menyatakan penggunaan tanpa internet di luar cakupan. Terkait: README 17.

## A-0006 — Kredensial penyedia eksternal tidak tersedia saat pengembangan
Celah: Butuh akun/kunci nyata Midtrans, Xendit, Meta WhatsApp, Cloudflare R2, Resend.
Asumsi: Implementasi memakai kontrak + driver palsu/sandbox dan uji HTTP fake; verifikasi tanda tangan dan idempotensi webhook diuji secara lokal. Pengujian ke penyedia nyata menunggu kredensial pemilik (didokumentasikan di docs/INTEGRATIONS.md).
Alasan: agent.md 12. Terkait: README 9, agent.md 6.6-6.8.
