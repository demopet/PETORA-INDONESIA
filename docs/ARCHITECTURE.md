# ARCHITECTURE — PETORA INDONESIA

Status: draf awal Fase 0 (akan diperbarui seiring implementasi). Lihat `agent.md` §5-6 sebagai panduan normatif.

## Ringkasan
- **Pola:** Modular Monolith pada Laravel 13.
- **UI:** Filament (panel admin/staf) + Livewire + Tailwind CSS; POS dan Portal Pelanggan sebagai permukaan Livewire khusus/PWA.
- **Data:** PostgreSQL tunggal, Eloquent, migrasi per modul.
- **Asinkron:** Laravel Queue + Horizon (Redis), antrean `critical`, `default`, `notifications`, `reports`.
- **Penjadwalan:** Laravel Scheduler.
- **Multi-cabang:** Branch sebagai tenant (Spatie Multitenancy) dengan isolasi kolom `branch_id` (lihat D-0004).

## Struktur modul (rencana)
Namespace `Modules\<Nama>` (autoload PSR-4 `modules/`).
- `Platform` — pengaturan, penomoran, branding, wizard, audit, kesehatan.
- `Identity` — pengguna, Fortify/2FA, peran, hak akses, batas, persetujuan, PIN.
- `Branches` — cabang, gudang, ruang, kandang, kamar.
- `Customers` — pelanggan, hewan, kepemilikan, penggabungan duplikat.
- `Catalog` — produk, varian, satuan, layanan, paket, harga, pajak.
- `Inventory` — stok, batch, FEFO, opname, transfer, penyesuaian.
- `Procurement` — supplier, PR, PO, penerimaan, retur, hutang supplier.
- `Billing` — kunjungan, tagihan, pembayaran, shift, POS, refund, void, retur, loyalitas, voucher.
- `Clinic` — antrian, rekam medis, formulir berversi, resep, vaksin, lab, rawat inap, bedah, dokumen.
- `Grooming` — layanan, status, foto.
- `Boarding` — kamar, reservasi, log harian.
- `Scheduling` — kalender, slot, booking, daftar tunggu, no-show.
- `Finance` — kas, bank, biaya, jurnal, laporan keuangan, penutupan periode.
- `Commission` — aturan, perhitungan, laporan (dapat dimatikan).
- `HR` — profil staf, shift, absensi, cuti, pengingat STR.
- `Notifications` — template, kanal, aturan, log, pengiriman.
- `Integrations` — kontrak & driver pembayaran, WhatsApp, email; webhook.
- `Reporting` — laporan, tampilan tersimpan, ekspor, pengiriman terjadwal.
- `CustomerPortal` — PWA pelanggan (guard `customer`).

Tiap modul: `Actions/ Models/ Policies/ Enums/ DTOs/ Events/ Listeners/ Contracts/ Filament/ Livewire/ Database/{migrations,factories,seeders}/ Routes/ Resources/{views,lang}/ Tests/ README.md`.

## Batas modul
- Interaksi lintas modul via **Contract**, **Action publik**, **Event domain**. Tidak menulis tabel/model modul lain.
- Pembacaan model induk bersama (Branches, Customers, Catalog, Identity) diizinkan; penulisan tidak.
- Layanan (Clinic, Grooming, Boarding, Scheduling, Procurement) menerbitkan biaya ke Billing lewat kontrak `BillableItem` → satu Kunjungan/Tagihan.
- Modul opsional (Commission, loyalitas, rawat inap, dll.) tidak menjadi dependensi modul lain.
- Ditegakkan oleh arch test Pest.

## Klasifikasi cakupan data (awal, akan dirinci per tabel)
- **Global bersama:** pelanggan, hewan & kepemilikan, katalog produk/layanan & harga induk, template pesan, peran/definisi permission, integrasi & kredensial (terenkripsi), pengaturan global/branding.
- **Bercakupan cabang (`branch_id` + scope):** stok & gerakan, gudang/ruang/kandang, kasir & shift, jadwal/slot & booking operasional, transaksi/tagihan/pembayaran operasional, kunjungan/antrian, aktivitas layanan cabang, laporan operasional per cabang.
- **Mode gabungan:** pelepasan scope eksplisit dengan pemeriksaan hak akses (Owner/lintas cabang).

## Kontrak kunci (rencana)
- `BillableItem` (sumber biaya → Billing), `PaymentGateway` (Midtrans/Xendit), `MessageChannel` (WA Cloud API, gateway pihak ketiga, Resend, SMTP, in-app), generator penomoran transaksional, `BranchScope`.

## Uang & dokumen
- Uang: integer Rupiah (bigint) + value object `Money`. Kuantitas desimal presisi tetap.
- Penomoran dokumen per jenis/cabang, alokasi dalam transaksi + `SELECT FOR UPDATE`, tanpa ganda/lubang.

## Keamanan & jejak
- Policy pada setiap aksi, scope cabang, penyembunyian data sensitif server-side.
- Activity log & log akses rekam medis; kredensial terenkripsi; webhook bertanda tangan & idempoten.

## Kesehatan & operasional
- Endpoint health (`/.well-known/petora-health`), dashboard kesehatan (antrean, scheduler, storage, backup, integrasi), Horizon.
