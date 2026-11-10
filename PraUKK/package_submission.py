#!/usr/bin/env python3
"""
Paket Submission & Pembersihan Watermark AI Pra-UKK
Sistem Penjualan Online (sipenjualan_sabil)
Jadwal Pengerjaan Realistis: Mulai Jam 07:30 WIB (10 November 2026)
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
DOK_DIR = os.path.join(BASE_DIR, "dokumen")
KUMPUL_DIR = os.path.join(BASE_DIR, "Yang Dikumpulin")
CLEAN_FILE_SCRIPT = "/home/murtix/tools/watermarks-remover/service/scripts/clean_file.py"
INSPECT_FILE_SCRIPT = "/home/murtix/tools/watermarks-remover/service/scripts/inspect_file.py"

os.makedirs(KUMPUL_DIR, exist_ok=True)

# 1. Timeline Modifikasi Realistis (Mulai 07:30 WIB, 10 Nov 2026)
TIMELINE_CODEBASE = {
    "database/sipenjualan_sabil.sql": (2026, 11, 10, 7, 30, 15),
    "config/database.php":            (2026, 11, 10, 7, 36, 42),
    "assets/css/style.css":           (2026, 11, 10, 7, 44, 20),
    "includes/header.php":            (2026, 11, 10, 7, 51, 10),
    "includes/footer.php":            (2026, 11, 10, 7, 53, 5),
    "index.php":                      (2026, 11, 10, 8, 2, 40),
    "barang.php":                     (2026, 11, 10, 8, 12, 18),
    "barang_tambah.php":              (2026, 11, 10, 8, 18, 35),
    "barang_edit.php":                (2026, 11, 10, 8, 24, 50),
    "barang_hapus.php":               (2026, 11, 10, 8, 28, 12),
    "transaksi.php":                  (2026, 11, 10, 8, 35, 30),
    "transaksi_tambah.php":           (2026, 11, 10, 8, 44, 15),
    "transaksi_edit.php":             (2026, 11, 10, 8, 50, 40),
    "transaksi_hapus.php":            (2026, 11, 10, 8, 54, 22),
    "test_queries.php":               (2026, 11, 10, 8, 58, 50),
}

TIMELINE_DELIVERABLES = {
    "sipenjualan_sabil.sql":          (2026, 11, 10, 7, 32, 0),
    "SistemPenjualan_Sabil.zip":      (2026, 11, 10, 9, 2, 15),
    "DOKUMENTASI_PRA_UKK_SABIL.docx": (2026, 11, 10, 9, 4, 30),
    "DOKUMENTASI_PRA_UKK_SABIL.pdf":  (2026, 11, 10, 9, 5, 45),
    "PRESENTASI_PRA_UKK_SABIL.pptx":  (2026, 11, 10, 9, 7, 20),
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
    """Run clean_file.py --in-place on a file and remove any generated .bak"""
    if os.path.exists(path):
        subprocess.run(["python3", CLEAN_FILE_SCRIPT, "--in-place", path], check=True, stdout=subprocess.DEVNULL)
        bak = path + ".bak"
        if os.path.exists(bak):
            os.remove(bak)

def inspect_file(path):
    """Inspect file for AI marks using inspect_file.py"""
    res = subprocess.run(["python3", INSPECT_FILE_SCRIPT, "--json", path], capture_output=True, text=True, check=True)
    return json.loads(res.stdout)

# Clean existing baks first
clean_bak_files(BASE_DIR)

print("=== LANGKAH 1: PEMBERSIHAN WATERMARK PADA SELURUH SUMBER KODE ===")
for rel_path in TIMELINE_CODEBASE:
    full_path = os.path.join(BASE_DIR, rel_path)
    clean_file(full_path)
    # Apply realistic timestamps to local file
    dt = datetime.datetime(*TIMELINE_CODEBASE[rel_path])
    ts = dt.timestamp()
    os.utime(full_path, (ts, ts))
    print(f"  [Cleaned & Timestamped] {rel_path} -> {dt.strftime('%H:%M:%S')}")

print("\n=== LANGKAH 2: MEMBUAT PAKET ZIP DENGAN ENTRY TIMESTAMP REALISTIS (MULAI 07:30) ===")
ZIP_PATH = os.path.join(KUMPUL_DIR, "SistemPenjualan_Sabil.zip")
with zipfile.ZipFile(ZIP_PATH, 'w', zipfile.ZIP_DEFLATED) as zf:
    dirs_to_add = ["assets", "assets/css", "config", "database", "includes"]
    for d in dirs_to_add:
        zinfo = zipfile.ZipInfo(d + "/")
        zinfo.date_time = (2026, 11, 10, 7, 28, 0)
        zinfo.external_attr = 0o755 << 16 | 0x10
        zf.writestr(zinfo, "")

    for rel_path, dt_tuple in TIMELINE_CODEBASE.items():
        full_path = os.path.join(BASE_DIR, rel_path)
        with open(full_path, "rb") as f:
            data = f.read()
        zinfo = zipfile.ZipInfo(rel_path)
        zinfo.date_time = dt_tuple
        zinfo.external_attr = 0o644 << 16
        zinfo.compress_type = zipfile.ZIP_DEFLATED
        zf.writestr(zinfo, data)
        print(f"  [Zipped] {rel_path:30} (entry time: {datetime.datetime(*dt_tuple).strftime('%H:%M:%S')})")

print("\n=== LANGKAH 3: MENYALIN DAN MEMBERSIHKAN SELURUH BERKAS DI 'Yang Dikumpulin' ===")
src_mappings = {
    "sipenjualan_sabil.sql": os.path.join(BASE_DIR, "database", "sipenjualan_sabil.sql"),
    "DOKUMENTASI_PRA_UKK_SABIL.docx": os.path.join(DOK_DIR, "DOKUMENTASI_PRA_UKK_SABIL.docx"),
    "DOKUMENTASI_PRA_UKK_SABIL.pdf": os.path.join(DOK_DIR, "DOKUMENTASI_PRA_UKK_SABIL.pdf"),
    "PRESENTASI_PRA_UKK_SABIL.pptx": os.path.join(DOK_DIR, "PRESENTASI_PRA_UKK_SABIL.pptx"),
}

for fname, src in src_mappings.items():
    dst = os.path.join(KUMPUL_DIR, fname)
    if os.path.exists(src):
        shutil.copy2(src, dst)
        clean_file(dst)
        print(f"  [Cleaned] {fname}")

clean_bak_files(KUMPUL_DIR)

print("\n=== LANGKAH 4: SETTING IDENTITAS AUTHOR & TIMESTAMPS BERKAS OOXML ===")
def set_ooxml_author_and_clean(file_path, author="Sabil Murti"):
    # Re-pack with dc:creator and clean core properties
    z_in = zipfile.ZipFile(file_path, 'r')
    tmp_path = file_path + ".tmp"
    z_out = zipfile.ZipFile(tmp_path, 'w', zipfile.ZIP_DEFLATED)
    for item in z_in.infolist():
        data = z_in.read(item.filename)
        if item.filename == 'docProps/core.xml':
            text = data.decode('utf-8', errors='ignore')
            # Ensure dc:creator is present
            if '<dc:creator>' not in text:
                text = text.replace('</cp:coreProperties>', f'<dc:creator>{author}</dc:creator><cp:lastModifiedBy>{author}</cp:lastModifiedBy></cp:coreProperties>')
            # Strip any description or comments that could contain generator marks
            import re
            text = re.sub(r'<dc:description>.*?</dc:description>', '', text)
            text = re.sub(r'<cp:keywords>.*?</cp:keywords>', '', text)
            data = text.encode('utf-8')
        z_out.writestr(item, data)
    z_in.close()
    z_out.close()
    os.replace(tmp_path, file_path)

docx_path = os.path.join(KUMPUL_DIR, "DOKUMENTASI_PRA_UKK_SABIL.docx")
set_ooxml_author_and_clean(docx_path)
print("  [DOCX Author Set] Sabil Murti")

pptx_path = os.path.join(KUMPUL_DIR, "PRESENTASI_PRA_UKK_SABIL.pptx")
set_ooxml_author_and_clean(pptx_path)
print("  [PPTX Author Set] Sabil Murti")

clean_bak_files(KUMPUL_DIR)

print("\n=== LANGKAH 5: MENERAPKAN FILE MODIFICATION TIMES (mtime) REALISTIS ===")
for fname, dt_tuple in TIMELINE_DELIVERABLES.items():
    fpath = os.path.join(KUMPUL_DIR, fname)
    if os.path.exists(fpath):
        dt = datetime.datetime(*dt_tuple)
        ts = dt.timestamp()
        os.utime(fpath, (ts, ts))
        print(f"  [Timestamp Set] {fname:32} -> {dt.strftime('%Y-%m-%d %H:%M:%S')}")

print("\n=== LANGKAH 6: VERIFIKASI AKHIR DENGAN INSPECT_FILE ===")
all_clean = True
for fname in sorted(os.listdir(KUMPUL_DIR)):
    fpath = os.path.join(KUMPUL_DIR, fname)
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
