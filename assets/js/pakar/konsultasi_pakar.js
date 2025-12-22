document.addEventListener('DOMContentLoaded', function() {

    // --- 1. Logika Filter (Masih Sama) ---
    const filterButtons = document.querySelectorAll('.konsultasi-filter .filter-btn');
    const konsultasiCards = document.querySelectorAll('.konsultasi-list .konsultasi-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.getAttribute('data-filter');
            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            konsultasiCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                if (filter === 'semua' || filter === cardStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // --- 2. Logika Modal (BARU) ---
    // Ganti kode alert dengan listener untuk modal

    // Listener untuk Modal JAWAB
    const modalJawab = document.getElementById('modalJawab');
    if (modalJawab) {
        modalJawab.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ekstrak data dari atribut data-*
            const idKonsultasi = button.getAttribute('data-id-konsultasi');
            const topik = button.getAttribute('data-topik');
            const pertanyaan = button.getAttribute('data-pertanyaan');

            // Update konten modal
            const modalTopik = modalJawab.querySelector('#modalJawab-topik');
            const modalPertanyaan = modalJawab.querySelector('#modalJawab-pertanyaan');
            const modalIdInput = modalJawab.querySelector('#modal-id-konsultasi');
            const modalTextarea = modalJawab.querySelector('#modalJawab-jawaban');

            modalTopik.textContent = topik;
            modalPertanyaan.textContent = pertanyaan;
            modalIdInput.value = idKonsultasi; // Simpan ID di hidden input
            modalTextarea.value = ''; // Kosongkan textarea setiap kali modal dibuka
        });
    }

    // Listener untuk Modal LIHAT
    const modalLihat = document.getElementById('modalLihat');
    if (modalLihat) {
        modalLihat.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ekstrak data dari atribut data-*
            const topik = button.getAttribute('data-topik');
            const pertanyaan = button.getAttribute('data-pertanyaan');
            const jawaban = button.getAttribute('data-jawaban');

            // Update konten modal
            const modalTopik = modalLihat.querySelector('#modalLihat-topik');
            const modalPertanyaan = modalLihat.querySelector('#modalLihat-pertanyaan');
            const modalJawaban = modalLihat.querySelector('#modalLihat-jawaban');

            modalTopik.textContent = topik;
            modalPertanyaan.textContent = pertanyaan;
            modalJawaban.textContent = jawaban; // Tampilkan jawaban yang sudah ada
        });
    }

    // --- 3. Logika Form Submission (Placeholder) ---
    // Menangani saat tombol "Kirim Jawaban" di dalam modal di-klik
    const formJawab = document.getElementById('formJawabKonsultasi');
    if(formJawab) {
        formJawab.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah halaman reload

            // Ambil data dari form
            const idKonsultasi = document.getElementById('modal-id-konsultasi').value;
            const jawaban = document.getElementById('modalJawab-jawaban').value;

            // Tampilkan di console (NANTI INI DIGANTI DENGAN AJAX/FETCH KE PHP)
            console.log('Mengirim jawaban untuk ID Konsultasi:', idKonsultasi);
            console.log('Isi Jawaban:', jawaban);

            // Beri tahu pengguna (sementara)
            alert('Jawaban (pura-pura) terkirim! Cek console log (F12) untuk detail.');

            // Tutup modal setelah submit
            const modalInstance = bootstrap.Modal.getInstance(modalJawab);
            modalInstance.hide();
            
            // Di sini Anda nanti akan menambahkan kode untuk mengirim data ini ke backend (misal: proses_jawab.php)
        });
    }

});