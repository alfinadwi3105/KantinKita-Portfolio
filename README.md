# KantinKita

KantinKita merupakan aplikasi berbasis web yang dikembangkan untuk membantu proses pengelolaan layanan kantin secara lebih terorganisasi.

Proyek ini dibuat sebagai bagian dari praktikum Pemrograman Berbasis Web 2, Program Studi D3 Rekayasa Perangkat Lunak Aplikasi, Universitas Telkom.

## 1. Latar Belakang / Masalah

Pengelolaan layanan kantin membutuhkan sistem yang dapat membantu mengatur informasi menu, pesanan pelanggan, dan antrean agar proses pelayanan lebih terstruktur.

## 2. Solusi

KantinKita dikembangkan sebagai aplikasi berbasis web untuk mendukung pengelolaan layanan kantin melalui sistem informasi.

## 3. Fitur Aplikasi

- Pengelolaan informasi menu.
- Pengelolaan pesanan.
- Pengelolaan antrean.
- Integrasi proses layanan kantin.

## 4. Teknologi yang Digunakan

- **PHP** – bahasa pemrograman backend.
- **Laravel** – framework pengembangan aplikasi web.
- **HTML** – menyusun struktur halaman web.
- **CSS** – mengatur tampilan halaman web.
- **JavaScript** – mendukung interaksi pada aplikasi.
- **Database** – menyimpan dan mengelola data aplikasi.
- **Git dan GitHub** – mengelola versi dan menyimpan kode proyek.

## 5. Hasil Proyek

KantinKita merupakan hasil penerapan pembelajaran Pemrograman Berbasis Web 2 dalam membangun aplikasi berbasis web.

## 6. Dokumentasi

Bagian ini berisi dokumentasi tampilan aplikasi KantinKita sebagai gambaran hasil pengembangan proyek.

### Tampilan Halaman Tentang

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/8cd95151-4061-4afc-8477-30e57d0ee658" />

### Tampilan Halaman Menu

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/b5f5e26b-e5f1-4d43-b549-72e263f17f9c" />

### Tampilan Halaman Antrean

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/2102cf88-e32c-4bc0-a537-bafe7c24bc86" />
## 7. Cara Menjalankan

Berikut langkah-langkah untuk menjalankan aplikasi KantinKita di lingkungan lokal:

1. Clone repository:
   ```bash
   git clone https://github.com/alfinadwi3105/KantinKita-Portfolio.git
   ```

2. Masuk ke folder proyek:
   ```bash
   cd KantinKita-Portfolio
   ```

3. Instal dependensi aplikasi:
   ```bash
   composer install
   ```

4. Salin file konfigurasi:
   ```bash
   copy .env.example .env
   ```

5. Buat application key:
   ```bash
   php artisan key:generate
   ```

6. Sesuaikan konfigurasi database pada file `.env` dengan pengaturan database lokal.

7. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```

8. Buka aplikasi melalui browser menggunakan alamat yang ditampilkan pada terminal.

**Catatan:** Proyek ini memiliki folder `backend` dan `service-kitchen` sebagai bagian terpisah yang memiliki konfigurasi dan langkah menjalankan masing-masing layanan.

## 8. Pengembang

**Alfina Dwi H**

Mahasiswa D3 Rekayasa Perangkat Lunak Aplikasi  
Universitas Telkom


# Praktikum Pemrograman Berbasis Web 2

Repositori ini dipakai **sepanjang satu semester**. Kamu meng-clone sekali, lalu
setiap praktikum dikerjakan di cabang (branch) baru di dalam repositori yang sama.

Program Studi D3 Rekayasa Perangkat Lunak Aplikasi — Telkom University

---

## 1. Isi repositori

| Lokasi | Isinya |
|---|---|
| `.github/student.json` | Identitasmu. **Wajib diisi** sebelum praktikum pertama. |
| `.github/workflows/test.yml` | Pengujian otomatis tiap kali kamu push ke branch `tpNN`. |
| `tests/Feature/TpNN/` | Berkas pengujian praktikum ke-NN. Folder baru muncul tiap pertemuan. |
| `app/`, `routes/`, `resources/views/` | Tempat kamu menulis kode. |

Folder `vendor/` dan berkas `.env` tidak ada di repositori — keduanya dibuat di
laptopmu pada langkah berikutnya.

---

## 2. Menyiapkan di laptop (sekali saja)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Buka <http://127.0.0.1:8000>. Kalau terbuka tanpa galat, lingkunganmu siap.

Lalu isi identitasmu di `.github/student.json`:

```json
{
    "nama": "Nama Lengkap Kamu",
    "nim": "6702234001",
    "kelas": "D3RPLA-3A"
}
```

Penilaian otomatis membaca berkas ini. Selama isinya masih teks contoh bawaan,
kriteria pertama tidak akan lolos.

---

## 3. Siklus tiap praktikum

Pola berikut **berulang setiap pertemuan**. Ganti `NN` dengan nomor praktikum
yang sedang berjalan.

### 3.1. Buat branch dari `main` terbaru

```bash
git checkout main
git pull origin main
git checkout -b tpNN
```

`main` adalah cabang resmi — isinya hanya pekerjaan yang sudah selesai. Semua
percobaan dilakukan di `tpNN`, sehingga `main` tetap utuh kalau pekerjaanmu kacau.

### 3.2. Kerjakan, lalu uji di laptop

```bash
php artisan test                       # seluruh kriteria
php artisan test tests/Feature/TpNN    # satu praktikum saja
php artisan test --filter=NamaKelasTes # satu kriteria saja
```

Baris hijau `PASS` berarti kriteria terpenuhi. Kalau merah, baca nama tes dan
pesan di bawahnya — di situ tertulis apa yang belum benar.

### 3.3. Commit bertahap

```bash
git add .
git commit -m "menambah BerandaController dan view beranda"
```

Commit setiap kali satu bagian selesai, jangan menumpuk di akhir. Tulis pesan
yang menjelaskan **apa yang berubah** — `update`, `fix`, atau `asdf` tidak
memberi informasi apa pun dan ikut dinilai.

### 3.4. Push ke GitHub

```bash
git push -u origin tpNN
```

Tanda `-u` cukup sekali per cabang; push berikutnya cukup `git push`.

### 3.5. Periksa tab Actions

Buka repositorimu di GitHub → tab **Actions**. Sebuah proses berjalan otomatis
dan berakhir dengan centang hijau atau silang merah.

Proses itu berjalan di komputer GitHub, bukan laptopmu. Centang hijau di sana
membuktikan kodemu berjalan di mesin mana pun — bukan sekadar "jalan di laptop
saya". Kalau merah, buka langkah yang gagal, baca baris galat paling bawah,
perbaiki, lalu push lagi. Boleh diulang sebanyak yang kamu perlukan.

### 3.6. Merge ke `main`

Nilai **baru dihitung** setelah pekerjaanmu masuk ke `main`.

1. Klik **Compare & pull request** di halaman repositori.
2. Pastikan arahnya: base `main`, compare `tpNN`.
3. Beri judul dan ringkasan singkat, lalu **Create pull request**.
4. Tunggu pemeriksaan otomatisnya hijau.
5. **Merge pull request** → pilih **Create a merge commit** → **Confirm merge**.

> **Pilih Create a merge commit.** Jangan Squash, jangan Rebase. Penilaian membaca
> riwayat Git untuk memastikan pekerjaanmu masuk lewat pull request, dan dua
> pilihan lain menghapus jejak itu.

Setelah merge, samakan salinan lokalmu — praktikum minggu depan dicabangkan dari
`main`:

```bash
git checkout main
git pull origin main
```

### 3.7. Lihat nilai

Beberapa menit setelah merge, nilaimu muncul di halaman Classroom 50 dan di
bagian atas repositorimu. Terakhir, tempel tautan repositori ini pada tugas di LMS.

---

## 4. Berkas pengujian

Berkas tes dikelompokkan per praktikum, dengan pola `Tp` + nomor praktikum +
nomor urut kriteria + nama singkat:

```
tests/Feature/
├── Tp01/Tp011SetupTest.php, Tp012MvcTest.php, ...
└── Tp02/Tp021...Test.php, ...
```

Angka urutnya membuat keluaran `php artisan test` mengikuti urutan modul.

Setiap pertemuan, berkas tes yang baru dibagikan lewat **LMS**. Ekstrak di akar
repositori supaya berkasnya masuk ke folder yang benar — **jangan taruh di folder
praktikum lain**, karena dua berkas dengan nama class yang sama membuat seluruh
pengujian gagal sebelum satu tes pun jalan.

Bacalah berkas-berkas ini — di situ tertulis persis apa yang diharapkan dari
pekerjaanmu. **Tetapi jangan mengubahnya.** Saat penilaian berjalan, berkas tes
diganti dengan versi resmi milik dosen, sehingga menyunting tes tidak menaikkan
nilai sama sekali.

---

## 5. Aturan yang tidak boleh dilanggar

- **Jangan meng-commit `.env`.** Isinya kunci aplikasi dan kata sandi database.
  Sekali ter-commit, rahasianya tetap tercatat di riwayat Git meskipun dihapus
  belakangan. Berkas ini sudah terdaftar di `.gitignore` — jangan keluarkan.
- **Jangan meng-commit `vendor/`.** Bisa diunduh ulang dengan `composer install`.
- **Jangan mengubah berkas di `tests/`.**
- **Jangan bekerja langsung di `main`.** Selalu lewat branch `tpNN`.
- **Pakai PHP 8.3.** Versi lain berisiko jalan di laptop tetapi gagal dinilai.

---

## 6. Batas waktu

Grading dikunci **Senin pagi**. Yang dihitung adalah isi branch `main` pada saat
itu. Sebelum batas tersebut kamu bebas memperbaiki: commit baru di `tpNN`, push,
lalu merge lagi.

---

## 7. Kalau bermasalah

| Gejala | Solusi |
|---|---|
| `composer install` menolak dipasang | Versi PHP aktif bukan 8.3. Samakan lewat Laragon → PHP → Version. |
| `No application encryption key` | Jalankan `php artisan key:generate` setelah menyalin `.env`. |
| `View [nama] not found` | Nama berkas harus `nama.blade.php` di `resources/views`. |
| `Undefined variable` di view | Nama variabel di controller dan di view harus sama
