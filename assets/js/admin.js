// Admin JS - AQUARA Admin Panel
// Fitur: Sidebar toggle, Search tabel sederhana

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Sidebar Toggle (untuk Mobile/Responsif)
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    // Kita cari tombol toggle yang mungkin ada di navbar
    const toggleBtn = document.querySelector('#sidebarCollapse') || document.querySelector('.navbar-toggler');

    if (toggleBtn && sidebar && content) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });
    }

    // 2. Search Bar Sederhana (Client-side filtering)
    // Ini fitur bagus untuk mempertahankan pencarian cepat di tabel tanpa reload
    const searchInputs = document.querySelectorAll('input.form-control[placeholder*="Cari"]');
    searchInputs.forEach(function(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            // Cari tabel terdekat dari input search ini
            const container = this.closest('.col-md-9') || document; 
            const table = container.querySelector('table');
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });

    // 3. Auto-hide Alerts
    // Jika ada alert sukses/gagal, sembunyikan otomatis setelah 3 detik
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Gunakan Bootstrap bundle jika tersedia untuk fade out yang mulus
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                new bootstrap.Alert(alert).close();
            } else {
                alert.style.display = 'none';
            }
        }, 3000);
    });

    console.log('✅ AQUARA Admin JS Loaded (Clean Version)!');
});