# BUSINESS_RULES — PETORA INDONESIA

Pemetaan 18 aturan bisnis kunci (README §15) ke implementasi dan uji. Status awal: **belum**.

| # | Aturan (ringkas) | Status | Lokasi uji (rencana/nama) | Catatan |
|---|---|---|---|---|
| 1 | Satu kunjungan → satu tagihan, satu pembayaran, satu struk (kasir/klinik/grooming/penitipan) | belum | `BusinessRule01_SingleVisitSingleInvoice` | Kontrak BillableItem → Billing |
| 2 | Pelanggan/hewan lintas cabang; stok/kasir/shift/jadwal/laporan per cabang; harga boleh beda | belum | `BusinessRule02_BranchScopeAndSharedMasterData` | D-0004 |
| 3 | FEFO; stok berkurang otomatis dari penjualan, resep, pemakaian bahan | belum | `BusinessRule03_FefoAndAutomaticStockDeduction` | Ledger append-only |
| 4 | Transaksi final tidak diubah diam-diam; koreksi via void/refund/retur + jejak | belum | `BusinessRule04_FinalTransactionsImmutable` | Model/policy/trigger |
| 5 | Void/refund/retur menyesuaikan stok, keuangan, poin/saldo, komisi | belum | `BusinessRule05_ReversalAdjustsAllSides` | Event/aksi atomik |
| 6 | Diskon/refund/penyesuaian stok di atas batas perlu PIN/persetujuan atasan | belum | `BusinessRule06_ApprovalAndSupervisorPin` | |
| 7 | Komisi: lapor saja; menunggu→final→dibalik | belum | `BusinessRule07_CommissionLifecycle` | Modul opsional |
| 8 | Rekam medis draf→kunci; koreksi addendum berversi; catatan internal dokter tak tampil ke pelanggan | belum | `BusinessRule08_MedicalRecordLockAndPrivacy` | Log akses |
| 9 | Perubahan template formulir medis tidak merusak catatan lama (berversi) | belum | `BusinessRule09_VersionedMedicalForms` | |
| 10 | Jadwal/sumber daya tak bentrok; vaksin wajib sebelum penitipan | belum | `BusinessRule10_NoScheduleConflictAndBoardingVaccine` | Constraint DB |
| 11 | Status pembayaran online berubah otomatis tepat sekali meski webhook berulang | belum | `BusinessRule11_IdempotentPaymentWebhook` | Uji ganda/paralel |
| 12 | Peran Owner tak bisa dihapus & selalu ada minimal satu Owner aktif | belum | `BusinessRule12_OwnerRoleProtection` | |
| 13 | Hak akses ditegakkan tiap aksi, termasuk cakupan & penyembunyian data sensitif | belum | `BusinessRule13_AuthorizationAndDataScoping` | |
| 14 | Nomor dokumen sesuai pola, tanpa ganda/lubang walau bersamaan | belum | `BusinessRule14_GaplessUniqueDocumentNumbering` | Uji konkurensi |
| 15 | Pelanggan hanya melihat data miliknya | belum | `BusinessRule15_CustomerDataPrivacy` | Guard customer |
| 16 | Pesan WA resmi bisnis wajib template disetujui; sistem jelaskan bila tak bisa kirim | belum | `BusinessRule16_WhatsAppTemplateCompliance` | |
| 17 | Fitur dimatikan tak muncul & tak menjalankan perhitungan | belum | `BusinessRule17_DisabledModulesInactive` | |
| 18 | Mata uang hanya Rupiah | belum | `BusinessRule18_IDROnly` | Money integer |

Setiap aturan wajib memiliki ≥1 uji bernama jelas dan lulus sebelum dinyatakan selesai.
