# OPERATIONS — PETORA INDONESIA

Status: draf Fase 0.

## Komponen (target produksi)
- Aplikasi PHP, web server dengan HTTPS otomatis (mis. Caddy), PostgreSQL, Redis, worker Horizon, scheduler. Volume terpisah untuk DB dan berkas. Health check pada semua layanan.

## Cadangan & pemulihan
- Cadangan terjadwal (DB + berkas) via Scheduler, retensi dapat diatur, pemicu manual, dan dapat dipulihkan. (Uji pemulihan sungguhan di Fase 10.)

## Pembaruan & rollback
- Alur aman: cadangan otomatis → pasang versi baru → migrasi → rollback bila gagal. Skrip `update`/`rollback` akan didokumentasikan dan diuji.

## Pemantauan
- Endpoint health `/.well-known/petora-health`.
- Layar kesehatan sistem: antrean, scheduler, storage, cadangan terakhir, status integrasi.

## Mode pemeliharaan
- Didukung untuk pembaruan/pemulihan terkontrol.

Langkah operasional bernomor akan dilengkapi di Fase 9-11.
