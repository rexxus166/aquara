// event_pakar.js - Versi Ringkas & Terkoneksi Database

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== HANDLER TOMBOL DESKRIPSI ==========
    const eventCards = document.querySelectorAll('.event-card');

    eventCards.forEach(card => {
        const btnDesc = card.querySelector('.btn-description');
        const descDiv = card.querySelector('.event-description');
        const btnClose = card.querySelector('.btn-close-desc');

        if (btnDesc && descDiv && btnClose) {
            // Buka Deskripsi
            btnDesc.addEventListener('click', function(e) {
                e.preventDefault();
                // Tutup deskripsi lain yang sedang terbuka (opsional, biar rapi)
                document.querySelectorAll('.event-description').forEach(div => {
                    if (div !== descDiv) div.style.display = 'none';
                });
                document.querySelectorAll('.btn-description').forEach(btn => {
                    if (btn !== btnDesc) {
                        btn.textContent = 'Deskripsi Event';
                        btn.style.backgroundColor = ''; // Reset warna
                    }
                });

                // Toggle deskripsi saat ini
                if (descDiv.style.display === 'none' || descDiv.style.display === '') {
                    descDiv.style.display = 'block';
                    btnDesc.textContent = 'Sembunyikan Deskripsi';
                    btnDesc.style.backgroundColor = 'var(--primary-color)';
                    descDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    tutupDeskripsi(card);
                }
            });

            // Tutup Deskripsi via Tombol Tutup
            btnClose.addEventListener('click', function(e) {
                e.preventDefault();
                tutupDeskripsi(card);
            });
        }
    });

    // Fungsi helper untuk menutup deskripsi
    function tutupDeskripsi(card) {
        const descDiv = card.querySelector('.event-description');
        const btnDesc = card.querySelector('.btn-description');
        descDiv.style.display = 'none';
        btnDesc.textContent = 'Deskripsi Event';
        btnDesc.style.backgroundColor = ''; // Reset ke warna asli CSS
    }

    // ========== USER DROPDOWN (Jika masih diperlukan) ==========
    // ... (Biarkan kode dropdown user Anda yang lama jika masih dipakai) ...
});