// assets/js/pakar/konsultasi_pakar.js

document.addEventListener("DOMContentLoaded", function() {
    
    // =========================================
    // 1. FITUR FILTER (SEMUA / BELUM / SUDAH)
    // =========================================
    const filterBtns = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.konsultasi-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update tombol aktif
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');

            // Filter kartu
            cards.forEach(card => {
                if (filter === 'semua' || card.getAttribute('data-status') === filter) {
                    card.style.display = 'flex'; // Gunakan flex karena CSS asli mungkin pakai flex
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // =========================================
    // 2. HANDLER MODAL JAWAB (ISI DATA OTOMATIS)
    // =========================================
    const modalJawab = document.getElementById('modalJawab');
    if (modalJawab) {
        modalJawab.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Tombol yang diklik
            
            // Ambil data dari atribut data-* tombol
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const topik = button.getAttribute('data-topik');
            const pertanyaan = button.getAttribute('data-pertanyaan');

            // Isi ke dalam elemen modal
            document.getElementById('modal_id_konsultasi').value = id;
            document.getElementById('modal_nama_penanya').textContent = nama;
            document.getElementById('modal_topik').textContent = topik;
            document.getElementById('modal_pertanyaan').textContent = pertanyaan;
            
            // Kosongkan textarea jawaban agar bersih
            document.getElementById('jawaban_pakar').value = '';
        });
    }

    // =========================================
    // 3. PROSES KIRIM JAWABAN (AJAX KE BACKEND)
    // =========================================
    const formJawab = document.getElementById('formJawabKonsultasi');
    if (formJawab) {
        formJawab.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnSubmit = formJawab.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            
            // Tampilkan loading di tombol
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';

            const formData = new FormData(formJawab);

            // Kirim ke API backend (Pastikan file api/pakar_jawab.php sudah dibuat)
            fetch('../../api/pakar_jawab.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Tutup modal
                    const modalInstance = bootstrap.Modal.getInstance(modalJawab);
                    modalInstance.hide();
                    
                    // Tampilkan pesan sukses & reload halaman
                    alert('✅ Jawaban berhasil terkirim!');
                    window.location.reload(); 
                } else {
                    alert('❌ Gagal mengirim: ' + (data.message || 'Terjadi kesalahan.'));
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('⚠️ Terjadi kesalahan koneksi.');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
            });
        });
    }
});