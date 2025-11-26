# BIM Cost Estimator - 5D BIM Web Application

Aplikasi estimasi biaya konstruksi berbasis web yang mengintegrasikan **Model 3D (BIM)** dari Autodesk Revit dengan **Analisa Harga Satuan Pekerjaan (AHSP)** secara otomatis.

Aplikasi ini menerapkan konsep **5D BIM** (3D Geometry + Cost), memungkinkan Quantity Surveyor (QS) dan Engineer untuk menghitung RAB (Rencana Anggaran Biaya) langsung dari metadata model tanpa perhitungan manual.

## 🌟 Fitur Utama

### 1. Autodesk Platform Services (APS) Integration
* **OAuth2 Authentication:** Login aman menggunakan akun Autodesk.
* **Data Extraction:** Mengambil metadata (Volume, Parameter, Assembly Code) dari file Revit (.rvt) secara otomatis di background (Queue Jobs).
* **3D Viewer (Forge):** Visualisasi model 3D di browser tanpa install software tambahan.

### 2. Federated Model Management (Multi-File)
* Mendukung struktur proyek kompleks yang terdiri dari banyak file (contoh: Arsitektur.rvt + Struktur.rvt + MEP.rvt).
* Setiap file dikelola dan divisualisasikan secara independen namun terikat dalam satu Header Project.

### 3. Cost Database Engine (AHSP)
* **Master Library:** Manajemen database harga yang fleksibel (bisa buat Library SNI 2024, Harga Proyek A, Harga Proyek B).
* **AHSP Builder:** Fitur peracikan harga satuan (Analisa) dengan sistem Koefisien (Bahan/Upah x Koefisien).
* **Resource Manager:** Manajemen harga dasar bahan dan upah.
* **Excel Import:** Dukungan import massal data AHSP dan Resources dari Excel.

### 4. Automated Cost Estimation
* **Auto-Matching:** Algoritma pencocokan otomatis berdasarkan `Assembly Code` (Keynote) pada elemen Revit dengan Kode AHSP di database.
* **Unassigned Detection:** Mendeteksi elemen yang belum memiliki harga.

### 5. Smart Dashboard & Reporting
* **Visual Cost Feedback:** Model 3D berwarna Hijau (Sudah dihitung) dan Merah (Belum ada harga).
* **Interactive Tooltip:** Menampilkan detail AHSP dan Harga saat mouse diarahkan ke objek 3D.
* **Export to Excel:** Download Laporan BOQ (Bill of Quantities) lengkap dengan format standar tender (Rekap & Detail).

---

## 🚀 Alur Kerja (Workflow)

1.  **Connect ACC:** Hubungkan akun Autodesk Construction Cloud (ACC/BIM 360).
2.  **Prepare Library:** Siapkan database harga. Bisa import dari Excel atau buat manual di AHSP Builder.
3.  **Create Project:** Buat wadah proyek dan pilih Library harga yang akan digunakan.
4.  **Link Files:** Import file Revit dari ACC ke dalam proyek. Sistem akan otomatis menarik volume.
5.  **Calculate:** Klik tombol hitung. Sistem akan mencocokkan kode dan volume.
6.  **Visualize & Report:** Buka Dashboard 3D untuk audit visual, lalu Export Excel untuk laporan resmi.

---

## 🛠️ Teknologi

* **Backend:** Laravel 10
* **Frontend:** Livewire 3 + Tailwind CSS
* **3D Engine:** Autodesk Viewer API v7
* **Database:** MySQL
* **Queue:** Database Driver (untuk proses background import)

---

## 📋 Prasyarat Pemodelan (Revit)

Agar automasi berjalan, model Revit harus mengikuti standar berikut:
* Setiap elemen (Dinding, Lantai, Kolom, dll) wajib memiliki parameter **Assembly Code** atau **Keynote**.
* Kode tersebut harus sama persis dengan Kode AHSP di database aplikasi (Contoh: `C2010`, `A-105`).

---

&copy; 2025 BIM Cost Estimator.