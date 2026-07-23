# Padang In Your Hand (Polygon Padang) 🗺️

**Padang In Your Hand** adalah Sistem Informasi Geografis (GIS) interaktif berbasis web untuk memetakan wilayah administrasi (Kecamatan), infrastruktur, serta titik lokasi penting (Point of Interest) di Kota Padang, Sumatera Barat. 

Aplikasi ini tidak hanya menampilkan peta yang informatif kepada publik, tetapi juga menyediakan *Admin Dashboard* canggih yang memungkinkan pengelola untuk menggambar area (polygon) dan menempatkan titik lokasi (marker) secara presisi langsung di atas peta satelit.

---

## ✨ Fitur Utama

- **Peta Publik Interaktif**: Menampilkan batas-batas kecamatan dan titik lokasi dengan desain *premium* menggunakan CartoDB Voyager dan Leaflet.
- **Manajemen Kategori (Layer)**: Pengelompokan data spasial ke dalam layer-layer yang bisa diaktif/nonaktifkan oleh pengguna.
- **Admin Dashboard & Spatial Editor**:
  - Halaman admin modern bergaya *glassmorphism*.
  - **Polygon Drawing Tool**: Admin dapat menggambar area batas wilayah atau infrastruktur (menggunakan ekstensi `leaflet-polydraw`) langsung di atas Google Maps Satellite.
  - **Marker Pointing**: Presisi meletakkan titik lokasi hanya dengan klik pada peta.
  - Form pendukung untuk menambahkan detail seperti Nama, Deskripsi, dan Gambar (Foto Lokasi).
- **Auto-detect Kecamatan**: Mendeteksi secara otomatis titik yang ditandai berada di wilayah kecamatan mana berdasarkan GeoJSON batas kota.
- **Styling Premium**: Desain UI/UX yang tidak kaku, dengan dukungan marker SVG dinamis, popup modern yang *rounded*, dan *hover states* yang mulus.

## 🛠️ Tech Stack

- **Backend**: [Laravel 11.x](https://laravel.com/) (PHP)
- **Frontend**: Blade Templates, [Tailwind CSS](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/)
- **GIS & Maps**: [Leaflet.js](https://leafletjs.com/), Leaflet Polydraw
- **Map Providers**: CartoDB Voyager (Publik), Google Maps Satellite (Admin)

---

## 🚀 Panduan Instalasi (Development)

Untuk menjalankan proyek ini di *local environment* Anda, ikuti langkah-langkah berikut:

### 1. Kebutuhan Sistem
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / PostgreSQL

### 2. Langkah Instalasi

1. **Clone repositori ini**
   ```bash
   git clone https://github.com/RhlArdv/polygon-padang.git
   cd polygon-padang
   ```

2. **Install dependensi PHP dan Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file konfigurasi bawaan dan sesuaikan pengaturan database Anda:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur koneksi database (misal: `DB_DATABASE=padang_gis`).

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi Database & Seeder**
   ```bash
   php artisan migrate --seed
   ```
   *(Pastikan seeder telah mengatur akun admin default dan mengimpor data GeoJSON kecamatan jika diperlukan)*.

6. **Jalankan Server Lokal**
   Buka 2 terminal secara terpisah untuk menjalankan backend dan frontend asset compiler:
   ```bash
   # Terminal 1 (Backend)
   php artisan serve
   
   # Terminal 2 (Vite Frontend)
   npm run dev
   ```

7. **Akses Aplikasi**
   - Halaman Publik: `http://localhost:8000`
   - Halaman Admin: `http://localhost:8000/login`

---

## 📂 Struktur Penting

- `resources/views/welcome.blade.php`: Tampilan landing page (Peta Publik).
- `resources/views/peta.blade.php`: Logic utama Leaflet map untuk area Admin (Draw Polygon & Marker).
- `resources/views/layouts/admin.blade.php`: Struktur UI sidebar & topbar admin.
- `public/geojson/`: Tempat penyimpanan file batas wilayah GeoJSON (`padang-kecamatan-dissolved.geojson`).

## 📄 Lisensi

Aplikasi ini berstatus Open-Source di bawah [MIT license](https://opensource.org/licenses/MIT).
