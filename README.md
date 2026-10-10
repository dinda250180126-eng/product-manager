# Praktikum 3: Mini Project - K-Pop Merchandise Manager

Aplikasi web manajemen inventaris merchandise K-Pop sederhana berbasis **PHP murni** dan database **MySQL (PDO)**. Aplikasi ini menerapkan pola arsitektur aman (*Prepared Statements*, *Output Escaping* terhadap XSS, proteksi *CSRF Token*), pola navigasi *Post-Redirect-Get* (PRG), tata letak responsif menggunakan *CSS Flexbox*, serta fitur pencarian data (*Search/Filter*).

---

## 📌 Identitas Pengembang
* **Nama** : [Dinda Mutiara]
* **NIM**  : [250180126]
* **Kelas**: [A3]

---

## 📁 Struktur Direktori Proyek

Sesuai spesifikasi struktur modul pada Slide 16 & 20:

```text
product-manager/
├── config/
│   └── db.php              # Inisialisasi PDO, session, dan token CSRF
├── database/
│   └── store_db.sql        # Skema tabel products dan seed data awal
├── public/
│   ├── assets/
│   │   └── style.css       # Styling Box Model & Flexbox responsif
│   ├── index.php           # READ katalog produk + Fitur Pencarian (GET)
│   ├── create.php          # CREATE produk baru + Validasi server-side + Pola PRG
│   ├── edit.php            # READ by ID + UPDATE produk + Pola PRG
│   └── delete.php          # DELETE produk (Wajib POST + CSRF Protection)
└── README.md               # Dokumentasi cara menjalankan dan checklist uji
