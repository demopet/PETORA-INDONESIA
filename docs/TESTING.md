# TESTING — PETORA INDONESIA

## Perkakas
- Pest PHP 4 untuk semua uji (unit, feature, arch). PostgreSQL sungguhan untuk basis data uji (bukan SQLite). Redis untuk skenario antrean bila relevan.
- Pint (format cek) dan Larastan (analisis statis).
- CI GitHub Actions menjalankan perintah yang sama dengan layanan PostgreSQL & Redis.

## Perintah satu pintu
`composer verify` → Pint (cek) + Larastan + Pest. (Akan dikonfigurasi saat inisialisasi proyek.)

## Lapisan uji
- **Unit:** Money, penomoran, pajak, harga, komisi, FEFO.
- **Fitur:** alur HTTP/Livewire/Filament, termasuk penolakan akses.
- **Arch:** batas modul, tidak ada `dd`, trait branch scope, Policy terdaftar.
- **Konkurensi:** penomoran, pengeluaran stok, webhook ganda, booking bentrok, pembayaran ganda.
- **Isolasi & privasi:** matriks peran/aksi, cabang, penyembunyian data sensitif, pelanggan hanya data sendiri.
- **E2E skenario:** README §16.
- **Browser (opsional):** POS dan portal pelanggan.

## Disiplin
- Tulis uji bersama fitur. Uji gagal diperbaiki akarnya, tidak dilemahkan.
- Layanan eksternal selalu dipalsukan (fake/HTTP fake), tanpa jaringan.
- Aturan bisnis kunci 1-18 dipetakan di `docs/BUSINESS_RULES.md` dengan uji bernama.
