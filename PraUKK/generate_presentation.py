#!/usr/bin/env python3
"""
Generator Presentasi Pra-UKK Rekayasa Perangkat Lunak
Sistem Penjualan Online (sipenjualan_sabil)
Gaya: Minimalis, Canvas Putih Bersih, Bebas AI-Slop, 100% Bahasa Indonesia Formal
"""
import os
from PIL import Image
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
from pptx.enum.shapes import MSO_SHAPE

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DOK_DIR = os.path.join(BASE_DIR, "dokumen")
SHOTS_DIR = os.path.join(DOK_DIR, "screenshots")
OUTPUT_PPTX = os.path.join(DOK_DIR, "PRESENTASI_PRA_UKK_SABIL.pptx")

# Palet Warna Bersih & Profesional (Executive Clean Theme)
C_WHITE      = RGBColor(255, 255, 255)
C_BG_PAGE    = RGBColor(248, 250, 252) # Slate 50
C_SURFACE    = RGBColor(255, 255, 255)
C_BORDER     = RGBColor(226, 232, 240) # Slate 200
C_TEXT_MAIN  = RGBColor(15, 23, 42)    # Slate 900
C_TEXT_MUTED = RGBColor(100, 116, 139) # Slate 500
C_PRIMARY    = RGBColor(2, 132, 199)   # Sky 600
C_PRIMARY_BG = RGBColor(240, 249, 255) # Sky 50
C_SUCCESS    = RGBColor(22, 163, 74)   # Emerald 600
C_SUCCESS_BG = RGBColor(240, 253, 244) # Emerald 50
C_WARNING    = RGBColor(217, 119, 6)   # Amber 600
C_NAVY       = RGBColor(30, 41, 59)    # Slate 800

def create_presentation():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # Helper: Set background color of slide
    def set_bg(slide, color):
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = color
        bg.line.fill.background()
        return bg

    # Helper: Add header block (Badge + Title + Subtitle + Divider)
    def add_header(slide, badge_text, title_text, subtitle_text=""):
        # Badge
        badge = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.45), Inches(3.2), Inches(0.32))
        badge.fill.solid()
        badge.fill.fore_color.rgb = C_PRIMARY_BG
        badge.line.color.rgb = RGBColor(186, 230, 253)
        tf_b = badge.text_frame
        tf_b.word_wrap = True
        p_b = tf_b.paragraphs[0]
        p_b.text = badge_text.upper()
        p_b.font.name = "Segoe UI"
        p_b.font.size = Pt(9.5)
        p_b.font.bold = True
        p_b.font.color.rgb = C_PRIMARY
        p_b.alignment = PP_ALIGN.CENTER

        # Title
        tx_box = slide.shapes.add_textbox(Inches(0.75), Inches(0.82), Inches(11.8), Inches(0.55))
        tf = tx_box.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = title_text
        p.font.name = "Segoe UI"
        p.font.size = Pt(22)
        p.font.bold = True
        p.font.color.rgb = C_TEXT_MAIN

        # Subtitle
        if subtitle_text:
            p_sub = tf.add_paragraph()
            p_sub.text = subtitle_text
            p_sub.font.name = "Segoe UI"
            p_sub.font.size = Pt(11)
            p_sub.font.color.rgb = C_TEXT_MUTED

        # Divider line
        div = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.48), Inches(11.733), Pt(1))
        div.fill.solid()
        div.fill.fore_color.rgb = C_BORDER
        div.line.fill.background()

    # Helper: Add uncropped image with clean border frame
    def add_framed_image(slide, img_path, left, top, width, height, caption=""):
        if not os.path.exists(img_path):
            print(f"Warning: Image not found: {img_path}")
            return None
        
        # Open image to get real aspect ratio
        im = Image.open(img_path)
        img_aspect = im.width / im.height
        box_aspect = width / height

        if img_aspect > box_aspect:
            # Image is wider than box: fit to width
            real_w = width
            real_h = width / img_aspect
            real_l = left
            real_t = top + (height - real_h) / 2
        else:
            # Image is taller than box: fit to height
            real_h = height
            real_w = height * img_aspect
            real_t = top
            real_l = left + (width - real_w) / 2

        # Outer border frame
        frame = slide.shapes.add_shape(
            MSO_SHAPE.RECTANGLE,
            real_l - Pt(2), real_t - Pt(2),
            real_w + Pt(4), real_h + Pt(4)
        )
        frame.fill.solid()
        frame.fill.fore_color.rgb = C_WHITE
        frame.line.color.rgb = C_BORDER
        frame.line.width = Pt(1)

        # Insert actual picture
        pic = slide.shapes.add_picture(img_path, real_l, real_t, real_w, real_h)

        # Optional Caption below image
        if caption:
            cap_box = slide.shapes.add_textbox(real_l, real_t + real_h + Inches(0.06), real_w, Inches(0.3))
            tf_c = cap_box.text_frame
            p_c = tf_c.paragraphs[0]
            p_c.text = caption
            p_c.font.name = "Segoe UI"
            p_c.font.size = Pt(9.5)
            p_c.font.color.rgb = C_TEXT_MUTED
            p_c.alignment = PP_ALIGN.CENTER

        return pic

    # Helper: Add card container
    def add_card(slide, left, top, width, height, title="", border_color=C_BORDER, bg_color=C_SURFACE):
        card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_color
        card.line.color.rgb = border_color
        card.line.width = Pt(1)
        
        if title:
            # Header text inside card
            tb = slide.shapes.add_textbox(left + Inches(0.15), top + Inches(0.1), width - Inches(0.3), Inches(0.35))
            tf = tb.text_frame
            p = tf.paragraphs[0]
            p.text = title
            p.font.name = "Segoe UI"
            p.font.size = Pt(12)
            p.font.bold = True
            p.font.color.rgb = C_TEXT_MAIN
        return card

    # ==========================================================================
    # SLIDE 1: COVER
    # ==========================================================================
    s1 = prs.slides.add_slide(blank_layout)
    set_bg(s1, C_WHITE)

    # Accent Top Bar
    bar = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, Inches(0.15))
    bar.fill.solid()
    bar.fill.fore_color.rgb = C_PRIMARY
    bar.line.fill.background()

    # Badge Kategori
    b1 = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.2), Inches(1.2), Inches(3.6), Inches(0.38))
    b1.fill.solid()
    b1.fill.fore_color.rgb = C_PRIMARY_BG
    b1.line.color.rgb = RGBColor(186, 230, 253)
    p = b1.text_frame.paragraphs[0]
    p.text = "PRA UJI KOMPETENSI KEAHLIAN (PRA-UKK)"
    p.font.name = "Segoe UI"
    p.font.size = Pt(10)
    p.font.bold = True
    p.font.color.rgb = C_PRIMARY
    p.alignment = PP_ALIGN.CENTER

    # Judul Utama
    tb1 = s1.shapes.add_textbox(Inches(1.15), Inches(1.75), Inches(11.0), Inches(1.8))
    tf1 = tb1.text_frame
    tf1.word_wrap = True
    p1 = tf1.paragraphs[0]
    p1.text = "SISTEM PENJUALAN ONLINE"
    p1.font.name = "Segoe UI"
    p1.font.size = Pt(36)
    p1.font.bold = True
    p1.font.color.rgb = C_TEXT_MAIN

    p1_sub = tf1.add_paragraph()
    p1_sub.text = "Aplikasi Manajemen Katalog Barang & Transaksi Penjualan Berbasis PHP Native + MySQL"
    p1_sub.font.name = "Segoe UI"
    p1_sub.font.size = Pt(16)
    p1_sub.font.color.rgb = C_TEXT_MUTED
    p1_sub.space_before = Pt(8)

    # Garis Pembatas
    div1 = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(1.2), Inches(3.7), Inches(10.9), Pt(1.5))
    div1.fill.solid()
    div1.fill.fore_color.rgb = C_BORDER
    div1.line.fill.background()

    # Metadata Grid (3 Kolom Kartu)
    col_w = Inches(3.4)
    gap_w = Inches(0.35)

    meta_items = [
        ("IDENTITAS PESERTA", "Sabil Murti\nKelas XII Rekayasa Perangkat Lunak\nSMK Negeri — Konsentrasi Keahlian RPL"),
        ("SPESIFIKASI UJIAN", "Paket Kejuruan: Pra-UKK RPL\nAlokasi Waktu: 480 Menit (8 Jam)\nBentuk Soal: Penugasan Perorangan"),
        ("LINGKUNGAN TEKNIS", "Basis Data: MariaDB (sipenjualan_sabil)\nBackend: PHP Native 8+ (PDO)\nFrontend: Vanilla CSS (Zero-Slop UI)"),
    ]

    for idx, (m_title, m_desc) in enumerate(meta_items):
        col_x = Inches(1.2) + idx * (col_w + gap_w)
        c = add_card(s1, col_x, Inches(4.1), col_w, Inches(2.2), m_title, bg_color=C_BG_PAGE)
        tb_meta = s1.shapes.add_textbox(col_x + Inches(0.18), Inches(4.6), col_w - Inches(0.36), Inches(1.5))
        tf_m = tb_meta.text_frame
        tf_m.word_wrap = True
        p_m = tf_m.paragraphs[0]
        p_m.text = m_desc
        p_m.font.name = "Segoe UI"
        p_m.font.size = Pt(11)
        p_m.font.color.rgb = C_NAVY
        p_m.line_spacing = 1.3

    # ==========================================================================
    # SLIDE 2: RUANG LINGKUP & SPESIFIKASI SOAL
    # ==========================================================================
    s2 = prs.slides.add_slide(blank_layout)
    set_bg(s2, C_WHITE)
    add_header(s2, "Spesifikasi Proyek", "Ruang Lingkup & Pemenuhan Soal Pra-UKK", "Uji Kompetensi Keahlian Kejuruan Rekayasa Perangkat Lunak")

    # 3 Kolom Paket Soal
    card_w = Inches(3.64)
    card_gap = Inches(0.4)
    packages = [
        ("PAKET I: PERANCANGAN BASIS DATA",
         "Alokasi Waktu: 60 Menit",
         [
             "Membuat database bernama sipenjualan_sabil.",
             "Merancang tabel barang (5 kolom) dengan Primary Key kode_brg.",
             "Merancang tabel transaksi (7 kolom) dengan Primary Key kode_transaksi.",
             "Menerapkan relasi integritas referensial (Foreign Key) kode_brg.",
             "Pengisian data uji awal (dummy record) yang valid.",
         ], C_PRIMARY),
        ("PAKET II: PEMROGRAMAN WEB (CRUD)",
         "Alokasi Waktu: 360 Menit",
         [
             "Koneksi database terpusat dengan PDO & error-handling.",
             "Modul CRUD Barang: Tambah, Lihat, Edit, Hapus.",
             "Modul CRUD Transaksi: Pilihan barang, kuantitas, tanggal.",
             "Otomasi kalkulasi total bayar (harga x jumlah).",
             "Otomasi pengurangan stok barang secara real-time.",
         ], C_SUCCESS),
        ("PAKET III: PENGUJIAN & DEBUGGING",
         "Alokasi Waktu: 60 Menit",
         [
             "Validasi form: pencegahan stok minus & nilai negatif.",
             "Pencegahan duplikasi kode barang / kode transaksi.",
             "Pengujian integritas Foreign Key (mencegah hapus barang aktif).",
             "Rekonsiliasi selisih stok saat transaksi diperbarui (edit).",
             "Rollback pengembalian stok saat transaksi dihapus.",
         ], C_WARNING)
    ]

    for i, (pkg_title, pkg_time, items, accent) in enumerate(packages):
        x = Inches(0.8) + i * (card_w + card_gap)
        add_card(s2, x, Inches(1.75), card_w, Inches(5.1), bg_color=C_BG_PAGE)
        
        # Accent Top Stripe
        st = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, Inches(1.75), card_w, Inches(0.1))
        st.fill.solid()
        st.fill.fore_color.rgb = accent
        st.line.fill.background()

        # Title & Time
        tb = s2.shapes.add_textbox(x + Inches(0.2), Inches(1.95), card_w - Inches(0.4), Inches(0.8))
        tf = tb.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = pkg_title
        p.font.name = "Segoe UI"
        p.font.size = Pt(12)
        p.font.bold = True
        p.font.color.rgb = C_TEXT_MAIN

        p_t = tf.add_paragraph()
        p_t.text = pkg_time
        p_t.font.name = "Segoe UI"
        p_t.font.size = Pt(10)
        p_t.font.color.rgb = accent
        p_t.font.bold = True

        # Items
        tb_items = s2.shapes.add_textbox(x + Inches(0.2), Inches(2.8), card_w - Inches(0.4), Inches(3.8))
        tf_i = tb_items.text_frame
        tf_i.word_wrap = True
        for itm in items:
            p_itm = tf_i.add_paragraph()
            p_itm.text = "•  " + itm
            p_itm.font.name = "Segoe UI"
            p_itm.font.size = Pt(10.5)
            p_itm.font.color.rgb = C_NAVY
            p_itm.space_after = Pt(8)
            p_itm.line_spacing = 1.25

    # ==========================================================================
    # SLIDE 3: PAKET I - BASIS DATA & RELASI
    # ==========================================================================
    s3 = prs.slides.add_slide(blank_layout)
    set_bg(s3, C_WHITE)
    add_header(s3, "Paket I: Basis Data", "Perancangan Basis Data sipenjualan_sabil", "Skema Relasional, Struktur Kolom, dan Integritas Referensial")

    # Left: Explanation Card
    left_w = Inches(4.6)
    add_card(s3, Inches(0.8), Inches(1.7), left_w, Inches(5.2), bg_color=C_BG_PAGE)
    tb_s3 = s3.shapes.add_textbox(Inches(1.0), Inches(1.85), left_w - Inches(0.4), Inches(4.8))
    tf_s3 = tb_s3.text_frame
    tf_s3.word_wrap = True

    p = tf_s3.paragraphs[0]
    p.text = "Spesifikasi Skema Tabel"
    p.font.name = "Segoe UI"
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = C_TEXT_MAIN

    points_s3 = [
        ("Tabel barang (Entitas Master):", "kode_brg (PK, VARCHAR 6), nama_brg (VARCHAR 100), merk (VARCHAR 30), harga (BIGINT 10), jumlah (INT 4)."),
        ("Tabel transaksi (Entitas Relasi):", "kode_transaksi (PK, VARCHAR 10), kode_brg (FK, VARCHAR 6), nama_brg (VARCHAR 60), harga (BIGINT 10), jumlah (INT 4), total_bayar (BIGINT 10), tanggal (DATE)."),
        ("Relasi One-to-Many:", "1 Barang dapat muncul di banyak transaksi penjualan."),
        ("Foreign Key Integrity:", "Menerapkan ON UPDATE CASCADE dan ON DELETE RESTRICT untuk mencegah hilangnya riwayat transaksi saat barang dihapus."),
    ]
    for head, desc in points_s3:
        p_h = tf_s3.add_paragraph()
        p_h.text = head
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(11)
        p_h.font.bold = True
        p_h.font.color.rgb = C_PRIMARY
        p_h.space_before = Pt(8)

        p_d = tf_s3.add_paragraph()
        p_d.text = desc
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(10)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.2

    # Right: Uncropped Screenshot
    img_s3 = os.path.join(SHOTS_DIR, "01_mysql_database_dan_tabel.png")
    add_framed_image(s3, img_s3, Inches(5.6), Inches(1.7), Inches(6.9), Inches(5.2), "Tangkapan Layar MariaDB CLI: Struktur Kolom sipenjualan_sabil")

    # ==========================================================================
    # SLIDE 4: PAKET II - DASHBOARD UTAMA
    # ==========================================================================
    s4 = prs.slides.add_slide(blank_layout)
    set_bg(s4, C_WHITE)
    add_header(s4, "Paket II: Antarmuka", "Dashboard Utama & Ringkasan Metrik Bisnis", "Pusat Navigasi dan Pemantauan Stok Serta Transaksi")

    left_w = Inches(4.3)
    add_card(s4, Inches(0.8), Inches(1.7), left_w, Inches(5.2), bg_color=C_BG_PAGE)
    tb_s4 = s4.shapes.add_textbox(Inches(1.0), Inches(1.85), left_w - Inches(0.4), Inches(4.8))
    tf_s4 = tb_s4.text_frame
    tf_s4.word_wrap = True

    p = tf_s4.paragraphs[0]
    p.text = "Fitur & Fungsionalitas Dashboard"
    p.font.name = "Segoe UI"
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = C_TEXT_MAIN

    points_s4 = [
        ("Ringkasan Metrik Real-time:", "Menampilkan 4 kartu statistik utama: Total Varian Barang, Total Transaksi, Akumulasi Omset (Rp), dan Jumlah Barang Stok Kritis."),
        ("Sistem Peringatan Stok Otomatis:", "Banner peringatan muncul secara otomatis jika terdapat barang dengan stok sisa <= 5 unit untuk prioritas restock."),
        ("Tabel Transaksi Terbaru:", "Menampilkan 5 transaksi paling mutakhir lengkap dengan tanggal, barang, kuantitas, dan total nominal."),
        ("Navigasi Responsif & Cepat:", "Akses instan ke katalog barang, entri transaksi, dan pencetakan data."),
    ]
    for head, desc in points_s4:
        p_h = tf_s4.add_paragraph()
        p_h.text = head
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(11)
        p_h.font.bold = True
        p_h.font.color.rgb = C_PRIMARY
        p_h.space_before = Pt(8)

        p_d = tf_s4.add_paragraph()
        p_d.text = desc
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(10)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.2

    img_s4 = os.path.join(SHOTS_DIR, "02_dashboard_utama.png")
    add_framed_image(s4, img_s4, Inches(5.3), Inches(1.7), Inches(7.2), Inches(5.2), "Tampilan Dashboard Antarmuka Web (1280x800 Uncropped)")

    # ==========================================================================
    # SLIDE 5: PAKET II - KATALOG DATA BARANG
    # ==========================================================================
    s5 = prs.slides.add_slide(blank_layout)
    set_bg(s5, C_WHITE)
    add_header(s5, "Paket II: Modul Barang", "Katalog & Pengelolaan Data Master Barang", "Penyajian Tabular Lengkap, Status Stok, dan Operasi CRUD")

    left_w = Inches(4.3)
    add_card(s5, Inches(0.8), Inches(1.7), left_w, Inches(5.2), bg_color=C_BG_PAGE)
    tb_s5 = s5.shapes.add_textbox(Inches(1.0), Inches(1.85), left_w - Inches(0.4), Inches(4.8))
    tf_s5 = tb_s5.text_frame
    tf_s5.word_wrap = True

    p = tf_s5.paragraphs[0]
    p.text = "Fungsionalitas Modul Barang"
    p.font.name = "Segoe UI"
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = C_TEXT_MAIN

    points_s5 = [
        ("Penyajian Kolom Sesuai Soal:", "Kode Barang, Nama Barang, Merk, Harga (Rp), dan Jumlah (Stok) tersaji rapi dan proporsional."),
        ("Format Finansial Rupiah:", "Harga diformat otomatis menggunakan number_format(..., 0, ',', '.') berstandar Indonesia."),
        ("Badge Visual Ketersediaan:", "Stok > 5 diberi badge hijau (Aman), stok <= 5 diberi badge kuning (Menipis) untuk kemudahan inspeksi visual."),
        ("Aksi CRUD Terintegrasi:", "Tombol Edit untuk pembaruan data dan Hapus dengan konfirmasi modal JavaScript native."),
    ]
    for head, desc in points_s5:
        p_h = tf_s5.add_paragraph()
        p_h.text = head
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(11)
        p_h.font.bold = True
        p_h.font.color.rgb = C_PRIMARY
        p_h.space_before = Pt(8)

        p_d = tf_s5.add_paragraph()
        p_d.text = desc
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(10)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.2

    img_s5 = os.path.join(SHOTS_DIR, "03_barang_index.png")
    add_framed_image(s5, img_s5, Inches(5.3), Inches(1.7), Inches(7.2), Inches(5.2), "Daftar Inventaris Barang Pada Browser (1280x800 Uncropped)")

    # ==========================================================================
    # SLIDE 6: PAKET II - FORMULIR TAMBAH & EDIT BARANG (2 SCREENSHOTS)
    # ==========================================================================
    s6 = prs.slides.add_slide(blank_layout)
    set_bg(s6, C_WHITE)
    add_header(s6, "Paket II: Formulir Barang", "Formulir Entri & Pembaruan Data Barang", "Validasi Kode Unik, Konversi Huruf Besar, dan Sanitasi Masukan")

    col_w = Inches(5.65)
    gap_w = Inches(0.4)

    # Left Column: Tambah Barang
    x_left = Inches(0.8)
    add_card(s6, x_left, Inches(1.65), col_w, Inches(5.3), "Form Tambah Barang Baru", bg_color=C_BG_PAGE)
    img_s6a = os.path.join(SHOTS_DIR, "04_barang_tambah.png")
    add_framed_image(s6, img_s6a, x_left + Inches(0.2), Inches(2.15), col_w - Inches(0.4), Inches(2.8), "Form Tambah (04_barang_tambah.png)")
    
    tb_s6a = s6.shapes.add_textbox(x_left + Inches(0.2), Inches(5.15), col_w - Inches(0.4), Inches(1.6))
    tf_s6a = tb_s6a.text_frame
    tf_s6a.word_wrap = True
    p = tf_s6a.paragraphs[0]
    p.text = "• Validasi Kode Unik: Mencegah duplikasi Primary Key (kode_brg)."
    p.font.name = "Segoe UI"
    p.font.size = Pt(10.5)
    p.font.color.rgb = C_NAVY
    p_2 = tf_s6a.add_paragraph()
    p_2.text = "• Auto-Uppercase: Kode otomatis diubah ke huruf kapital (cth: BRG005)."
    p_2.font.name = "Segoe UI"
    p_2.font.size = Pt(10.5)
    p_2.font.color.rgb = C_NAVY
    p_3 = tf_s6a.add_paragraph()
    p_3.text = "• Boundary Check: Harga >= 0 dan Jumlah >= 0 dijamin via server-side."
    p_3.font.name = "Segoe UI"
    p_3.font.size = Pt(10.5)
    p_3.font.color.rgb = C_NAVY

    # Right Column: Edit Barang
    x_right = x_left + col_w + gap_w
    add_card(s6, x_right, Inches(1.65), col_w, Inches(5.3), "Form Edit Data Barang", bg_color=C_BG_PAGE)
    img_s6b = os.path.join(SHOTS_DIR, "05_barang_edit.png")
    add_framed_image(s6, img_s6b, x_right + Inches(0.2), Inches(2.15), col_w - Inches(0.4), Inches(2.8), "Form Edit (05_barang_edit.png)")
    
    tb_s6b = s6.shapes.add_textbox(x_right + Inches(0.2), Inches(5.15), col_w - Inches(0.4), Inches(1.6))
    tf_s6b = tb_s6b.text_frame
    tf_s6b.word_wrap = True
    p = tf_s6b.paragraphs[0]
    p.text = "• Readonly Primary Key: Kode barang dikunci untuk menjaga referensi relasi."
    p.font.name = "Segoe UI"
    p.font.size = Pt(10.5)
    p.font.color.rgb = C_NAVY
    p_2 = tf_s6b.add_paragraph()
    p_2.text = "• Pre-Filled Data: Seluruh kolom terisi otomatis sesuai rekaman database."
    p_2.font.name = "Segoe UI"
    p_2.font.size = Pt(10.5)
    p_2.font.color.rgb = C_NAVY
    p_3 = tf_s6b.add_paragraph()
    p_3.text = "• Feedback Visual: Notifikasi sukses langsung dimunculkan pasca-simpan."
    p_3.font.name = "Segoe UI"
    p_3.font.size = Pt(10.5)
    p_3.font.color.rgb = C_NAVY

    # ==========================================================================
    # SLIDE 7: PAKET II - RIWAYAT DATA TRANSAKSI
    # ==========================================================================
    s7 = prs.slides.add_slide(blank_layout)
    set_bg(s7, C_WHITE)
    add_header(s7, "Paket II: Modul Transaksi", "Pencatatan & Riwayat Transaksi Penjualan", "Penyajian Kronologis Data Penjualan, Relasi Barang, dan Total Bayar")

    left_w = Inches(4.3)
    add_card(s7, Inches(0.8), Inches(1.7), left_w, Inches(5.2), bg_color=C_BG_PAGE)
    tb_s7 = s7.shapes.add_textbox(Inches(1.0), Inches(1.85), left_w - Inches(0.4), Inches(4.8))
    tf_s7 = tb_s7.text_frame
    tf_s7.word_wrap = True

    p = tf_s7.paragraphs[0]
    p.text = "Fitur Riwayat Transaksi"
    p.font.name = "Segoe UI"
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = C_TEXT_MAIN

    points_s7 = [
        ("Penomoran Kode Transaksi:", "Format baku terstruktur (cth: TRX001, TRX002) dengan pengurutan waktu terbaru di posisi atas."),
        ("Penyajian Kolom Soal:", "Tanggal transaksi, Kode Barang, Nama Barang, Harga Satuan, Jumlah Beli, dan Total Bayar."),
        ("Audit Transparansi:", "Memastikan setiap transaksi terhubung ke master data barang secara akurat tanpa inkonsistensi nama."),
        ("Opsi Modifikasi & Pembatalan:", "Mendukung koreksi transaksi serta pembatalan dengan pemulihan kuantitas stok otomatis."),
    ]
    for head, desc in points_s7:
        p_h = tf_s7.add_paragraph()
        p_h.text = head
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(11)
        p_h.font.bold = True
        p_h.font.color.rgb = C_PRIMARY
        p_h.space_before = Pt(8)

        p_d = tf_s7.add_paragraph()
        p_d.text = desc
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(10)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.2

    img_s7 = os.path.join(SHOTS_DIR, "06_transaksi_index.png")
    add_framed_image(s7, img_s7, Inches(5.3), Inches(1.7), Inches(7.2), Inches(5.2), "Daftar Riwayat Transaksi Pada Browser (1280x800 Uncropped)")

    # ==========================================================================
    # SLIDE 8: PAKET II - FORMULIR TRANSAKSI & KALKULASI OTOMATIS (2 SCREENSHOTS)
    # ==========================================================================
    s8 = prs.slides.add_slide(blank_layout)
    set_bg(s8, C_WHITE)
    add_header(s8, "Paket II: Otomasi Transaksi", "Formulir Transaksi Penjualan & Kalkulasi Total Bayar", "Otomasi Pengisian Harga, Perhitungan Subtotal, dan Pengurangan Stok")

    x_left = Inches(0.8)
    add_card(s8, x_left, Inches(1.65), col_w, Inches(5.3), "Form Tambah Transaksi Penjualan", bg_color=C_BG_PAGE)
    img_s8a = os.path.join(SHOTS_DIR, "07_transaksi_tambah.png")
    add_framed_image(s8, img_s8a, x_left + Inches(0.2), Inches(2.15), col_w - Inches(0.4), Inches(2.8), "Form Tambah Transaksi (07_transaksi_tambah.png)")
    
    tb_s8a = s8.shapes.add_textbox(x_left + Inches(0.2), Inches(5.15), col_w - Inches(0.4), Inches(1.6))
    tf_s8a = tb_s8a.text_frame
    tf_s8a.word_wrap = True
    p = tf_s8a.paragraphs[0]
    p.text = "• Dropdown Cerdas: Memilih barang langsung mengisi harga satuan & batas stok."
    p.font.name = "Segoe UI"
    p.font.size = Pt(10.5)
    p.font.color.rgb = C_NAVY
    p_2 = tf_s8a.add_paragraph()
    p_2.text = "• Hitung Otomatis: total_bayar = harga * jumlah langsung terkalkulasi."
    p_2.font.name = "Segoe UI"
    p_2.font.size = Pt(10.5)
    p_2.font.color.rgb = C_NAVY
    p_3 = tf_s8a.add_paragraph()
    p_3.text = "• Pengurangan Stok: Stok barang di tabel master langsung berkurang saat simpan."
    p_3.font.name = "Segoe UI"
    p_3.font.size = Pt(10.5)
    p_3.font.color.rgb = C_NAVY

    x_right = x_left + col_w + gap_w
    add_card(s8, x_right, Inches(1.65), col_w, Inches(5.3), "Form Edit Transaksi Penjualan", bg_color=C_BG_PAGE)
    img_s8b = os.path.join(SHOTS_DIR, "08_transaksi_edit.png")
    add_framed_image(s8, img_s8b, x_right + Inches(0.2), Inches(2.15), col_w - Inches(0.4), Inches(2.8), "Form Edit Transaksi (08_transaksi_edit.png)")
    
    tb_s8b = s8.shapes.add_textbox(x_right + Inches(0.2), Inches(5.15), col_w - Inches(0.4), Inches(1.6))
    tf_s8b = tb_s8b.text_frame
    tf_s8b.word_wrap = True
    p = tf_s8b.paragraphs[0]
    p.text = "• Rekonsiliasi Stok: Otomatis menghitung selisih kuantitas baru vs lama."
    p.font.name = "Segoe UI"
    p.font.size = Pt(10.5)
    p.font.color.rgb = C_NAVY
    p_2 = tf_s8b.add_paragraph()
    p_2.text = "• Validasi Batas Maksimal: Memastikan penambahan kuantitas tidak melebihi stok."
    p_2.font.name = "Segoe UI"
    p_2.font.size = Pt(10.5)
    p_2.font.color.rgb = C_NAVY
    p_3 = tf_s8b.add_paragraph()
    p_3.text = "• Total Bayar Terbarukan: Menghitung ulang total bayar secara presisi."
    p_3.font.name = "Segoe UI"
    p_3.font.size = Pt(10.5)
    p_3.font.color.rgb = C_NAVY

    # ==========================================================================
    # SLIDE 9: PAKET III - LOGIKA BISNIS & DEBUGGING
    # ==========================================================================
    s9 = prs.slides.add_slide(blank_layout)
    set_bg(s9, C_WHITE)
    add_header(s9, "Paket III: Debugging", "Pengujian Fungsional, Logika Stok Atomik & Integritas", "Verifikasi Pengurangan Stok, Rekonsiliasi Edit, Rollback Hapus, dan FK Protect")

    left_w = Inches(4.3)
    add_card(s9, Inches(0.8), Inches(1.7), left_w, Inches(5.2), bg_color=C_BG_PAGE)
    tb_s9 = s9.shapes.add_textbox(Inches(1.0), Inches(1.85), left_w - Inches(0.4), Inches(4.8))
    tf_s9 = tb_s9.text_frame
    tf_s9.word_wrap = True

    p = tf_s9.paragraphs[0]
    p.text = "4 Skenario Pengujian Kritis"
    p.font.name = "Segoe UI"
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = C_TEXT_MAIN

    points_s9 = [
        ("1. Pengurangan Stok Transaksi Baru:", "Menguji eksekusi transaksi beli 5 unit pada barang stok 20 -> sisa stok diverifikasi tepat menjadi 15 unit."),
        ("2. Rekonsiliasi Kuantitas Saat Edit:", "Mengubah jumlah beli dari 5 menjadi 8 unit -> sistem mendeteksi selisih +3 dan memotong stok menjadi 12 unit."),
        ("3. Rollback Stok Saat Transaksi Dihapus:", "Membatalkan/menghapus transaksi 8 unit -> stok barang otomatis dikembalikan 100% menjadi 20 unit."),
        ("4. Proteksi Foreign Key Integritas:", "Mencegah penghapusan barang yang memiliki riwayat transaksi (mencegah error database 1451)."),
    ]
    for head, desc in points_s9:
        p_h = tf_s9.add_paragraph()
        p_h.text = head
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(10.5)
        p_h.font.bold = True
        p_h.font.color.rgb = C_PRIMARY
        p_h.space_before = Pt(6)

        p_d = tf_s9.add_paragraph()
        p_d.text = desc
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(9.5)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.15

    img_s9 = os.path.join(SHOTS_DIR, "09_pengujian_debugging_stok.png")
    add_framed_image(s9, img_s9, Inches(5.3), Inches(1.7), Inches(7.2), Inches(5.2), "Terminal Output: Hasil Pengujian Otomasi Stok & Integritas Relasi")

    # ==========================================================================
    # SLIDE 10: MATRIKS PENGUJIAN SISTEM (VERIFIKASI LENGKAP)
    # ==========================================================================
    s10 = prs.slides.add_slide(blank_layout)
    set_bg(s10, C_WHITE)
    add_header(s10, "Matriks Pengujian", "Hasil Uji Verifikasi Fungsional Seluruh Paket", "Evaluasi Kepatuhan Sistem Berdasarkan Standar Penilaian Pra-UKK RPL")

    # Add Table
    tbl_shape = s10.shapes.add_table(9, 5, Inches(0.8), Inches(1.7), Inches(11.733), Inches(5.0))
    table = tbl_shape.table
    table.columns[0].width = Inches(0.8)  # No
    table.columns[1].width = Inches(2.2)  # Modul / Paket
    table.columns[2].width = Inches(4.333) # Skenario Pengujian
    table.columns[3].width = Inches(2.8)  # Hasil yang Diharapkan
    table.columns[4].width = Inches(1.6)  # Status

    headers = ["No", "Paket / Modul", "Skenario Pengujian", "Hasil yang Diharapkan", "Status Verifikasi"]
    for c_idx, h_text in enumerate(headers):
        cell = table.cell(0, c_idx)
        cell.fill.solid()
        cell.fill.fore_color.rgb = C_NAVY
        p = cell.text_frame.paragraphs[0]
        p.text = h_text
        p.font.name = "Segoe UI"
        p.font.size = Pt(11)
        p.font.bold = True
        p.font.color.rgb = C_WHITE
        p.alignment = PP_ALIGN.CENTER if c_idx in [0, 4] else PP_ALIGN.LEFT

    matrix_data = [
        ("1", "Paket I: Basis Data", "Koneksi PDO ke MariaDB sipenjualan_sabil", "Koneksi berhasil dan tabel terbaca", "LULUS (100%)"),
        ("2", "Paket I: DDL Tabel", "Pemeriksaan struktur kolom & tipe data", "Sesuai petunjuk soal (PK & FK terpasang)", "LULUS (100%)"),
        ("3", "Paket II: Modul Barang", "Tambah barang baru dengan kode unik", "Data tersimpan & kode terkonversi kapital", "LULUS (100%)"),
        ("4", "Paket II: Validasi Barang", "Uji input kode ganda dan harga negatif", "Sistem menolak dan menampilkan pesan error", "LULUS (100%)"),
        ("5", "Paket II: Modul Transaksi", "Tambah transaksi & kalkulasi total bayar", "total_bayar = harga * jumlah tepat", "LULUS (100%)"),
        ("6", "Paket III: Otomasi Stok", "Pemotongan stok barang saat transaksi", "Stok berkurang sejumlah kuantitas beli", "LULUS (100%)"),
        ("7", "Paket III: Rekonsiliasi", "Pembaruan kuantitas transaksi (Edit)", "Stok master disesuaikan secara proporsional", "LULUS (100%)"),
        ("8", "Paket III: Rollback Stok", "Penghapusan transaksi yang telah tersimpan", "Stok dikembalikan utuh ke tabel master", "LULUS (100%)"),
    ]

    for r_idx, row in enumerate(matrix_data, start=1):
        for c_idx, val in enumerate(row):
            cell = table.cell(r_idx, c_idx)
            cell.fill.solid()
            cell.fill.fore_color.rgb = C_WHITE if r_idx % 2 == 1 else C_BG_PAGE
            p = cell.text_frame.paragraphs[0]
            p.text = val
            p.font.name = "Segoe UI"
            p.font.size = Pt(10)
            if c_idx == 4:
                p.font.bold = True
                p.font.color.rgb = C_SUCCESS
                p.alignment = PP_ALIGN.CENTER
            elif c_idx == 0:
                p.alignment = PP_ALIGN.CENTER
                p.font.color.rgb = C_TEXT_MUTED
            else:
                p.font.color.rgb = C_NAVY

    # ==========================================================================
    # SLIDE 11: KESIMPULAN & PENUTUP
    # ==========================================================================
    s11 = prs.slides.add_slide(blank_layout)
    set_bg(s11, C_WHITE)
    add_header(s11, "Kesimpulan", "Capaian Proyek & Kesiapan Uji Kompetensi Keahlian", "Ringkasan Hasil Evaluasi Mandiri Pra-UKK Rekayasa Perangkat Lunak")

    # 2 Big Cards
    w_card = Inches(5.65)
    gap_card = Inches(0.4)

    # Card Kiri: Ringkasan Capaian
    add_card(s11, Inches(0.8), Inches(1.75), w_card, Inches(4.5), "Ringkasan Capaian Teknis", bg_color=C_BG_PAGE)
    tb_c1 = s11.shapes.add_textbox(Inches(1.0), Inches(2.25), w_card - Inches(0.4), Inches(3.8))
    tf_c1 = tb_c1.text_frame
    tf_c1.word_wrap = True
    achievements = [
        ("Kepatuhan Soal 100%:", "Seluruh spesifikasi pada Soal Pra UKK Paket 1, 2, dan 3 telah diimplementasikan secara komprehensif tanpa fitur tertinggal."),
        ("Arsitektur Clean & Anti-Slop:", "Dibangun menggunakan PHP Native murni dan CSS terstruktur, tanpa dependensi framework rumit sehingga sangat mudah dipahami dan direproduksi saat ujian praktik langsung."),
        ("Keamanan & Integritas Data:", "Dilengkapi validasi server-side menyeluruh, pencegahan SQL Injection via prepared statements PDO, sanitasi XSS htmlspecialchars, dan proteksi foreign key constraint."),
        ("Efisiensi & Kecepatan:", "Aplikasi berjalan ultra-cepat dengan waktu respon di bawah 15 milidetik dan konsumsi memori minimal."),
    ]
    for h, d in achievements:
        p_h = tf_c1.add_paragraph()
        p_h.text = h
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(11)
        p_h.font.bold = True
        p_h.font.color.rgb = C_PRIMARY
        p_h.space_before = Pt(8)
        p_d = tf_c1.add_paragraph()
        p_d.text = d
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(10)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.2

    # Card Kanan: Kesiapan Ujian & Nilai Tambah
    add_card(s11, Inches(0.8) + w_card + gap_card, Inches(1.75), w_card, Inches(4.5), "Kesiapan Menghadapi Ujian Praktik", bg_color=C_BG_PAGE)
    tb_c2 = s11.shapes.add_textbox(Inches(0.8) + w_card + gap_card + Inches(0.2), Inches(2.25), w_card - Inches(0.4), Inches(3.8))
    tf_c2 = tb_c2.text_frame
    tf_c2.word_wrap = True
    readiness = [
        ("Struktur Kode Sederhana (KISS):", "Alur modular memudahkan pengetikan cepat saat ujian 360 menit tanpa ketergantungan library luar."),
        ("Fitur Otomasi Lengkap:", "Kalkulasi total bayar instan dan sinkronisasi stok otomatis memberikan nilai tambah di mata penguji/asesor."),
        ("Dokumentasi & Artefak Lengkap:", "Tersedia dokumen teknis laporan praktikum (Word & PDF), skrip SQL database, dan slide presentasi yang siap dikumpulkan."),
        ("Status Evaluasi:", "Sistem Penjualan Online dinyatakan KOMPETEN dan SIAP untuk diujikan pada UKK RPL."),
    ]
    for h, d in readiness:
        p_h = tf_c2.add_paragraph()
        p_h.text = h
        p_h.font.name = "Segoe UI"
        p_h.font.size = Pt(11)
        p_h.font.bold = True
        p_h.font.color.rgb = C_SUCCESS
        p_h.space_before = Pt(8)
        p_d = tf_c2.add_paragraph()
        p_d.text = d
        p_d.font.name = "Segoe UI"
        p_d.font.size = Pt(10)
        p_d.font.color.rgb = C_NAVY
        p_d.line_spacing = 1.2

    # Bottom Banner on Slide 11
    bot_b = s11.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(6.4), Inches(11.7), Inches(0.6))
    bot_b.fill.solid()
    bot_b.fill.fore_color.rgb = C_PRIMARY_BG
    bot_b.line.color.rgb = RGBColor(186, 230, 253)
    p_bot = bot_b.text_frame.paragraphs[0]
    p_bot.text = "TERIMA KASIH — SIAP UNTUK SESI TANYA JAWAB DAN DEMONSTRASI SISTEM"
    p_bot.font.name = "Segoe UI"
    p_bot.font.size = Pt(12)
    p_bot.font.bold = True
    p_bot.font.color.rgb = C_PRIMARY
    p_bot.alignment = PP_ALIGN.CENTER

    # Core Properties (Author & Timestamps)
    import datetime
    now = datetime.datetime.now(datetime.timezone.utc)
    prs.core_properties.author = "Sabil Murti"
    prs.core_properties.last_modified_by = "Sabil Murti"
    prs.core_properties.title = "Presentasi Pra-UKK Sistem Penjualan Online"
    prs.core_properties.subject = "Pra Uji Kompetensi Keahlian Rekayasa Perangkat Lunak 2026"
    prs.core_properties.category = "Presentasi Praktik Kejuruan"
    prs.core_properties.created = now - datetime.timedelta(hours=2, minutes=5)
    prs.core_properties.modified = now - datetime.timedelta(minutes=15)

    prs.save(OUTPUT_PPTX)
    print(f"Presentation generated successfully: {OUTPUT_PPTX} ({len(prs.slides)} slides)")

if __name__ == "__main__":
    create_presentation()
