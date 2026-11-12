# INSTALLATION — PETORA INDONESIA

Status: draf Fase 0 (alur produksi satu-perintah akan diselesaikan dan diuji di Fase 9-11).

## Prasyarat (target produksi)
- Server Linux dengan Docker & Docker Compose.
- Domain/HTTPS untuk akses publik (diperlukan untuk webhook penyedia & pemeriksaan alamat publik).

## Pemasangan (rencana satu perintah)
1. Salin repositori ke server.
2. Jalankan skrip pemasangan (mis. `./install.sh`) — akan membuat `.env` (APP_KEY, kredensial DB/Redis acak, APP_URL/PUBLIC_APP_URL), menyalakan tumpukan, migrasi, lalu membuka wizard pengaturan awal.
3. Selesaikan wizard: akun Owner, profil bisnis, cabang pertama, zona waktu, bahasa, pajak. (Tertutup permanen setelah selesai.)

## Catatan penting
- Cadangkan `APP_KEY`: data terenkripsi (kredensial integrasi, rahasia 2FA) tidak dapat dibuka tanpanya.
- Pemeriksaan alamat publik (jika `PUBLIC_APP_URL` HTTPS dikonfigurasi) memverifikasi endpoint health dari server aplikasi, **bukan** jangkauan dari jaringan penyedia webhook.
- Data contoh dapat dimuat untuk mencoba sistem dan dihapus bersih.

Detail langkah bernomor, perintah siap salin, dan pemecahan masalah akan dilengkapi saat skrip produksi siap.
