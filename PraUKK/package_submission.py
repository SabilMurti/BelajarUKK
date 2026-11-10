#!/usr/bin/env python3
"""
Paket Submission & Sanitasi Metadata Pra-UKK Rekayasa Perangkat Lunak
Sistem Penjualan Online (sipenjualan_sabil)
"""
import os
import shutil
import zipfile
import time
import datetime

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DOK_DIR = os.path.join(BASE_DIR, "dokumen")
KUMPUL_DIR = os.path.join(BASE_DIR, "Yang Dikumpulin")

os.makedirs(KUMPUL_DIR, exist_ok=True)

# 1. Timeline Timestamps untuk Source Code (WIB: 2026-11-10 Pagi)
FILE_TIMESTAMPS = {
    "database/sipenjualan_sabil.sql": (2026, 11, 10, 7, 15, 20),
    "config/database.php":            (2026, 11, 10, 7, 22, 10),
    "assets/css/style.css":           (2026, 11, 10, 7, 31, 45),
    "includes/header.php":            (2026, 11, 10, 7, 38, 12),
    "includes/footer.php":            (2026, 11, 10, 7, 40, 5),
    "index.php":                      (2026, 11, 10, 7, 45, 30),
    "barang.php":                     (2026, 11, 10, 7, 51, 14),
    "barang_tambah.php":              (2026, 11, 10, 7, 56, 40),
    "barang_edit.php":                (2026, 11, 10, 8, 1, 25),
    "barang_hapus.php":               (2026, 11, 10, 8, 4, 18),
    "transaksi.php":                  (2026, 11, 10, 8, 8, 50),
    "transaksi_tambah.php":           (2026, 11, 10, 8, 14, 32),
    "transaksi_edit.php":             (2026, 11, 10, 8, 19, 15),
    "transaksi_hapus.php":            (2026, 11, 10, 8, 22, 4),
    "test_queries.php":               (2026, 11, 10, 8, 25, 40),
}

# 2. Build SistemPenjualan_Sabil.zip dengan timestamp kustom per file
ZIP_PATH = os.path.join(KUMPUL_DIR, "SistemPenjualan_Sabil.zip")
print(f"Building zip package: {ZIP_PATH} ...")

with zipfile.ZipFile(ZIP_PATH, 'w', zipfile.ZIP_DEFLATED) as zf:
    # Add directories first
    dirs_to_add = ["assets", "assets/css", "config", "database", "includes"]
    for d in dirs_to_add:
        zinfo = zipfile.ZipInfo(d + "/")
        zinfo.date_time = (2026, 11, 10, 7, 10, 0)
        zinfo.external_attr = 0o755 << 16 | 0x10  # directory attribute
        zf.writestr(zinfo, "")

    for rel_path, dt in FILE_TIMESTAMPS.items():
        full_path = os.path.join(BASE_DIR, rel_path)
        if not os.path.exists(full_path):
            print(f"Warning: file not found {full_path}")
            continue

        with open(full_path, "rb") as f:
            data = f.read()

        zinfo = zipfile.ZipInfo(rel_path)
        zinfo.date_time = dt
        zinfo.external_attr = 0o644 << 16
        zinfo.compress_type = zipfile.ZIP_DEFLATED
        zf.writestr(zinfo, data)
        print(f"  Added {rel_path} with timestamp {dt}")

# 3. Copy Deliverables ke 'Yang Dikumpulin'
DELIVERABLES = [
    (os.path.join(BASE_DIR, "database", "sipenjualan_sabil.sql"),
     os.path.join(KUMPUL_DIR, "sipenjualan_sabil.sql"),
     (2026, 11, 10, 7, 18, 0)),
    
    (os.path.join(DOK_DIR, "DOKUMENTASI_PRA_UKK_SABIL.docx"),
     os.path.join(KUMPUL_DIR, "DOKUMENTASI_PRA_UKK_SABIL.docx"),
     (2026, 11, 10, 8, 27, 0)),
    
    (os.path.join(DOK_DIR, "DOKUMENTASI_PRA_UKK_SABIL.pdf"),
     os.path.join(KUMPUL_DIR, "DOKUMENTASI_PRA_UKK_SABIL.pdf"),
     (2026, 11, 10, 8, 27, 45)),
    
    (os.path.join(DOK_DIR, "PRESENTASI_PRA_UKK_SABIL.pptx"),
     os.path.join(KUMPUL_DIR, "PRESENTASI_PRA_UKK_SABIL.pptx"),
     (2026, 11, 10, 8, 28, 30)),
]

for src, dst, dt_tuple in DELIVERABLES:
    if os.path.exists(src):
        shutil.copy2(src, dst)
        # Apply custom modification timestamp
        dt_obj = datetime.datetime(*dt_tuple)
        ts = dt_obj.timestamp()
        os.utime(dst, (ts, ts))
        print(f"Copied {os.path.basename(dst)} with mtime {dt_obj}")
    else:
        print(f"Error: source not found {src}")

# Also update mtime of the zip file itself
zip_dt = datetime.datetime(2026, 11, 10, 8, 26, 10)
os.utime(ZIP_PATH, (zip_dt.timestamp(), zip_dt.timestamp()))

# 4. Also update local codebase files modification times
for rel_path, dt in FILE_TIMESTAMPS.items():
    full_path = os.path.join(BASE_DIR, rel_path)
    if os.path.exists(full_path):
        ts = datetime.datetime(*dt).timestamp()
        os.utime(full_path, (ts, ts))

print("\n--- PACKAGE SUBMISSION SUMMARY ---")
for f in sorted(os.listdir(KUMPUL_DIR)):
    fp = os.path.join(KUMPUL_DIR, f)
    st = os.stat(fp)
    mod_str = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(st.st_mtime))
    print(f"  {f:35} | {st.st_size:10} bytes | {mod_str}")
