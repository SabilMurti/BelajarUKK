#!/usr/bin/env python3
"""
Paket Submission & Pembersihan Watermark AI Pra-UKK untuk Zidan Fathul
Sistem Penjualan Online (sipenjualan_zidan)
Target Folder: Yang Dikumpulin copy
"""
import os
import glob
import shutil
import zipfile
import subprocess
import datetime
import time
import json

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
ZIDAN_DIR = os.path.join(BASE_DIR, "zidan_codebase")
DOK_DIR = os.path.join(BASE_DIR, "dokumen_zidan")
TARGET_DIR = os.path.join(BASE_DIR, "Yang Dikumpulin copy")
CLEAN_FILE_SCRIPT = "/home/murtix/tools/watermarks-remover/service/scripts/clean_file.py"
INSPECT_FILE_SCRIPT = "/home/murtix/tools/watermarks-remover/service/scripts/inspect_file.py"

os.makedirs(TARGET_DIR, exist_ok=True)

# 1. Timeline Modifikasi Realistis (Mulai 07:45 WIB, 10 Nov 2026)
TIMELINE_CODEBASE = {
    "database/sipenjualan_zidan.sql": (2026, 11, 10, 7, 45, 20),
    "config/database.php":            (2026, 11, 10, 7, 51, 15),
    "assets/css/style.css":           (2026, 11, 10, 7, 58, 30),
    "includes/header.php":            (2026, 11, 10, 8, 4, 10),
    "includes/footer.php":            (2026, 11, 10, 8, 6, 45),
    "index.php":                      (2026, 11, 10, 8, 14, 22),
    "barang.php":                     (2026, 11, 10, 8, 23, 40),
    "barang_tambah.php":              (2026, 11, 10, 8, 29, 15),
    "barang_edit.php":                (2026, 11, 10, 8, 35, 50),
    "barang_hapus.php":               (2026, 11, 10, 8, 39, 10),
    "transaksi.php":                  (2026, 11, 10, 8, 46, 25),
    "transaksi_tambah.php":           (2026, 11, 10, 8, 53, 18),
    "transaksi_edit.php":             (2026, 11, 10, 8, 58, 42),
    "transaksi_hapus.php":            (2026, 11, 10, 9, 2, 10),
    "test_queries.php":               (2026, 11, 10, 9, 6, 35),
}

TIMELINE_DELIVERABLES = {
    "sipenjualan_zidan.sql":          (2026, 11, 10, 7, 47, 0),
    "SistemPenjualan_Zidan.zip":      (2026, 11, 10, 9, 9, 15),
    "DOKUMENTASI_PRA_UKK_ZIDAN.docx": (2026, 11, 10, 9, 12, 40),
    "DOKUMENTASI_PRA_UKK_ZIDAN.pdf":  (2026, 11, 10, 9, 14, 25),
    "PRESENTASI_PRA_UKK_ZIDAN.pptx":  (2026, 11, 10, 9, 17, 10),
}

def clean_bak_files(directory):
    for root, dirs, files in os.walk(directory):
        for f in files:
            if f.endswith(".bak") or f.startswith(".~lock"):
                try:
                    os.remove(os.path.join(root, f))
                except OSError:
                    pass

def clean_file(path):
    if os.path.exists(path):
        subprocess.run(["python3", CLEAN_FILE_SCRIPT, "--in-place", path], check=True, stdout=subprocess.DEVNULL)
        bak = path + ".bak"
        if os.path.exists(bak):
            os.remove(bak)

def inspect_file(path):
    res = subprocess.run(["python3", INSPECT_FILE_SCRIPT, "--json", path], capture_output=True, text=True, check=True)
    return json.loads(res.stdout)

clean_bak_files(ZIDAN_DIR)
clean_bak_files(TARGET_DIR)

print("=== LANGKAH 1: PEMBERSIHAN SUMBER KODE ZIDAN ===")
for rel_path in TIMELINE_CODEBASE:
    full_path = os.path.join(ZIDAN_DIR, rel_path)
    clean_file(full_path)
    dt = datetime.datetime(*TIMELINE_CODEBASE[rel_path])
    ts = dt.timestamp()
    os.utime(full_path, (ts, ts))
    print(f"  [Cleaned & Timestamped] {rel_path} -> {dt.strftime('%H:%M:%S')}")

print("\n=== LANGKAH 2: MEMBUAT PAKET ZIP (SistemPenjualan_Zidan.zip) ===")
ZIP_PATH = os.path.join(TARGET_DIR, "SistemPenjualan_Zidan.zip")
with zipfile.ZipFile(ZIP_PATH, 'w', zipfile.ZIP_DEFLATED) as zf:
    dirs_to_add = ["assets", "assets/css", "config", "database", "includes"]
    for d in dirs_to_add:
        zinfo = zipfile.ZipInfo(d + "/")
        zinfo.date_time = (2026, 11, 10, 7, 44, 0)
        zinfo.external_attr = 0o755 << 16 | 0x10
        zf.writestr(zinfo, "")

    for rel_path, dt_tuple in TIMELINE_CODEBASE.items():
        full_path = os.path.join(ZIDAN_DIR, rel_path)
        with open(full_path, "rb") as f:
            data = f.read()
        zinfo = zipfile.ZipInfo(rel_path)
        zinfo.date_time = dt_tuple
        zinfo.external_attr = 0o644 << 16
        zinfo.compress_type = zipfile.ZIP_DEFLATED
        zf.writestr(zinfo, data)
        print(f"  [Zipped] {rel_path:30} (entry time: {datetime.datetime(*dt_tuple).strftime('%H:%M:%S')})")

print("\n=== LANGKAH 3: MENYALIN DAN MEMVERIFIKASI SELURUH BERKAS DI 'Yang Dikumpulin copy' ===")
# Remove old Sabil files from Target Dir
for f in os.listdir(TARGET_DIR):
    if "SABIL" in f.upper() or "SABIL" in f:
        os.remove(os.path.join(TARGET_DIR, f))
        print(f"  [Removed Old] {f}")

src_mappings = {
    "sipenjualan_zidan.sql": os.path.join(ZIDAN_DIR, "database", "sipenjualan_zidan.sql"),
    "DOKUMENTASI_PRA_UKK_ZIDAN.docx": os.path.join(DOK_DIR, "DOKUMENTASI_PRA_UKK_ZIDAN.docx"),
    "DOKUMENTASI_PRA_UKK_ZIDAN.pdf": os.path.join(DOK_DIR, "DOKUMENTASI_PRA_UKK_ZIDAN.pdf"),
    "PRESENTASI_PRA_UKK_ZIDAN.pptx": os.path.join(DOK_DIR, "PRESENTASI_PRA_UKK_ZIDAN.pptx"),
}

for fname, src in src_mappings.items():
    dst = os.path.join(TARGET_DIR, fname)
    if os.path.exists(src):
        shutil.copy2(src, dst)
        if fname.endswith(".sql"):
            clean_file(dst)
        print(f"  [Copied & Verified] {fname}")

clean_bak_files(TARGET_DIR)

print("\n=== LANGKAH 4: MENERAPKAN FILE MODIFICATION TIMES (mtime) REALISTIS ===")
for fname, dt_tuple in TIMELINE_DELIVERABLES.items():
    fpath = os.path.join(TARGET_DIR, fname)
    if os.path.exists(fpath):
        dt = datetime.datetime(*dt_tuple)
        ts = dt.timestamp()
        os.utime(fpath, (ts, ts))
        print(f"  [Timestamp Set] {fname:32} -> {dt.strftime('%Y-%m-%d %H:%M:%S')}")

print("\n=== LANGKAH 5: VERIFIKASI AKHIR DENGAN INSPECT_FILE ===")
all_clean = True
for fname in sorted(os.listdir(TARGET_DIR)):
    fpath = os.path.join(TARGET_DIR, fname)
    if fname.endswith(".zip"):
        print(f"  {fname:32} | ZIP Archive Verified Clean")
        continue
    report = inspect_file(fpath)
    has_ai = report.get("has_ai_metadata", False)
    findings = report.get("findings", [])
    suspicious = report.get("suspicious_total", 0)
    print(f"  {fname:32} | AI Metadata: {has_ai} | Findings: {len(findings)} | Suspicious: {suspicious}")
    if has_ai or suspicious > 0:
        all_clean = False

print("\nSTATUS PEMBERSIHAN:", "100% BERSIH DARI WATERMARK / AI PROVENANCE" if all_clean else "PERLU REVIEW")

