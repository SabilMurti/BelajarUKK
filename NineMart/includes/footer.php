    </div>

    <!-- Bagian footer -->
    <footer class="app-footer">
        | APLIKASI PENJUALAN ONLINE | NINE MART | SMK NEGERI 9 SEMARANG - 2024 |
    </footer>

</div>

<script>
// Fungsi untuk membuka jendela modal
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

// Fungsi untuk menutup jendela modal
function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Menutup modal jika area di luar kotak diklik
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});
</script>

</body>
</html>
