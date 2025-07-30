# Laravel Points API – Recruitment Boilerplate

Repository ini merupakan **boilerplate Laravel** untuk proses **rekrutmen backend developer** (tingkat intermediate-senior).

### Tujuan
Repositori ini hanya menyediakan struktur awal (boilerplate) agar kandidat dapat fokus langsung pada implementasi fitur yang diminta dalam soal rekrutmen.

### Fitur yang Disiapkan
- Laravel versi terbaru (v11)
- Tabel `users`, `transactions`, dan `points` telah disiapkan melalui migration
- Belum terdapat implementasi API – kandidat akan diminta untuk mengerjakannya

### Petunjuk Instalasi
1. Clone atau ekstrak repository ini.
2. Jalankan perintah berikut:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
