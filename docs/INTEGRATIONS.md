# INTEGRATIONS — PETORA INDONESIA

Status Fase 0: belum ada kredensial nyata. Semua integrasi dibangun via kontrak + driver palsu/sandbox untuk uji lokal.

| Kategori | Driver utama | Driver tambahan | Status | Catatan |
|---|---|---|---|---|
| Pembayaran | Midtrans, Xendit | — | belum | QRIS, e-wallet, VA, link bayar; sandbox/prod; Uji Koneksi; webhook bertanda tangan & idempoten; rekonsiliasi. |
| WhatsApp | WhatsApp Cloud API (resmi) | Gateway pihak ketiga (mis. Fonnte) | belum | Template bisnis wajib disetujui Meta; pengelola template; OTP pelanggan; routing/fallback. |
| Email | Resend | SMTP | belum | Struk, laporan terjadwal, pemberitahuan. |
| Storage | Cloudflare R2 (S3) | Disk lokal | belum | Dapat dipilih; privat via signed URL. |

## Prinsip
- Layar Pengaturan Integrasi (Owner/Super Admin). Tanpa kredensial di kode/repo.
- Kredensial terenkripsi di DB, tidak ditampilkan ulang, perubahan diaudit.
- URL webhook otomatis ditampilkan; status kesehatan di dashboard.
- Uji kontrak memakai fake/HTTP fake; **pengujian langsung ke penyedia nyata menunggu kredensial pemilik** (sandbox). Tidak ada panggilan jaringan di test otomatis.
