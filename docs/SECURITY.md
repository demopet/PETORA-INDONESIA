# SECURITY — PETORA INDONESIA

## Prinsip
- Otorisasi server-side pada setiap aksi (Policy) + cakupan cabang + penyembunyian data sensitif (termasuk ekspor/API).
- Kredensial/rahasia hanya di `.env`/secret manager, terenkripsi di DB, tidak pernah di Git/log/layar.
- 2FA (TOTP) dapat diwajibkan per peran; sesi aman; daftar perangkat aktif dapat dicabut.
- Rate limit login, OTP, endpoint publik. CSRF, validasi input, escaping output, header keamanan, proteksi SSRF & unggahan berbahaya, pembatasan CORS.
- Rekam medis terkunci berversi dengan log akses; transaksi final immutable (koreksi via void/refund/retur).
- Webhook pembayaran diverifikasi tanda tangan & diproses idempoten.
- Unggahan: validasi jenis/ukuran/MIME nyata, nama acak, akses via signed URL.
- Audit: login, perubahan peran/integrasi, diskon besar, void/refund, akses data medis, ekspor data.
- Dependensi diaudit (`composer audit`, `npm audit`) di CI.

## Data pribadi
- Mengacu prinsip UU PDP: retensi, anonimisasi, ekspor data pelanggan, persetujuan notifikasi.

## Pelaporan
- Gunakan kanal pelaporan yang ditetapkan pemilik/PETORA. Jangan mengekspos rahasia saat melapor.
