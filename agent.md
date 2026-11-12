# agent.md — Aturan Kerja Agent untuk PETORA INDONESIA

Dokumen ini adalah konstitusi kerja agent. Agent membangun PETORA INDONESIA dari repositori kosong sampai produk siap pakai di bisnis nyata, secara mandiri, penuh, dan lengkap. Dokumen ini menjelaskan **bagaimana agent bekerja**. Dokumen `README.md` menjelaskan **apa yang dibangun**.

---

## Daftar Isi

1. Mandat dan Kewenangan
2. Sumber Kebenaran dan Urutan Membaca
3. Stack Teknologi (Ditetapkan Pengguna)
4. Standar Kualitas dan Definisi Selesai
5. Arsitektur: Modular Monolith
6. Panduan Teknis per Area
7. Fase Pengerjaan
8. Pengujian
9. Deployment dan Operasional
10. Git, GitHub, dan Dokumentasi
11. Protokol Sesi: Memulai, Melanjutkan, Mengakhiri
12. Menangani Ketidakpastian dan Hambatan
13. Larangan
14. Gerbang Rilis
15. Pelaporan kepada Pengguna

---

## 1. Mandat dan Kewenangan

### 1.1 Mandat

Agent diberi **kontrol penuh** atas seluruh keputusan produk, bisnis, teknis, desain, dan proses selama keputusan itu mendekatkan pada satu tujuan:

> PETORA INDONESIA yang lengkap, konsisten, mudah dipasang, mudah dipakai, menyenangkan, dapat dipercaya, dapat dirawat, dan siap dipakai di bisnis petshop dan klinik hewan nyata di Indonesia (lihat README bagian 18).

Agent **tidak perlu meminta izin** untuk:

- Struktur folder, penamaan, pola desain, skema basis data, desain API internal.
- Memilih pustaka pendukung tambahan (lihat 3.3).
- Mengisi celah spesifikasi dengan perilaku paling umum untuk petshop dan klinik hewan di Indonesia.
- Keputusan bisnis kecil sampai menengah: nilai bawaan, urutan layar, alur, istilah, template bawaan, peran bawaan, hak akses bawaan, data contoh.
- Memecah, mengurutkan ulang, atau menggabungkan pekerjaan.
- Merefaktor, membuang, atau menulis ulang kode sendiri yang terbukti kurang baik.

### 1.2 Cara memutuskan

1. Utamakan perilaku yang paling umum di petshop dan klinik hewan Indonesia.
2. Bila ragu antara dua perilaku, **jadikan pengaturan** yang bisa diubah pemilik, jangan dikunci mati (README prinsip 4). Pilih bawaan yang paling aman dan paling umum.
3. Catat setiap keputusan berarti di `docs/DECISIONS.md` dan setiap asumsi spesifikasi di `docs/ASSUMPTIONS.md` (format di 10.4).
4. Lanjut bekerja. Jangan berhenti menunggu persetujuan.

### 1.3 Batasan tetap (tidak boleh diubah agent)

Hal berikut ditetapkan pengguna dan README. Mengubahnya membutuhkan instruksi eksplisit dari pengguna:

- Stack inti di bagian 3.1.
- Self-hosted. Tidak ada layanan hosting, kunci lisensi, atau aktivasi online.
- Mata uang hanya Rupiah (IDR).
- Atribusi "Powered by PETORA INDONESIA" selalu tampil dan tidak ada pengaturan untuk menyembunyikannya.
- Seluruh kode sumber diserahkan kepada pelanggan.
- Di luar cakupan (README bagian 17): mode offline, driver printer thermal atau laci kas, pembayaran komisi atau gaji, hosting.
- Aturan bisnis kunci (README bagian 15) berlaku mutlak.

### 1.4 Kapan boleh bertanya kepada pengguna

Hanya bila **semua** syarat ini terpenuhi: (a) agent benar-benar tidak bisa melanjutkan tanpa jawaban, (b) pekerjaan lain tidak bisa dikerjakan lebih dulu, dan (c) jawabannya tidak bisa ditebak dengan wajar. Contoh yang sah: kredensial atau akun nyata (Midtrans, Xendit, Meta, R2, Resend) yang hanya bisa dibuat pemilik, pengesahan hukum atas teks lisensi, tindakan destruktif di luar repositori. Dalam kasus itu, kerjakan dulu semua yang bisa dikerjakan dengan driver palsu atau sandbox (lihat bagian 12), lalu laporkan ringkas.

---

## 2. Sumber Kebenaran dan Urutan Membaca

### 2.1 Hierarki

Jika dua sumber bertentangan, yang lebih atas menang. Konflik dicatat di `docs/DECISIONS.md`.

1. Instruksi terbaru dari pengguna (lalu perbarui dokumen terkait agar konsisten).
2. `docs/SPEC.md` (salinan spesifikasi lengkap PETORA).
3. `README.md`.
4. `agent.md` (dokumen ini) untuk cara kerja.
5. Keputusan agent di `docs/DECISIONS.md` dan `docs/ASSUMPTIONS.md`.

### 2.2 Urutan membaca di awal sesi

1. Baca `agent.md` (ini).
2. Baca `PROGRESS.md` bila ada, untuk tahu posisi terakhir.
3. Baca `README.md`. Bila `docs/SPEC.md` belum ada, salin spesifikasi produk (semua bagian README di luar bagian teknologi) ke `docs/SPEC.md`.
4. Baca `docs/DECISIONS.md` dan `docs/ASSUMPTIONS.md` bila ada.
5. Lanjut dari tugas terbuka berikutnya di `PROGRESS.md`.

### 2.3 README memuat bagian teknologi yang usang

README saat ini berisi bagian "Teknologi yang Digunakan", "Memulai Pengembangan", "Kunci autentikasi dua langkah", dan "Pemeriksaan alamat publik" yang menyebut Next.js, Drizzle, pg-boss, Vitest, dan `TOTP_ENCRYPTION_KEY`. Bagian itu **tidak berlaku**. Di Fase 0, agent harus menulis ulang bagian tersebut agar sesuai stack di bagian 3 dan hasil pemasangan sebenarnya. Konsep yang tetap dipertahankan: wizard memeriksa aplikasi dan basis data, pemeriksaan alamat publik lewat `PUBLIC_APP_URL` HTTPS, serta catatan bahwa pemeriksaan itu bukan bukti webhook penyedia dapat menjangkau server. Isi produk di bagian 1 sampai 20 tidak boleh dihapus.

---

## 3. Stack Teknologi (Ditetapkan Pengguna)

### 3.1 Stack inti

| Area | Pilihan |
|---|---|
| Backend | Laravel |
| Panel admin | Filament |
| Frontend | Livewire |
| UI | Tailwind CSS + komponen Filament |
| Basis data | PostgreSQL |
| ORM | Eloquent |
| Autentikasi | Laravel Fortify |
| RBAC | Spatie Laravel Permission |
| Multi-cabang | Spatie Laravel Multitenancy |
| Log aktivitas | Spatie Laravel Activitylog |
| Media | Spatie Media Library |
| Antrean | Laravel Queue + Horizon |
| Penjadwalan | Laravel Scheduler |
| Autentikasi API | Laravel Sanctum |
| Penyimpanan file | Cloudflare R2 |
| Pembayaran | Midtrans dan Xendit |
| WhatsApp | WhatsApp Cloud API (resmi) |
| Email | Laravel Mail + Resend |
| Pengujian | Pest PHP |
| Deployment | Docker (konfigurasi otomatis) |
| Version control | Git + GitHub |
| Arsitektur | Modular Monolith |
| Pengembangan | Dibantu AI Agent |
| Mata uang | IDR |

### 3.2 Penyesuaian yang sudah diputuskan (agent tidak perlu bertanya)

- **Versi:** pakai versi stabil terbaru Laravel, PHP, Filament, Livewire, dan Tailwind yang saling kompatibel. Verifikasi kompatibilitas semua paket Spatie sebelum mengunci versi, lalu catat di `docs/DECISIONS.md`. Kunci versi lewat `composer.lock` dan `package-lock.json`.
- **Horizon membutuhkan Redis.** Tambahkan Redis ke Docker Compose. Ini bagian dari stack, bukan penyimpangan.
- **README meminta WhatsApp resmi dan gateway pihak ketiga (misalnya Fonnte), serta email via Resend dan SMTP.** Spesifikasi menang. Bangun lewat kontrak (interface) per kategori. WhatsApp Cloud API dan Resend adalah driver utama. Gateway WhatsApp pihak ketiga dan SMTP adalah driver tambahan yang disyaratkan README bagian 9.
- **Cloudflare R2** (kompatibel S3) adalah disk media utama untuk produksi. Karena README menegaskan data milik pemilik dan pemasangan yang mudah, sediakan juga disk lokal sebagai pilihan lewat pengaturan, dan pastikan Media Library bekerja di keduanya. Berkas privat selalu diakses lewat URL bertanda tangan sementara.
- **Fortify** menangani kata sandi, reset, dan autentikasi dua langkah (TOTP) untuk staf. Agent memutuskan cara memadukannya dengan halaman login Filament. 2FA harus benar-benar berfungsi dan dapat diwajibkan per peran.
- **Aplikasi pelanggan** memakai guard terpisah (`customer`), login nomor HP + OTP WhatsApp, dibangun dengan Livewire + Tailwind sebagai PWA (manifest, ikon, dapat dipasang di layar HP, tanpa mode offline). **Sanctum** dipakai untuk token API yang dibutuhkan (misalnya integrasi atau keperluan sesi lain) dan agent boleh memperluas pemakaiannya bila memang berguna.
- **POS** dibangun sebagai halaman Livewire khusus (di dalam panel atau layout sendiri, keputusan agent), ramah sentuh dengan pintasan keyboard. Bukan sekadar resource CRUD Filament.

### 3.3 Pustaka tambahan

Agent bebas menambah pustaka pendukung (ekspor Excel dan PDF, pembuatan kode batang, tanda tangan digital, grafik, kalender, dan sebagainya) dengan syarat: aktif dirawat, berlisensi yang kompatibel dengan distribusi kode sumber ke pelanggan, tidak menambah ketergantungan layanan eksternal berbayar yang wajib, dan dicatat di `docs/DECISIONS.md`. Mengganti atau membuang item di tabel 3.1 membutuhkan instruksi pengguna.

---

## 4. Standar Kualitas dan Definisi Selesai

### 4.1 Prinsip

- **Lengkap, bukan sekadar mulai.** README bagian 18 mensyaratkan tidak ada menu mati atau fitur setengah jadi. Jika sebuah fitur ada di README, ia harus ada, berfungsi, teruji, dan terdokumentasi.
- **Irisan vertikal.** Selesaikan satu fitur ujung ke ujung (migrasi, model, aksi, kebijakan akses, UI, notifikasi bila ada, uji, seeder, dokumentasi) sebelum pindah. Hindari membangun semua tabel dulu lalu semua UI belakangan.
- **Konsisten.** Istilah mengikuti glosarium README. Satu pola untuk satu masalah. Tampilan, pesan, dan perilaku seragam di semua modul.
- **Jejak dan keamanan sejak awal,** bukan ditambal di akhir.

### 4.2 Definisi Selesai (setiap fitur)

Sebuah fitur selesai hanya bila semua poin ini benar:

1. Perilaku sesuai README dan aturan bisnis kunci, termasuk kasus tepi (kosong, ganda, bersamaan, gagal).
2. Hak akses ditegakkan di sisi server untuk setiap aksi, termasuk cakupan cabang dan penyembunyian data sensitif. UI yang menyembunyikan tombol saja tidak cukup.
3. Aksi penting tercatat di log audit.
4. Uji Pest tertulis dan lulus (unit, fitur, dan uji konkurensi bila relevan).
5. Analisis statis dan format lolos (`composer verify` hijau).
6. Layar kosong, pesan galat, dan status memuat bersifat ramah (README bagian 13). Teks tersedia dalam Bahasa Indonesia dan Inggris lewat berkas bahasa.
7. Responsif dan dapat dipakai dengan keyboard, kontras baik.
8. Tidak ada N+1 query, tabel besar berpaginasi, indeks basis data tepat.
9. Dokumentasi terkait diperbarui (`docs/`, panduan peran bila menyangkut peran itu, `CHANGELOG.md`).
10. `docs/TRACEABILITY.md` memperbarui status fitur tersebut.

### 4.3 Kode

- PHP strict types, tipe eksplisit, enum untuk status, Form Request atau aturan validasi untuk semua input, Policy untuk otorisasi.
- Logika bisnis di kelas Action atau Service bertarget tunggal, bukan di controller, komponen Livewire, atau closure Filament.
- Laravel Pint untuk format. Larastan (PHPStan) pada level tertinggi yang realistis, dinaikkan bertahap, dicatat.
- Tidak ada kode mati, kode dikomentari, `dd()`, `dump()`, atau TODO tertinggal di kode yang dirilis. Utang teknis yang disadari dicatat di `PROGRESS.md` dengan alasan.
- Aktifkan `Model::shouldBeStrict()` di lingkungan non-produksi (cegah lazy loading, atribut hilang, mass assignment liar).
- Kode dan identifier dalam bahasa Inggris. Teks UI lewat berkas bahasa (`id` dan `en`, bawaan `id`). Dokumentasi dalam Bahasa Indonesia, mengikuti README.

---

## 5. Arsitektur: Modular Monolith

Satu aplikasi Laravel, satu basis data, satu proses deploy, dengan modul yang batasnya tegas.

### 5.1 Struktur yang disarankan

Agent boleh menyesuaikan nama dan membaginya lebih halus, dengan catatan di `docs/ARCHITECTURE.md`.

```
app/                      kernel tipis (provider, dasar HTTP, utilitas bersama)
modules/                  namespace Modules\<Nama>, autoload psr-4
  Platform/               pengaturan, penomoran dokumen, branding, wizard, audit, kesehatan sistem
  Identity/               pengguna, autentikasi, peran, hak akses, batas angka, persetujuan, PIN supervisor
  Branches/               cabang, gudang, ruang, kandang, kamar
  Customers/              pelanggan, hewan, kepemilikan, penggabungan data ganda
  Catalog/                produk, layanan, paket, harga, satuan, pajak
  Inventory/              stok, batch, FEFO, opname, transfer, penyesuaian
  Procurement/            supplier, PR, PO, penerimaan, retur, hutang supplier
  Billing/                kunjungan, tagihan, pembayaran, shift, POS, refund, void, retur, loyalitas, voucher
  Clinic/                 antrian, rekam medis, formulir, resep, vaksin, lab, rawat inap, bedah, dokumen
  Grooming/               layanan, status, foto
  Boarding/               kamar, reservasi, log harian
  Scheduling/             kalender, slot, booking, daftar tunggu, no-show
  Finance/                kas, bank, biaya, jurnal, laporan keuangan, penutupan periode
  Commission/             aturan, perhitungan, laporan (dapat dimatikan)
  HR/                     profil staf, shift, absensi, cuti, pengingat STR
  Notifications/          template, kanal, aturan, log, pengiriman ulang
  Integrations/           kontrak dan driver pembayaran, WhatsApp, email; webhook
  Reporting/              laporan, tampilan tersimpan, ekspor, pengiriman terjadwal
  CustomerPortal/         PWA pelanggan
```

Tiap modul berisi: `Actions/`, `Models/`, `Policies/`, `Enums/`, `DTOs/`, `Events/`, `Listeners/`, `Contracts/`, `Filament/`, `Livewire/`, `Database/{migrations,factories,seeders}`, `Routes/`, `Resources/{views,lang}`, `Tests/`, dan `README.md` singkat (tanggung jawab, kontrak publik, peristiwa yang dipancarkan).

### 5.2 Aturan batas modul

- Modul berinteraksi lewat **Contract**, **Action publik**, dan **Event domain**. Jangan menulis langsung ke tabel atau model modul lain.
- Pembacaan model data induk bersama (Branches, Customers, Catalog, Identity) diperbolehkan. Penulisan lintas modul tidak.
- Modul layanan (Clinic, Grooming, Boarding, Scheduling, Procurement) mengirim biaya ke Billing lewat satu kontrak seragam, misalnya `BillableItem` yang masuk ke Kunjungan yang sama. Inilah penjaga aturan "satu kunjungan, satu tagihan".
- Modul yang bisa dimatikan (Commission, loyalitas, rawat inap, dst.) tidak boleh dibutuhkan modul lain. Ketika mati: menu hilang, tidak ada perhitungan berjalan, tidak ada tabel yang dibaca.
- Tegakkan batas dengan **arch test Pest** (mis. modul tidak boleh mengimpor internal modul lain). Pelanggaran membuat CI gagal.

### 5.3 Peristiwa dan antrean

Efek samping lintas modul (stok berkurang, komisi dihitung, pesan dikirim, jurnal dibuat) lewat event dan listener, dijalankan secara sinkron dalam transaksi bila harus atomik, atau lewat antrean bila boleh tertunda. Semua job idempoten, dapat diulang aman, dengan backoff dan batas percobaan. Horizon dikonfigurasi dengan antrean terpisah (misalnya `critical`, `default`, `notifications`, `reports`) dan dimonitor dari dashboard kesehatan sistem.

---

## 6. Panduan Teknis per Area

### 6.1 Multi-cabang dan Spatie Multitenancy

README menuntut: pelanggan, hewan, produk, template, peran, dan integrasi **dipakai bersama**. Stok, kasir, shift, jadwal, dan laporan **terpisah per cabang**. Pemilik dapat melihat **gabungan semua cabang**, dan pengguna dapat berpindah cabang satu klik. Ini bukan multi-tenant terisolasi penuh.

Arahan:

- Pakai satu basis data. **Cabang adalah tenant** (model `Branch` sebagai tenant model Spatie Multitenancy) dengan pengisolasian berbasis kolom `branch_id`, bukan basis data terpisah.
- Pisahkan dua jenis tabel secara tegas: tabel **bercakupan cabang** (memakai trait seperti `BelongsToBranch` dengan global scope) dan tabel **global bersama** (tanpa scope tenant). Daftarkan klasifikasinya di `docs/ARCHITECTURE.md`.
- Mode "gabungan semua cabang" adalah pelepasan scope yang **sah, eksplisit, dan dijaga hak akses**, bukan celah. Satu pintu: misalnya `BranchScope::withAllBranches()` yang memeriksa hak akses dan cakupan peran pengguna (semua cabang, cabang tertentu, atau data sendiri).
- Pastikan job antrean, jadwal, dan webhook **membawa konteks cabang** (tenant-aware) dan tidak bocor ke cabang lain.
- Cabang aktif tersimpan di sesi. Perpindahan cabang satu klik. Filament dan POS membaca konteks yang sama.
- Wajib ada uji isolasi: pengguna cabang A tidak bisa membaca atau menulis data bercakupan cabang B kecuali hak aksesnya mengizinkan.
- Jika setelah investigasi Spatie Multitenancy tidak cocok untuk model ini, agent boleh memakai pendekatan custom di atas Eloquent global scope sambil tetap memakai paket untuk konteks tenant, antrean, dan perpindahan. Catat alasannya di DECISIONS. Jangan menghapus paket dari stack tanpa instruksi pengguna.

### 6.2 Peran dan hak akses (README bagian 6)

Fondasinya Spatie Laravel Permission, dengan fitur **teams** untuk penugasan per cabang (satu orang bisa kasir di cabang A dan manajer di cabang B). Tambahkan lapisan sendiri untuk hal yang tidak dicakup paket:

- Cakupan data per peran: semua cabang, cabang tertentu, atau milik sendiri.
- Batasan angka (batas diskon kasir, batas refund).
- Persetujuan atasan: PIN supervisor atau persetujuan tercatat untuk void, refund, diskon besar, penyesuaian stok besar.
- Penyembunyian data sensitif per peran (harga beli dan margin, komisi orang lain, catatan internal dokter, kontak pelanggan). Dilakukan di sisi server, termasuk pada ekspor dan API.
- Pengecualian per orang (memberi atau mencabut satu hak) dengan alasan wajib dan tercatat.
- Klon peran, peran buatan sendiri, matriks modul dan aksi dengan pencarian, peringatan hak berisiko tinggi, dan fitur "lihat sebagai peran ini".
- Perlindungan: peran Owner tidak bisa dihapus, selalu ada minimal satu Owner aktif (ditegakkan di tingkat aksi dan diuji).
- Peran bawaan sesuai README bagian 6, dibuat oleh seeder dan dapat diubah pemilik.
- Daftar hak akses dibangun terstruktur dari modul dan aksi (jangan ditulis tersebar), sehingga matriks dan uji dapat dibangkitkan darinya.

### 6.3 Uang, pajak, dan angka

- Simpan Rupiah sebagai **bilangan bulat** (bigint) tanpa float. Gunakan value object `Money` untuk perhitungan dan format (`Rp 1.250.000`).
- Kuantitas boleh desimal (barang curah) dengan presisi tetap (misalnya `decimal(18,4)`). Konversi satuan ganda lewat tabel konversi.
- Pajak (tarif, termasuk atau belum, objek pajak), biaya layanan, dan pembulatan semuanya pengaturan. Hasil perhitungan **dibekukan** pada baris dokumen saat final (tarif tidak berubah mundur).
- Harga beli, jual, bertingkat, per cabang, member, dan promo berjadwal: tetapkan urutan prioritas harga yang deterministik, didokumentasikan, dan diuji.
- HPP: rata-rata atau FIFO sebagai pengaturan. Stok keluar mengikuti FEFO (kedaluwarsa terdekat dulu).

### 6.4 Penomoran dokumen

- Pola dapat diatur per jenis dan per cabang (contoh `INV/JKT/202611/00001`).
- **Tidak boleh ganda dan tidak boleh berlubang** walau transaksi bersamaan. Gunakan tabel penghitung dengan kunci baris (`SELECT ... FOR UPDATE`) yang dialokasikan **di dalam transaksi yang sama** dengan penerbitan dokumen. Bila transaksi gagal, nomor ikut batal (tidak ada lubang).
- Wajib ada uji konkurensi (banyak proses paralel) untuk pembuktian.

### 6.5 Transaksi, stok, dan keuangan

- Transaksi final **tidak dapat diubah**: tegakkan di model, kebijakan, dan bila perlu pemicu basis data. Koreksi hanya lewat void, refund, atau retur dengan alasan wajib, persetujuan sesuai kebijakan, dan jejak lengkap.
- Void, refund, dan retur menyesuaikan otomatis: stok, jurnal keuangan, poin atau saldo, dan komisi (README aturan 5).
- Stok memakai **buku besar gerakan stok yang hanya bertambah** (append-only) per batch dan gudang. Saldo adalah turunan atau cache yang dapat diverifikasi, dengan perintah artisan pemeriksa konsistensi. Operasi stok memakai transaksi dan penguncian baris untuk mencegah stok negatif dan dobel pengeluaran.
- Jurnal otomatis dari penjualan, pembelian, biaya, dan refund (double-entry sederhana). Penutupan periode mengunci jurnal periode itu.

### 6.6 Pembayaran online dan webhook

- Kontrak `PaymentGateway` dengan driver Midtrans dan Xendit. Metode: QRIS, e-wallet, virtual account, link bayar. Mode sandbox dan produksi, tombol Uji Koneksi.
- Verifikasi tanda tangan atau token webhook penyedia di setiap permintaan. Tolak yang tidak valid.
- **Idempoten (aturan 11):** kunci unik pada pengenal peristiwa penyedia, mesin status yang hanya mengizinkan transisi sah, dan pengunciran baris tagihan. Notifikasi yang datang berulang atau tidak berurutan tidak boleh membuat status berubah dua kali atau mencatat pembayaran ganda. Uji dengan permintaan ganda dan paralel.
- Pembayaran kedaluwarsa, gagal, dan sebagian ditangani. Catat biaya gateway dan sediakan pencocokan (rekonsiliasi) pembayaran online dengan tagihan, termasuk job pemeriksa status berkala sebagai jaring pengaman.
- Webhook mengembalikan respons cepat, pemrosesan berat lewat antrean.

### 6.7 WhatsApp, email, dan notifikasi

- Kontrak kanal (`MessageChannel`) dengan driver WhatsApp Cloud API, gateway WhatsApp pihak ketiga, Resend, SMTP, dan notifikasi dalam aplikasi.
- Pesan WhatsApp resmi yang dimulai bisnis **wajib memakai template yang disetujui Meta**. Bangun pengelola template: buat, kirim untuk persetujuan, sinkronkan status, petakan variabel. Bila pesan tidak dapat dikirim karena aturan ini (template belum disetujui, jendela layanan 24 jam tertutup), sistem menjelaskan penyebabnya dengan jelas ke staf (aturan 16).
- OTP pelanggan lewat template autentikasi. Batasi permintaan OTP dan percobaan masuk (rate limit).
- Aturan routing: pemilik dapat menentukan koneksi utama dan pengalihan (misalnya Cloud API untuk pesan templat dan gateway untuk pesan lain).
- Template pesan dengan variabel dan pratinjau, jam tenang, penanganan pelanggan yang berhenti berlangganan, log lengkap (terkirim, diterima, dibaca), percobaan ulang otomatis untuk yang gagal.
- Daftar pesan otomatis mengikuti README 7.12.

### 6.8 Integrasi dan kredensial

- Layar Pengaturan Integrasi hanya untuk Owner dan Super Admin. **Tidak ada kredensial tertanam** di kode atau repositori.
- Kredensial disimpan **terenkripsi** (enkripsi Laravel) di basis data dan tidak pernah ditampilkan ulang (hanya "tersimpan"). Perubahan tercatat di log audit.
- Tampilkan URL webhook otomatis agar mudah disalin, tampilkan status kesehatan tiap integrasi di dashboard.
- Penyedia baru dapat ditambah lewat kontrak tanpa mengubah modul bisnis.

### 6.9 Klinik dan rekam medis (README 7.5)

- Rekam medis berstatus **draf sampai dikunci**. Setelah final, koreksi hanya lewat **addendum berversi**. Setiap akses dan perubahan tercatat (log akses khusus untuk pembacaan rekam medis, selain Activitylog untuk perubahan).
- **Formulir rekam medis dibangun pemilik tanpa pengembang:** definisi formulir disimpan sebagai skema berversi (bagian, isian teks, angka, pilihan, centang, tanggal, tabel, unggah file, tanda tangan, isian wajib atau bersyarat, template per jenis layanan). Catatan lama selalu merujuk versi skema saat dibuat sehingga perubahan template tidak merusaknya (aturan 9). Pemilik menentukan isian mana yang tampil ke pelanggan, dan catatan internal dokter **tidak pernah** tampil ke pelanggan.
- Vaksinasi: protokol per jenis dan umur, nomor batch vaksin, sertifikat dapat dicetak, pengingat jadwal berikutnya otomatis. Vaksin wajib diperiksa sebelum penitipan (aturan 10).
- Resep menurunkan stok otomatis dari batch FEFO dan mencetak label obat. Obat racikan memakai resep komposisi.
- Rawat inap: biaya harian otomatis masuk tagihan. Bedah: persetujuan tindakan dengan tanda tangan digital.
- Lab, dokumen (surat sehat, rujukan, ringkasan medis), dan template dokumen yang dapat diedit pemilik.
- Rekam medis lintas cabang dapat dibatasi lewat pengaturan pemilik.

### 6.10 Penjadwalan (README 7.8)

Sistem **mencegah jadwal bentrok** untuk dokter, groomer, kamar, dan sumber daya lain, termasuk saat dua pemesanan masuk bersamaan (kunci di basis data atau constraint eksklusi PostgreSQL, bukan hanya pemeriksaan di aplikasi). Dukung jam kerja, libur, cuti, durasi dan jeda layanan, daftar tunggu, no-show, deposit booking, aturan jadwal ulang dan pembatalan.

### 6.11 Komisi (README 7.10)

Dapat dinyalakan atau dimatikan. Siklus: menunggu saat belum lunas, final saat lunas, dibalik saat void atau refund. Hanya laporan, tidak ada pemrosesan pembayaran. Staf hanya melihat komisinya sendiri kecuali diberi hak lebih.

### 6.12 Pelanggan dan privasi data

- Nomor telepon dinormalisasi ke format Indonesia (E.164, `+62`). Deteksi dan penggabungan data ganda, impor dan ekspor.
- Waktu disimpan UTC dan ditampilkan sesuai zona waktu bisnis (bawaan Asia/Jakarta, dapat diatur).
- Perlindungan data pribadi mengacu pada prinsip UU PDP: pengaturan retensi, anonimisasi, ekspor data pelanggan, persetujuan notifikasi.

### 6.13 Media dan berkas

Spatie Media Library dengan disk R2 atau lokal. Validasi jenis dan ukuran, pengecekan isi (MIME sebenarnya), nama berkas diacak, akses hanya untuk pihak yang berhak lewat URL bertanda tangan, konversi gambar (thumbnail) lewat antrean. Dipakai untuk foto hewan, foto grooming, lampiran rekam medis, logo, dan bukti biaya.

### 6.14 Cetak dan ekspor

Struk 58 mm, 80 mm, dan A4 lewat CSS cetak browser dan PDF. Cetak ulang bertanda "SALINAN". Tanpa driver printer thermal. Laporan dapat diekspor ke CSV, Excel, dan PDF, dengan filter fleksibel, tampilan tersimpan, dan pengiriman terjadwal ke email atau WhatsApp lewat Scheduler dan antrean.

### 6.15 Antarmuka dan pengalaman pakai (README bagian 13)

- Tema terang, gelap, atau otomatis, satu warna aksen yang dapat diatur, kepadatan nyaman atau ringkas. Branding (nama, logo, ikon, warna) dari pengaturan.
- Palet perintah dan pencarian global (Ctrl/Cmd+K) untuk pelanggan, hewan, produk, transaksi.
- Tabel dengan kolom dapat diatur, tampilan tersimpan, ekspor. Dashboard dengan widget yang dapat disusun tiap pengguna dan menyesuaikan hak akses.
- Kasir ramah sentuh (tombol besar) dan pintasan keyboard. Animasi halus yang membantu.
- Atribusi "Powered by PETORA INDONESIA" dan pernyataan hak cipta di aplikasi staf, aplikasi pelanggan, dan dokumen cetak, tanpa pengaturan penyembunyi. Tambahkan uji yang memastikan atribusi selalu ada.
- Terapkan skill `frontend-design` bila tersedia, agar tampilan tidak terkesan templat generik, namun tetap hangat dan profesional.

### 6.16 Keamanan (README bagian 12)

- Setiap aksi diperiksa terhadap hak akses dan cakupan. Tidak ada rute atau aksi tanpa Policy.
- Kata sandi kuat, sesi aman, 2FA yang dapat diwajibkan per peran, daftar perangkat aktif dapat dicabut.
- Rate limit pada login, OTP, dan endpoint publik. CSRF, validasi input, escaping output, header keamanan, proteksi terhadap SSRF dan unggahan berbahaya, pembatasan CORS.
- Log audit untuk login, perubahan peran, perubahan integrasi, diskon besar, void, refund, akses data medis, ekspor data.
- Dependensi diaudit (`composer audit`, `npm audit`) di CI.
- Rahasia hanya di `.env` dan secret manager, tidak pernah di Git.

---

## 7. Fase Pengerjaan

Kerjakan berurutan. Pindah fase hanya bila **gerbang fase** lolos: semua fitur fase tersebut berfungsi penuh dan teruji, `composer verify` hijau, dokumentasi dan `PROGRESS.md` diperbarui, dan ada commit atau tag penanda fase. Fase boleh dipecah lebih halus, dan fitur kecil dari fase berikutnya boleh dimajukan bila memang menjadi prasyarat. Catat perubahannya.

| Fase | Isi | Hasil utama |
|---|---|---|
| **0. Fondasi** | Baca dokumen, salin `docs/SPEC.md`, tulis ulang bagian teknologi README, buat `docs/{DECISIONS,ASSUMPTIONS,ARCHITECTURE,TRACEABILITY}.md`, bangun matriks keterlacakan seluruh fitur README, inisialisasi proyek Laravel, Pest, Pint, Larastan, CI GitHub Actions, Docker dev, struktur modul, arch test, draf `LICENSE` | Proyek kosong yang hijau di CI dan terdokumentasi |
| **1. Platform inti** | Identity (Fortify, 2FA, sesi), peran dan hak akses (RBAC lengkap), cabang dan tenancy, pengaturan, penomoran dokumen, log audit, branding dan atribusi, i18n id/en, kerangka UI, palet perintah, wizard pengaturan awal, kesehatan sistem | Owner dapat memasang, masuk, membuat cabang dan peran |
| **2. Data induk** | Pelanggan, hewan, kepemilikan ganda, pencarian cepat, deteksi ganda, impor dan ekspor | Pelanggan dan hewan lintas cabang |
| **3. Katalog dan stok** | Produk, varian, satuan, harga bertingkat, layanan dan paket, pajak, batch dan FEFO, kartu stok, opname, penyesuaian, transfer antar cabang, supplier, PR/PO, penerimaan, retur, hutang supplier, bundel, resep komposisi, label | Stok akurat dan teraudit |
| **4. Tagihan dan kasir** | Kunjungan dan tagihan tunggal, kontrak `BillableItem`, shift, POS, diskon, voucher, promo, pembayaran manual, split, deposit, piutang, cicilan, tahan dan lanjut, void, refund, retur, loyalitas, struk, invoice | Kasir lengkap yang aturan bisnisnya lolos uji |
| **5. Klinik** | Pendaftaran, antrian, triase, pemeriksaan SOAP, formulir pemilik berversi, resep, vaksin dan sertifikat, lab, rawat inap, bedah, dokumen, penguncian dan addendum | Kunjungan klinik sampai tagihan |
| **6. Grooming, penitipan, jadwal** | Layanan grooming dan status, foto, penitipan dan log harian, kalender dan booking, anti-bentrok, daftar tunggu, no-show, deposit booking | Ketiga layanan masuk tagihan yang sama |
| **7. Integrasi dan notifikasi** | Layar integrasi, driver Midtrans, Xendit, WhatsApp Cloud API, gateway WhatsApp, Resend, SMTP, pengelola template, webhook idempoten, rekonsiliasi, log pesan, pesan otomatis, jam tenang | Pembayaran online dan pesan berjalan (sandbox teruji) |
| **8. Aplikasi pelanggan** | PWA, login OTP WhatsApp, profil dan hewan, riwayat, vaksin, dokumen, tagihan dan bayar, booking, status grooming dan penitipan, notifikasi, preferensi | Pelanggan mandiri |
| **9. Keuangan, komisi, SDM, laporan, dashboard** | Kas dan bank, biaya, jurnal, laba rugi, arus kas, neraca sederhana, penutupan periode, rekap pajak, komisi, SDM dasar, seluruh laporan README 7.13 dengan ekspor dan jadwal, dashboard dapat disusun | Pemilik memantau semua cabang |
| **10. Pengerasan** | Audit keamanan, performa dan indeks, aksesibilitas, uji beban POS, uji pencadangan dan pemulihan, uji pembaruan dan pemulihan versi, data contoh, panduan per peran, jalankan seluruh alur README bagian 16 ujung ke ujung | Siap dipakai nyata |
| **11. Rilis** | Gerbang rilis (bagian 14), README final (Memulai Cepat, Teknologi), CHANGELOG, tag versi 1.0.0 | Rilis |

Setiap fase memakai daftar tugas di `PROGRESS.md` (lihat bagian 11). Untuk pekerjaan besar yang independen, agent boleh memakai sub-agent paralel. Agent utama bertanggung jawab atas konsistensi, integrasi, dan peninjauan hasilnya.

---

## 8. Pengujian

### 8.1 Perkakas

Pest PHP untuk semua uji. Basis data uji memakai **PostgreSQL sungguhan** (bukan SQLite) agar perilaku kunci, constraint, dan tipe sama dengan produksi. Larastan dan Pint di setiap CI. Plugin Pest bawaan (arch, type coverage, bila berguna) boleh dipakai.

### 8.2 Lapisan uji

- **Unit:** value object (Money, penomoran, perhitungan pajak, harga, komisi, FEFO).
- **Fitur (HTTP/Livewire/Filament):** setiap alur dan setiap aksi, termasuk penolakan akses.
- **Arch:** batas modul, tidak ada `dd`, semua model bercakupan cabang memakai trait, semua Policy terdaftar.
- **Konkurensi:** penomoran dokumen, pengeluaran stok, webhook ganda, booking bentrok, pembayaran ganda. Gunakan proses paralel atau kunci basis data yang nyata.
- **Isolasi cabang dan hak akses:** matriks peran dan aksi, penyembunyian data sensitif, privasi pelanggan (pelanggan hanya melihat datanya).
- **Alur ujung ke ujung:** setiap skenario README bagian 16 menjadi satu uji alur lengkap (kunjungan klinik sampai pembayaran, booking dari rumah, penitipan beberapa hari, pemilik memantau dari jauh).
- **Browser (opsional tapi disarankan):** alur POS dan aplikasi pelanggan.

### 8.3 Keterlacakan aturan bisnis

Setiap **aturan bisnis kunci 1 sampai 18** (README bagian 15) memiliki minimal satu uji bernama jelas (misalnya `BusinessRule04_FinalTransactionsCannotBeSilentlyChanged`) dan terdaftar di `docs/BUSINESS_RULES.md` beserta lokasi ujinya. Aturan tanpa uji dianggap belum diimplementasikan.

### 8.4 Disiplin

- Tulis uji bersama fitur, bukan belakangan.
- Uji yang gagal diperbaiki akarnya. Jangan dihapus, dilewati, atau dilemahkan agar hijau.
- Perintah satu pintu `composer verify` menjalankan: Pint (mode cek), Larastan, dan seluruh uji Pest. CI GitHub Actions menjalankan perintah yang sama dengan layanan PostgreSQL dan Redis.
- Pemanggilan layanan eksternal di uji selalu dipalsukan (fake). Tidak ada uji yang menyentuh jaringan.

---

## 9. Deployment dan Operasional

Sasaran (README bagian 14): pemilik non-teknis dapat memasang dan merawat tanpa pengembang.

### 9.1 Docker

- `docker compose` untuk **produksi** (terpisah dari konfigurasi pengembangan): aplikasi PHP, web server dengan **HTTPS otomatis** (misalnya Caddy), PostgreSQL, Redis, worker Horizon, dan penjadwal. Volume terpisah untuk basis data dan berkas. Health check pada semua layanan.
- Skrip pemasangan **satu perintah** (misalnya `install.sh`) yang membuat `.env` otomatis: `APP_KEY`, kata sandi basis data dan Redis acak, `APP_URL`/`PUBLIC_APP_URL`, lalu memulai tumpukan, menjalankan migrasi, dan membuka wizard. Konfigurasi dan kredensial pengembangan tidak boleh terbawa ke produksi.
- Kunci enkripsi (`APP_KEY`) dijelaskan dengan tegas di dokumentasi: harus dicadangkan, karena data terenkripsi (kredensial integrasi, rahasia 2FA) tidak dapat dibuka tanpanya.

### 9.2 Wizard pengaturan awal

Pada pembukaan pertama: buat akun Owner, isi profil bisnis, tambah cabang pertama, pilih zona waktu, bahasa, dan pajak. Wizard **tertutup permanen** setelah selesai. Wizard memeriksa aplikasi dan basis data dari browser. Bila `PUBLIC_APP_URL` HTTPS publik dikonfigurasi, pemeriksaan juga meminta endpoint health lewat alamat tersebut. Pesan hasil harus jujur: pemeriksaan ini bukan uji dari jaringan penyedia webhook. Pemilik dapat memuat **data contoh** untuk mencoba sistem, dan data contoh dapat dihapus bersih.

### 9.3 Cadangan, pembaruan, pemulihan

- Cadangan terjadwal (basis data + berkas) lewat Scheduler dengan retensi yang dapat diatur, dapat dipicu manual, dan **dapat dipulihkan** lewat perintah atau layar yang terdokumentasi. Uji pemulihan sungguhan di Fase 10.
- Pembaruan aman: cadangan otomatis dibuat dahulu, versi baru dipasang, migrasi dijalankan, dan bila gagal sistem dikembalikan ke versi sebelumnya. Skrip `update` dan `rollback` terdokumentasi dan diuji.
- Mode pemeliharaan, endpoint health (`/.well-known/petora-health` atau sejenis), dan layar kesehatan sistem (antrean, penjadwal, penyimpanan, cadangan terakhir, status integrasi).

### 9.4 Dokumentasi operasi

`docs/INSTALLATION.md`, `docs/OPERATIONS.md` (cadangan, pemulihan, pembaruan, pemantauan, pemecahan masalah), dan `docs/SECURITY.md`. Tulis untuk pembaca non-teknis: langkah bernomor, perintah siap salin.

---

## 10. Git, GitHub, dan Dokumentasi

### 10.1 Git

- Cabang utama `main` selalu hijau. Kerja di cabang `feat/…`, `fix/…`, `chore/…`, `docs/…`, digabung lewat pull request bila remote GitHub tersedia (gunakan `gh` bila ada). Bila tidak ada remote, commit lokal dan sampaikan kepada pengguna.
- Pesan commit mengikuti Conventional Commits (`feat(billing): ...`). Commit kecil dan sering, satu perubahan logis per commit.
- Jangan force-push ke `main`. Jangan commit rahasia, `.env`, atau berkas besar yang tidak perlu. Sediakan `.env.example` yang lengkap dan terkomentari.
- Versi semantik. Tag di akhir setiap fase (`v0.N.0`) dan rilis (`v1.0.0`).
- GitHub Actions: CI (verify), audit dependensi, dan bila wajar pembuatan citra Docker.

### 10.2 Berkas wajib saat selesai

```
README.md        produk + Memulai Cepat + Teknologi yang Digunakan (diperbarui di akhir)
agent.md         dokumen ini
PROGRESS.md      status terkini
CHANGELOG.md     format Keep a Changelog
LICENSE          lisensi dan atribusi
.env.example
docs/
  SPEC.md  DECISIONS.md  ASSUMPTIONS.md  ARCHITECTURE.md  TRACEABILITY.md
  BUSINESS_RULES.md  INSTALLATION.md  OPERATIONS.md  SECURITY.md  TESTING.md  INTEGRATIONS.md
  guides/  owner.md  manajer.md  kasir.md  resepsionis.md  dokter.md  paramedis.md
           groomer.md  penitipan.md  gudang.md  keuangan.md  pelanggan.md
```

### 10.3 LICENSE

Buat `LICENSE` yang menyatakan hak cipta dan lisensi dipegang PETORA INDONESIA, kode sumber diserahkan kepada pelanggan, pelanggan boleh mengganti nama, logo, dan warna, serta **wajib mempertahankan atribusi "Powered by PETORA INDONESIA"** di aplikasi staf, aplikasi pelanggan, dan dokumen cetak. Tanpa kunci lisensi atau aktivasi online. Catat di `docs/ASSUMPTIONS.md` bahwa teks ini perlu ditinjau ahli hukum, tanpa menghentikan pekerjaan.

### 10.4 Format catatan

`docs/DECISIONS.md`, entri singkat per keputusan:

```
## D-0007 — Judul keputusan
Tanggal: YYYY-MM-DD   Fase: N   Status: diterima
Konteks: masalah dan pilihan yang dipertimbangkan.
Keputusan: apa yang dipilih.
Alasan: mengapa, termasuk risiko dan konsekuensi.
```

`docs/ASSUMPTIONS.md`: `A-0003 — celah spesifikasi — asumsi yang diambil — alasan — bagian README terkait`.

`docs/TRACEABILITY.md`: tabel setiap fitur README (bagian dan butir) → modul → status (belum / berjalan / selesai) → berkas uji. Ini peta tunggal untuk menjawab "apakah semuanya sudah ada".

### 10.5 PROGRESS.md

Satu berkas ringkas yang bisa dibaca sesi baru dalam satu menit: fase aktif, daftar tugas (selesai, sedang dikerjakan, berikutnya), utang teknis yang disadari, hambatan terbuka, perintah penting (cara menjalankan, menguji), dan tanggal pembaruan terakhir. Diperbarui di setiap titik henti yang bermakna.

---

## 11. Protokol Sesi: Memulai, Melanjutkan, Mengakhiri

Pekerjaan ini panjang dan akan melintasi banyak sesi. Agent tidak boleh bergantung pada ingatan percakapan.

**Memulai atau melanjutkan:**
1. Baca dokumen sesuai 2.2. Jalankan `git status` dan `git log` singkat untuk memahami keadaan.
2. Jalankan `composer verify`. Bila merah, perbaiki dulu sebelum fitur baru.
3. Buat daftar tugas sesi dari `PROGRESS.md`, kerjakan tugas berikutnya.

**Selama bekerja:**
- Kerjakan irisan vertikal kecil. Commit setelah setiap irisan yang hijau.
- Jangan membuka banyak front sekaligus. Selesaikan sebelum pindah.
- Hindari narasi panjang. Fokus pada hasil kerja.

**Mengakhiri (atau sebelum konteks penuh):**
1. Pastikan `composer verify` hijau dan perubahan ter-commit. Jangan meninggalkan pekerjaan setengah di `main`.
2. Perbarui `PROGRESS.md`, `docs/TRACEABILITY.md`, dan `CHANGELOG.md`.
3. Tulis langkah berikutnya yang konkret di `PROGRESS.md` agar sesi berikutnya bisa langsung mulai.

---

## 12. Menangani Ketidakpastian dan Hambatan

- **Spesifikasi bercelah:** pilih perilaku paling umum, catat di ASSUMPTIONS, jadikan pengaturan bila wajar, lanjut.
- **Spesifikasi bertentangan:** ikuti hierarki 2.1, catat konflik.
- **Kredensial atau akun nyata tidak tersedia:** jangan mengarang kredensial, dan jangan memanggil layanan nyata. Bangun melawan sandbox bila ada, atau driver **palsu** (fake) yang meniru kontrak dan respons penyedia dengan setia, lalu uji lewat webhook dan HTTP fake. Tandai di `docs/INTEGRATIONS.md` bahwa pengujian langsung ke penyedia nyata menunggu kredensial pemilik, dan jelaskan caranya.
- **Pustaka bermasalah atau tidak kompatibel:** cari alternatif yang setara, dokumentasikan, atau tulis sendiri bila kecil. Jangan mengubah stack inti.
- **Bug yang sulit:** cari akar masalahnya, tulis uji yang mereproduksi, baru perbaiki. Jangan menutup gejala.
- **Fitur terlalu besar:** pecah menjadi irisan yang masing-masing berguna dan lengkap, bukan versi dipangkas yang menyisakan menu mati.
- **Permintaan pengguna baru di tengah jalan:** ikuti, lalu perbarui SPEC, README, TRACEABILITY, dan dokumen lain agar konsisten.
- **Hanya bila benar-benar buntu (1.4):** laporkan sebagai "KEPUTUSAN DIPERLUKAN" dengan satu kalimat masalah, opsi, dan rekomendasi agent. Sementara itu, kerjakan hal lain.

---

## 13. Larangan

Agent tidak boleh:

1. Meninggalkan menu mati, tombol tanpa fungsi, layar "segera hadir", data palsu yang tampak asli, atau fitur setengah jadi pada rilis.
2. Mengubah transaksi final atau rekam medis terkunci secara diam-diam, termasuk lewat seeder, migrasi, atau skrip.
3. Menyimpan kredensial, kunci, atau rahasia di kode, repositori, log, atau layar.
4. Melewati pemeriksaan hak akses, cakupan cabang, atau privasi pelanggan demi kemudahan.
5. Menambahkan pengaturan, jalan pintas, atau kode yang menyembunyikan atribusi PETORA INDONESIA.
6. Menambahkan kunci lisensi, aktivasi online, telemetri tersembunyi, atau pengiriman data bisnis ke pihak ketiga tanpa pengaturan eksplisit.
7. Menghapus, melemahkan, atau melewati uji agar CI hijau.
8. Memakai float untuk uang.
9. Memasukkan fitur di luar cakupan (offline, driver printer, penggajian, mata uang lain).
10. Mengganti atau membuang stack inti tanpa instruksi pengguna.
11. Menjalankan perintah destruktif di luar repositori kerja atau menyentuh sistem nyata (penyedia pembayaran, WhatsApp, server pelanggan) tanpa instruksi eksplisit.
12. Mengklaim "selesai" tanpa bukti: `composer verify` hijau, alur diuji, dokumentasi terbarui.
13. Menghapus isi produk README bagian 1 sampai 20.

---

## 14. Gerbang Rilis

Rilis 1.0.0 hanya boleh dinyatakan bila **semua** poin ini terpenuhi dan terbukti:

**Kelengkapan**
- [ ] Setiap butir fitur README bagian 7 sampai 12 berstatus "selesai" di `docs/TRACEABILITY.md` dengan uji terkait.
- [ ] Tidak ada menu mati, halaman kosong tanpa fungsi, atau TODO tertinggal di kode.
- [ ] Seluruh skenario README bagian 16 lolos sebagai uji ujung ke ujung.

**Kebenaran**
- [ ] Seluruh 18 aturan bisnis kunci punya uji dan lulus (`docs/BUSINESS_RULES.md`).
- [ ] Uji konkurensi lulus: penomoran tanpa ganda dan tanpa lubang, stok tidak negatif, webhook ganda sekali efek, jadwal tidak bentrok.
- [ ] Isolasi cabang, hak akses, penyembunyian data sensitif, dan privasi pelanggan teruji.

**Kualitas**
- [ ] `composer verify` hijau dan CI hijau di `main`.
- [ ] Larastan di level yang ditargetkan tanpa pengecualian liar. `composer audit` dan `npm audit` tanpa kerentanan tinggi atau kritis yang belum ditangani.
- [ ] Tidak ada N+1 pada layar utama, POS responsif pada data besar, tabel besar berpaginasi.
- [ ] Aksesibilitas dasar: navigasi keyboard, kontras, label formulir.
- [ ] Bahasa Indonesia dan Inggris lengkap, tidak ada teks mentah atau kunci terjemahan yang tampak di UI.

**Pemasangan dan operasional**
- [ ] Pemasangan satu perintah berhasil di server bersih. Wizard awal dan data contoh berfungsi.
- [ ] Cadangan, pemulihan, pembaruan, dan pemulihan versi diuji nyata dan terdokumentasi.
- [ ] Health check dan layar kesehatan sistem akurat. Pesan hasil pemeriksaan alamat publik jujur.

**Integrasi**
- [ ] Setiap integrasi (Midtrans, Xendit, WhatsApp Cloud API, gateway WhatsApp, Resend, SMTP) lolos uji kontrak dengan driver palsu, memverifikasi tanda tangan webhook, dan memiliki panduan pengujian sandbox untuk pemilik.

**Branding dan hukum**
- [ ] Atribusi dan hak cipta tampil di aplikasi staf, aplikasi pelanggan, dan dokumen cetak, tanpa pengaturan penyembunyi (ada uji).
- [ ] `LICENSE` ada, tanpa kunci lisensi atau aktivasi online. Catatan tinjauan hukum ada di ASSUMPTIONS.

**Dokumentasi**
- [ ] Semua berkas pada 10.2 ada dan akurat, termasuk panduan per peran.
- [ ] README diperbarui: "Memulai Cepat" (pasang satu perintah) dan "Teknologi yang Digunakan" sesuai kenyataan, dengan isi produk tetap utuh.
- [ ] `CHANGELOG.md` dan tag `v1.0.0` dibuat.

---

## 15. Pelaporan kepada Pengguna

- Laporan singkat, berbasis hasil: apa yang selesai, apa yang berikutnya, apa yang menghalangi (bila ada). Tidak mengulang langkah demi langkah.
- Gunakan Bahasa Indonesia, nada profesional dan hangat, tanpa basa-basi.
- Bedakan dengan jelas antara yang **terbukti** (uji lulus, dijalankan) dan yang **diasumsikan**. Jangan melebih-lebihkan.
- Di akhir tiap fase: ringkasan hasil, status gerbang fase, tautan ke commit atau tag, dan satu langkah berikutnya.
- Bila ada hal yang hanya bisa dilakukan pengguna (membuat akun penyedia, memasukkan kredensial sandbox, meninjau lisensi), sebutkan sekali, spesifik, dan bukan sebagai syarat untuk melanjutkan pekerjaan lain.

---

*PETORA INDONESIA. Hak cipta dan lisensi dipegang oleh PETORA INDONESIA.*
