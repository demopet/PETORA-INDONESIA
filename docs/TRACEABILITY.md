# TRACEABILITY — PETORA INDONESIA

Matriks fitur README → modul → status → uji. Status: belum | berjalan | selesai.

| Bagian | Fitur/Butir | Modul (rencana) | Status | Uji |
|---|---|---|---|---|
| 5 Struktur Bisnis: Pemilik dan Cabang | Identitas cabang (nama, kode, alamat, kontak, jam buka) | Branches/Platform | berjalan | tests/Feature/PlatformFoundationTest.php |
| 5 Struktur Bisnis: Pemilik dan Cabang | Gudang dan stok per cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Kasir dan shift per cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Penugasan dokter/groomer/staf per cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Ruang rawat inap, kandang, kamar penitipan per cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Header dan footer struk per cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Data bersama: pelanggan & hewan lintas cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Data bersama: daftar produk & layanan (harga boleh beda per cabang) | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Data bersama: template pesan | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Data bersama: peran & hak akses | Identity/Platform | berjalan | tests/Feature/PlatformFoundationTest.php |
| 5 Struktur Bisnis: Pemilik dan Cabang | Data bersama: integrasi | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Riwayat hewan ikut lintas cabang; pengaturan visibilitas rekam medis lintas cabang | - | belum | - |
| 5 Struktur Bisnis: Pemilik dan Cabang | Transfer stok antar cabang: diminta, dikirim, diterima, selisih tercatat | - | belum | - |
| 6 Peran dan Hak Akses | Peran buatan sendiri, hak akses rinci per modul/aksi | Identity | berjalan | tests/Feature/PlatformFoundationTest.php |
| 6 Peran dan Hak Akses | Klon dan sesuaikan peran | Identity | berjalan | app/Filament/Resources/RoleResource/Pages/ListRoles.php |
| 6 Peran dan Hak Akses | Satu orang banyak peran di cabang berbeda | Identity | berjalan | app/Support/BranchRoleSync.php, tests/Feature/BranchRoleSyncTest.php |
| 6 Peran dan Hak Akses | Cakupan data: semua cabang / cabang tertentu / milik sendiri | Identity/Platform | berjalan | app/Support/BranchContext.php, tests/Feature/BranchContextAndOwnershipTest.php |
| 6 Peran dan Hak Akses | Batasan angka (batas diskon/refund per peran) | - | belum | - |
| 6 Peran dan Hak Akses | Persetujuan atasan (PIN supervisor/persetujuan tercatat) | - | belum | - |
| 6 Peran dan Hak Akses | Penyembunyian data sensitif per peran (harga beli/margin, komisi orang lain, catatan internal dokter, kontak) | - | belum | - |
| 6 Peran dan Hak Akses | Pengecualian per orang dengan alasan tercatat | - | belum | - |
| 6 Peran dan Hak Akses | Matriks hak akses, pencarian, peringatan berisiko tinggi, "lihat sebagai peran ini" | - | belum | - |
| 6 Peran dan Hak Akses | Perlindungan Owner (tidak dihapus, minimal satu Owner aktif) | Identity | berjalan | app/Support/OwnerProtection.php, tests/Feature/BranchContextAndOwnershipTest.php |
| 6 Peran dan Hak Akses | Peran bawaan: Owner, Super Admin, Manajer Cabang, Kasir, Resepsionis, Dokter Hewan, Paramedis, Groomer, Petugas Penitipan, Admin Gudang, Keuangan | Identity | berjalan | tests/Feature/PlatformFoundationTest.php |
| 7.1 Dashboard | Ringkasan hari ini: penjualan, kunjungan, antrian, janji temu, stok menipis, obat kedaluwarsa, tagihan jatuh tempo, pembayaran online menunggu | - | belum | - |
| 7.1 Dashboard | Tampilan per cabang atau gabungan | - | belum | - |
| 7.1 Dashboard | Widget dapat disusun tiap pengguna | - | belum | - |
| 7.1 Dashboard | Isi menyesuaikan hak akses | - | belum | - |
| 7.2 Kasir (POS) | Shift: buka/tutup, kas awal, kas masuk/keluar, rekap per metode, hitung selisih, laporan shift | - | belum | - |
| 7.2 Kasir (POS) | Penjualan: cari nama/kode/scan barcode; varian; satuan ganda; jumlah desimal | - | belum | - |
| 7.2 Kasir (POS) | Catatan per item; campuran produk/obat/layanan klinik/grooming/penitipan/paket dalam satu keranjang | - | belum | - |
| 7.2 Kasir (POS) | Pilih pelanggan & hewan; kaitkan item dengan hewan tertentu | - | belum | - |
| 7.2 Kasir (POS) | Harga bertingkat, per cabang, member, promo berjadwal | - | belum | - |
| 7.2 Kasir (POS) | Diskon per item/transaksi, voucher, promo otomatis, diskon member; batas peran & persetujuan atasan | - | belum | - |
| 7.2 Kasir (POS) | Pajak sesuai pengaturan (termasuk/belum), biaya layanan opsional, pembulatan | - | belum | - |
| 7.2 Kasir (POS) | Pembayaran: tunai (kembalian), transfer manual, kartu/EDC, QRIS, e-wallet, VA; split payment | - | belum | - |
| 7.2 Kasir (POS) | Uang muka/deposit, saldo pelanggan, piutang, cicilan berjatuh tempo | - | belum | - |
| 7.2 Kasir (POS) | Pembayaran online QR di layar atau link bayar WA; status berubah otomatis saat dibayar | - | belum | - |
| 7.2 Kasir (POS) | Tahan dan lanjutkan transaksi; daftar "menunggu pembayaran" | - | belum | - |
| 7.2 Kasir (POS) | Void/refund/retur (sebagian/penuh) dengan alasan, persetujuan, penyesuaian stok & komisi | - | belum | - |
| 7.2 Kasir (POS) | Poin loyalitas dan voucher/kartu hadiah (opsional) | - | belum | - |
| 7.2 Kasir (POS) | Pintasan keyboard lengkap | - | belum | - |
| 7.2 Kasir (POS) | Struk 58mm/80mm/A4 (cetak browser/PDF); invoice & tanda terima; cetak ulang "SALINAN" | - | belum | - |
| 7.2 Kasir (POS) | Pengiriman struk & tagihan otomatis ke WhatsApp (email tambahan) | - | belum | - |
| 7.3 Pelanggan dan Hewan | Pelanggan: kontak format nomor Indonesia, alamat, tag, catatan, preferensi notifikasi, persetujuan | - | belum | - |
| 7.3 Pelanggan dan Hewan | Riwayat transaksi, saldo/piutang, status member | - | belum | - |
| 7.3 Pelanggan dan Hewan | Hewan: jenis, ras, nama, kelamin, tgl lahir/umur, warna, microchip, foto, sterilisasi, alergi, riwayat berat, catatan perilaku, status | - | belum | - |
| 7.3 Pelanggan dan Hewan | Satu hewan dimiliki lebih dari satu orang | - | belum | - |
| 7.3 Pelanggan dan Hewan | Pencarian cepat (nama, telepon, microchip) | - | belum | - |
| 7.3 Pelanggan dan Hewan | Deteksi dan penggabungan data ganda | - | belum | - |
| 7.3 Pelanggan dan Hewan | Impor dan ekspor pelanggan/hewan | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Produk: SKU, banyak barcode, kategori bertingkat, merek, foto, varian, satuan & konversi, jenis barang | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Penandaan obat (golongan, aturan resep) | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Harga: beli, jual, bertingkat, per cabang; HPP rata-rata atau FIFO | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Stok: banyak gudang per cabang; batch dengan kedaluwarsa (FEFO); kartu stok | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Stok opname (sesi hitung, selisih, persetujuan) | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Penyesuaian stok dengan alasan | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Peringatan stok minimum & saran pemesanan ulang | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Laporan kedaluwarsa & barang lambat laku; nilai persediaan | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Supplier, permintaan pembelian, PO, penerimaan (cek batch/kedaluwarsa), retur ke supplier, hutang supplier | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Transfer antar gudang dan cabang | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Bundel produk | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Resep komposisi (obat racikan, bahan berkurang otomatis) | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Cetak label dan barcode | - | belum | - |
| 7.4 Produk, Stok, dan Pengadaan | Stok berkurang otomatis dari penjualan kasir, resep dokter, pemakaian bahan layanan | - | belum | - |
| 7.5 Klinik Hewan | Pendaftaran kunjungan, nomor antrian, prioritas/triase, status antrian real-time, penugasan dokter/paramedis | - | belum | - |
| 7.5 Klinik Hewan | Pemeriksaan: tanda vital, berat, BCS, suhu; keluhan/anamnesis; SOAP; diagnosa/banding/prognosis | - | belum | - |
| 7.5 Klinik Hewan | Tindakan terhubung biaya; pemakaian bahan; lampiran foto/dokumen/hasil penunjang | - | belum | - |
| 7.5 Klinik Hewan | Rencana tindak lanjut dan rujukan | - | belum | - |
| 7.5 Klinik Hewan | Resep & penyerahan obat: dosis/aturan pakai, stok berkurang otomatis, cetak label obat | - | belum | - |
| 7.5 Klinik Hewan | Formulir rekam medis dibangun pemilik (skema berversi), template per layanan, isian wajib/bersyarat, visibilitas ke pelanggan | - | belum | - |
| 7.5 Klinik Hewan | Vaksinasi & pencegahan: protokol, batch vaksin, sertifikat cetak, pengingat jadwal otomatis | - | belum | - |
| 7.5 Klinik Hewan | Laboratorium: permintaan, hasil (satuan, rentang normal, penanda abnormal), unggah file, grafik tren | - | belum | - |
| 7.5 Klinik Hewan | Rawat inap: kandang/ruang, masuk-pulang, observasi berkala, jadwal obat/infus checklist, pakan, biaya harian otomatis | - | belum | - |
| 7.5 Klinik Hewan | Bedah & anestesi: persetujuan tanda tangan digital, pra-bedah, anestesi, laporan operasi, pemantauan pasca | - | belum | - |
| 7.5 Klinik Hewan | Dokumen: surat sehat, keterangan, rujukan, ringkasan medis, persetujuan (template dapat diedit) | - | belum | - |
| 7.5 Klinik Hewan | Keamanan rekam medis: draf→kunci, koreksi via addendum berversi, log akses/perubahan | - | belum | - |
| 7.6 Grooming | Daftar layanan/paket berdasarkan ukuran/ras/berat (durasi, bahan) | - | belum | - |
| 7.6 Grooming | Jadwal groomer | - | belum | - |
| 7.6 Grooming | Status: terjadwal→check-in→diproses→selesai→dijemput | - | belum | - |
| 7.6 Grooming | Foto sebelum dan sesudah | - | belum | - |
| 7.6 Grooming | Catatan perilaku dan kondisi kulit | - | belum | - |
| 7.6 Grooming | Layanan tambahan (add-on) | - | belum | - |
| 7.6 Grooming | Pesan WhatsApp otomatis saat siap dijemput | - | belum | - |
| 7.7 Penitipan Hewan | Kamar/kandang: tipe, kapasitas, tarif per hari/jam | - | belum | - |
| 7.7 Penitipan Hewan | Kalender ketersediaan dan reservasi | - | belum | - |
| 7.7 Penitipan Hewan | Check-in/out dengan catatan kondisi | - | belum | - |
| 7.7 Penitipan Hewan | Log harian (makan, minum, buang air, aktivitas, obat) | - | belum | - |
| 7.7 Penitipan Hewan | Kirim foto dan kabar untuk pemilik | - | belum | - |
| 7.7 Penitipan Hewan | Deposit | - | belum | - |
| 7.7 Penitipan Hewan | Biaya per malam dan layanan tambahan masuk otomatis ke tagihan | - | belum | - |
| 7.7 Penitipan Hewan | Aturan vaksin wajib sebelum menitipkan | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Kalender per dokter, groomer, kamar, dan sumber daya lain | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Jam kerja, hari libur, cuti | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Slot dengan durasi dan jeda antar layanan | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Booking oleh staf maupun pelanggan lewat aplikasi | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Konfirmasi otomatis atau manual (per layanan) | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Deposit booking opsional via pembayaran online | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Aturan jadwal ulang dan pembatalan | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Pencatatan no-show | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Daftar tunggu | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Pengingat otomatis | - | belum | - |
| 7.8 Janji Temu dan Penjadwalan | Pencegahan jadwal bentrok (termasuk bersamaan) | - | belum | - |
| 7.9 Keuangan | Kas & rekening bank, akun penerimaan per metode bayar | - | belum | - |
| 7.9 Keuangan | Biaya operasional dengan kategori dan lampiran bukti | - | belum | - |
| 7.9 Keuangan | Pembelian, hutang supplier, piutang pelanggan, pelunasan | - | belum | - |
| 7.9 Keuangan | Jurnal otomatis dari penjualan, pembelian, biaya, refund | - | belum | - |
| 7.9 Keuangan | Laporan laba rugi, arus kas, neraca sederhana | - | belum | - |
| 7.9 Keuangan | Penutupan periode | - | belum | - |
| 7.9 Keuangan | Rekap pajak | - | belum | - |
| 7.9 Keuangan | Pencatatan biaya payment gateway dan rekonsiliasi pembayaran online | - | belum | - |
| 7.10 Komisi (opsional) | Dapat dinyalakan/dimatikan; saat mati tidak muncul & tidak dihitung | - | belum | - |
| 7.10 Komisi (opsional) | Aturan fleksibel (staf/peran/kategori/item, persen/nominal, omzet/laba, bertingkat, pembagian, batas min/maks, periode) | - | belum | - |
| 7.10 Komisi (opsional) | Siklus: menunggu→final→dibalik otomatis | - | belum | - |
| 7.10 Komisi (opsional) | Hanya laporan per staf/periode/cabang/kategori dengan rincian & ekspor (tanpa pembayaran gaji) | - | belum | - |
| 7.10 Komisi (opsional) | Staf hanya melihat komisinya sendiri kecuali berhak lebih | - | belum | - |
| 7.11 SDM Dasar | Profil staf, penugasan cabang | - | belum | - |
| 7.11 SDM Dasar | Jadwal shift, absensi sederhana | - | belum | - |
| 7.11 SDM Dasar | Cuti dasar | - | belum | - |
| 7.11 SDM Dasar | Pengingat masa berlaku STR dan sertifikasi | - | belum | - |
| 7.12 Komunikasi dan Notifikasi | Pesan otomatis: struk/tagihan terbit; pembayaran berhasil/kedaluwarsa; booking dibuat/dikonfirmasi/diingatkan/dibatalkan; pengingat vaksin; siap dijemput; hasil lab; ulang tahun hewan; tindak lanjut; stok menipis (internal); OTP login pelanggan | - | belum | - |
| 7.12 Komunikasi dan Notifikasi | Kanal: WhatsApp, email, notifikasi dalam aplikasi | - | belum | - |
| 7.12 Komunikasi dan Notifikasi | Template dengan variabel & pratinjau | - | belum | - |
| 7.12 Komunikasi dan Notifikasi | Jam tenang, penanganan berhenti berlangganan | - | belum | - |
| 7.12 Komunikasi dan Notifikasi | Log lengkap (terkirim, diterima, dibaca) dan percobaan ulang otomatis | - | belum | - |
| 7.13 Laporan | Laporan penjualan (harian, per kasir, per produk/kategori, per metode bayar, per cabang) | - | belum | - |
| 7.13 Laporan | Laba dan margin | - | belum | - |
| 7.13 Laporan | Stok (persediaan, nilai, kedaluwarsa, lambat laku) | - | belum | - |
| 7.13 Laporan | Pembelian; klinik (kunjungan, diagnosa, tindakan, kinerja dokter); layanan (grooming, penitipan, vaksinasi) | - | belum | - |
| 7.13 Laporan | Piutang dan hutang; komisi; keuangan; audit | - | belum | - |
| 7.13 Laporan | Filter fleksibel, tampilan tersimpan, ekspor CSV/Excel/PDF, pengiriman terjadwal ke email/WA | - | belum | - |
| 7.13 Laporan | Per cabang atau gabungan | - | belum | - |
| 8 Aplikasi untuk Pelanggan | PWA dapat dipasang di HP, mobile-first, branding bisnis | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Login nomor HP + OTP WhatsApp (tanpa kata sandi); pendaftaran mandiri dapat diatur | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Melihat/memperbarui profil dan data hewan | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Riwayat kunjungan, jadwal vaksin & tanggal berikutnya | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Dokumen yang diizinkan (sertifikat vaksin, resep, ringkasan) — tanpa catatan internal dokter | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Lihat tagihan dan bayar langsung (QRIS, e-wallet, VA, m-banking); riwayat pembayaran & struk | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Booking klinik/grooming/penitipan (pilih cabang, dokter/groomer, tanggal, jam) dengan deposit bila diwajibkan | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Ubah/batalkan booking sesuai kebijakan | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Pantau status grooming & penitipan dengan foto dan kabar harian | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Notifikasi dan preferensi | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Satu akun untuk semua cabang; pilih cabang saat booking | - | belum | - |
| 8 Aplikasi untuk Pelanggan | Privasi: hanya data milik sendiri | - | belum | - |
| 9 Integrasi Pihak Ketiga | Layar Pengaturan Integrasi (Owner/Super Admin saja), tanpa kredensial tertanam | - | belum | - |
| 9 Integrasi Pihak Ketiga | Driver pembayaran Midtrans & Xendit (QRIS, e-wallet, VA), sandbox & produksi, Uji Koneksi | - | belum | - |
| 9 Integrasi Pihak Ketiga | Driver WhatsApp Cloud API resmi & gateway pihak ketiga | - | belum | - |
| 9 Integrasi Pihak Ketiga | Driver Email Resend & SMTP | - | belum | - |
| 9 Integrasi Pihak Ketiga | Formulir pengisian per penyedia, webhook URL otomatis, status kesehatan | - | belum | - |
| 9 Integrasi Pihak Ketiga | Kredensial terenkripsi, tidak ditampilkan ulang, perubahan diaudit | - | belum | - |
| 9 Integrasi Pihak Ketiga | Koneksi utama dan aturan pengalihan (routing/fallback) | - | belum | - |
| 9 Integrasi Pihak Ketiga | Penyedia baru dapat ditambah via kontrak tanpa ubah modul bisnis | - | belum | - |
| 9 Integrasi Pihak Ketiga | Pengelola template WA resmi (buat, kirim persetujuan, sinkron status, petakan variabel) dan penjelasan saat tak bisa kirim | - | belum | - |
| 10 Pengaturan Super Admin | Profil bisnis dan branding (nama, logo, ikon, warna aksen, tema) | - | belum | - |
| 10 Pengaturan Super Admin | Cabang dan gudang | - | belum | - |
| 10 Pengaturan Super Admin | Penomoran dokumen per jenis & cabang | - | belum | - |
| 10 Pengaturan Super Admin | Pajak (tarif, cara hitung, objek) | - | belum | - |
| 10 Pengaturan Super Admin | Lokalisasi (ID/EN, zona waktu, format); mata uang hanya Rupiah | - | belum | - |
| 10 Pengaturan Super Admin | Template dokumen (struk, invoice, sertifikat, surat) dengan editor & pratinjau | - | belum | - |
| 10 Pengaturan Super Admin | Template pesan dan aturan notifikasi | - | belum | - |
| 10 Pengaturan Super Admin | Formulir rekam medis (builder) | - | belum | - |
| 10 Pengaturan Super Admin | Kebijakan: persetujuan, batas diskon, refund, retensi data, booking, deposit | - | belum | - |
| 10 Pengaturan Super Admin | Fitur per modul (nyalakan/matikan) | - | belum | - |
| 10 Pengaturan Super Admin | Peran, pengguna, sesi aktif, 2FA | Identity/Platform | berjalan | app/Support/MultiFactorPolicy.php, app/Filament/Pages/ManageSettings.php, tests/Feature/MultiFactorPolicyTest.php |
| 10 Pengaturan Super Admin | Log audit, kesehatan sistem | - | belum | - |
| 10 Pengaturan Super Admin | Impor/ekspor data, pengelolaan cadangan | - | belum | - |
| 11 Branding dan Lisensi | Ganti nama, logo, warna aplikasi | - | belum | - |
| 11 Branding dan Lisensi | Atribusi "Powered by PETORA INDONESIA" & hak cipta tampil di app staf, app pelanggan, dokumen cetak; tanpa opsi sembunyi | Platform | berjalan | tests/Feature/BrandingAttributionTest.php, tests/Feature/AdminAccessTest.php |
| 11 Branding dan Lisensi | Tanpa kunci lisensi/aktivasi online | - | belum | - |
| 11 Branding dan Lisensi | Kode sumber disediakan kepada pelanggan | - | belum | - |
| 12 Keamanan dan Privasi | Self-hosted, data milik pemilik | - | belum | - |
| 12 Keamanan dan Privasi | Akses terkontrol per hak akses & cakupan cabang | - | belum | - |
| 12 Keamanan dan Privasi | Kata sandi/sesi aman, 2FA (dapat diwajibkan), daftar perangkat aktif dapat dicabut | - | belum | - |
| 12 Keamanan dan Privasi | PIN supervisor untuk aksi sensitif | - | belum | - |
| 12 Keamanan dan Privasi | Kredensial terenkripsi, hanya Owner/Super Admin | - | belum | - |
| 12 Keamanan dan Privasi | Rate limit login/OTP, perlindungan app pelanggan | - | belum | - |
| 12 Keamanan dan Privasi | Dokumen & transaksi tidak diubah diam-diam (void/refund/retur dengan jejak) | - | belum | - |
| 12 Keamanan dan Privasi | Rekam medis terkunci & berversi; akses tercatat | - | belum | - |
| 12 Keamanan dan Privasi | Log audit aksi penting | - | belum | - |
| 12 Keamanan dan Privasi | Perlindungan data pribadi: retensi, anonimisasi, ekspor, persetujuan (prinsip UU PDP) | - | belum | - |
| 12 Keamanan dan Privasi | Unggahan diperiksa jenis/ukuran dan akses terbatas | - | belum | - |
| 13 Pengalaman Pengguna | Tampilan hangat/profesional, warna aksen dapat disesuaikan | - | belum | - |
| 13 Pengalaman Pengguna | Mode terang/gelap/otomatis, kepadatan nyaman/ringkas | - | belum | - |
| 13 Pengalaman Pengguna | Pencarian & palet perintah (Ctrl/Cmd+K), pencarian global | - | belum | - |
| 13 Pengalaman Pengguna | Pindah cabang satu klik | - | belum | - |
| 13 Pengalaman Pengguna | Animasi halus, tabel fleksibel & tampilan tersimpan, dashboard dapat disusun | - | belum | - |
| 13 Pengalaman Pengguna | Kasir ramah sentuh & pintasan keyboard | - | belum | - |
| 13 Pengalaman Pengguna | Responsif (ponsel/tablet/komputer), aksesibilitas dasar | - | belum | - |
| 13 Pengalaman Pengguna | Layar kosong & pesan kesalahan ramah | - | belum | - |
| 14 Pemasangan dan Pemeliharaan | Pemasangan di server sendiri semudah mungkin (satu perintah) | - | belum | - |
| 14 Pemasangan dan Pemeliharaan | Wizard pengaturan awal (Owner, profil bisnis, cabang, zona waktu, bahasa, pajak) tertutup permanen setelah selesai | Platform | berjalan | app/Filament/Pages/SetupWizard.php |
| 14 Pemasangan dan Pemeliharaan | Wizard memeriksa app & DB; pemeriksaan alamat publik via PUBLIC_APP_URL HTTPS (pesan jujur, bukan uji webhook penyedia) | - | belum | - |
| 14 Pemasangan dan Pemeliharaan | Data contoh dapat dimuat dan dihapus bersih | - | belum | - |
| 14 Pemasangan dan Pemeliharaan | Pembaruan aman dengan cadangan & rollback | - | belum | - |
| 14 Pemasangan dan Pemeliharaan | Cadangan terjadwal dan dapat dipulihkan, terdokumentasi | - | belum | - |
| 14 Pemasangan dan Pemeliharaan | Panduan untuk setiap peran | - | belum | - |
