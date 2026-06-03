# Sistem Informasi Absensi Komplek L

Sebuah aplikasi manajemen presensi modern yang cepat dan akurat, dibangun memanfaatkan pemindaian QR-Code. Sistem ini dirancang menggunakan arsitektur **Laravel** dengan panel administrasi **Filament** untuk memberikan pengalaman antarmuka yang sangat responsif, rapi, dan mudah dikelola.

## Fitur Utama (Key Features)

- **QR-Code Generation & Scanning:** Kemampuan untuk menghasilkan (_generate_) kode QR unik untuk entri presensi dan memindainya secara _real-time_ menggunakan perangkat _mobile_ atau _webcam_.
- **Real-time Attendance Logging:** Pencatatan waktu masuk dan keluar secara seketika (_real-time_) langsung ke dalam basis data dengan akurasi tinggi.
- **Modern Admin Dashboard:** Panel administrasi yang super cepat dan dinamis, dibangun sepenuhnya dengan komponen _Resources_ dan _Pages_ dari **Filament v5**.
- **Data Export & Reporting:** Fasilitas rekapitulasi data kehadiran untuk keperluan evaluasi bulanan.
- **Responsive UI:** Antarmuka yang dioptimalkan untuk berbagai perangkat (desktop, tablet, dan _smartphone_) berkat penggunaan _utility-first CSS_.

## 💻 Teknologi yang Digunakan (Tech Stack)

Sistem ini dikembangkan di atas arsitektur **TALL Stack** generasi terbaru:

- **Framework:** Laravel 13
- **Admin Panel:** Filament v5
- **Front-end:** Tailwind CSS, Alpine.js, Livewire
- **Database:** MySQL
- **QR-Code Module:** SimpleSoftwareIO's QR Code Library
