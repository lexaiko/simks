## Tentang SAKU

Sistem Administrasi Keuangan (SAKU) adalah Software Administrasi Keuangan Sekolah yang digunakan di lembaga.
fitur yang tersedia antara lain :

-   Data Siswa
-   Data Periode
-   Data Kelas
-   Data Tagihan
-   Data Kas
-   Data Transaksi Keuangan
-   Dll

Fitur-fitur diatas masih dalam pengembangan aktif, sehingga sangat mungkin terdapat perubahan-perubahan di masa yang akan datang.

## Panduan instalasi

Siapkan software pendukung:

-   [PHP](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [Git](https://git-scm.com/)
-   [NPM](https://www.npmjs.com/)

Pastikan software-software diatas sudah berada di path sistem operasi (https://www.computerhope.com/issues/ch000549.htm)

```bash
git clone https://github.com/lexaiko/simks.git
cd saku
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan setting database di file `.env`

```bash
php artisan filament:install --panels
php artisan make:queue-batches-table
php artisan make:notifications-table
php artisan vendor:publish --tag=filament-actions-migrations
php artisan migrate:fresh --seed
npm install
npm run dev
php artisan storage:link
```

Jalankan Aplikasi dengan perintah

```bash
php artisan serve --host 0.0.0.0  --port=8008
```

Aplikasi bisa diakses di http://localhost:8008. <br/>
Gunakan Username `default.admin` dan Password `default.admin`.
<br/>
<br/>

Untuk menjalankan Queue (digunakan untuk Fungsi Impor) gunakan perintah

```bash
php artisan queue:listen
```

atau juga bisa menggunakan [Supervisor](https://laravel.com/docs/11.x/queues#supervisor-configuration)

## Teknologi

-   [PHP 8](https://www.php.net/)
-   [Laraval 11](https://laravel.com)
-   [Livewire 3](https://laravel-livewire.com/)
-   [Filament 3](https://filamentphp.com/)
-   [MariaDB](https://mariadb.org/)
-   ....

## Lisensi

Aplikasi ini menggunakan lisensi sesuai dengan lisensi Laravel yaitu [MIT license](https://opensource.org/licenses/MIT).<br/>
Silahkan dipakai dan dimodifikasi sesuai dengan lisensi diatas
