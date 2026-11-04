    </div><!-- /.content-body -->

    <!-- Footer Bar -->
    <footer class="app-footer">
        | APLIKASI PENJUALAN ONLINE | NINE MART | SMK NEGERI 9 SEMARANG - 2024 |
    </footer>

</div><!-- /.app-container -->

<script>
// Modal Handler Utility
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside box
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});
</script>

</body>
</html>
