// konsultasi_anggota.js - FINAL VERSION (Word Wrap Fix)

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== FORM ELEMENTS ==========
    const konsultasiForm = document.getElementById('konsultasiForm');
    const teleponInput = document.getElementById('telepon');
    const ahliSelect = document.getElementById('ahli');
    const topikInput = document.getElementById('topik');
    const pertanyaanTextarea = document.getElementById('pertanyaan');
    const riwayatContainer = document.getElementById('riwayatContainer');

    // ========== LOAD RIWAYAT KONSULTASI ==========
    if (riwayatContainer) {
        loadRiwayatKonsultasi();
    }

    // ========== SELECT AHLI FROM CARD ==========
    window.selectAhli = function(namaAhli) {
        if (ahliSelect) {
            ahliSelect.value = namaAhli;
            const formSection = document.querySelector('.form-konsultasi-section');
            if (formSection) {
                formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            setTimeout(() => {
                if (topikInput) topikInput.focus();
            }, 500);
            showNotification(`Ahli ${namaAhli} dipilih`, 'success');
        }
    };

    // ========== FORM VALIDATION ==========
    function validateForm() {
        const telepon = teleponInput.value.trim();
        const topik = topikInput.value.trim();
        const pertanyaan = pertanyaanTextarea.value.trim();

        if (!telepon) {
            showNotification('Nomor telepon harus diisi', 'error');
            teleponInput.focus(); return false;
        }
        const phoneRegex = /^[0-9]{10,15}$/;
        if (!phoneRegex.test(telepon.replace(/[\s-]/g, ''))) {
            showNotification('Format nomor telepon tidak valid (10-15 digit)', 'error');
            teleponInput.focus(); return false;
        }
        if (!topik) {
            showNotification('Topik konsultasi harus diisi', 'error');
            topikInput.focus(); return false;
        }
        if (!pertanyaan) {
            showNotification('Pertanyaan harus diisi', 'error');
            pertanyaanTextarea.focus(); return false;
        }
        if (pertanyaan.length < 20) {
            showNotification('Pertanyaan minimal 20 karakter', 'error');
            pertanyaanTextarea.focus(); return false;
        }
        return true;
    }

    // ========== FORM SUBMIT ==========
    if (konsultasiForm) {
        konsultasiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateForm()) return;

            const submitButton = konsultasiForm.querySelector('.btn-submit');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';

            const formData = new FormData();
            formData.append('nama', document.getElementById('nama').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('telepon', teleponInput.value.trim());
            formData.append('ahli', ahliSelect.value);
            formData.append('topik', topikInput.value.trim());
            formData.append('pertanyaan', pertanyaanTextarea.value.trim());

            fetch('../../api/submit_konsultasi.php', { 
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Konsultasi berhasil dikirim!', 'success');
                    topikInput.value = '';
                    pertanyaanTextarea.value = '';
                    ahliSelect.value = '';
                    loadRiwayatKonsultasi();
                    showConfirmationModal(data.konsultasi_id);
                } else {
                    showNotification(data.message || 'Gagal mengirim konsultasi', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan sistem', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    }

    // ========== LOAD RIWAYAT ==========
    function loadRiwayatKonsultasi() {
        fetch('../../api/get_konsultasi.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.konsultasi.length > 0) {
                displayRiwayat(data.konsultasi);
            } else {
                riwayatContainer.innerHTML = `<div class="empty-state"><p>Belum ada riwayat konsultasi</p></div>`;
            }
        })
        .catch(error => {
            console.error('Error loading riwayat:', error);
            riwayatContainer.innerHTML = '<p class="error-message">Gagal memuat riwayat.</p>';
        });
    }

    // ========== DISPLAY RIWAYAT (UPDATE: FIX TEXT WRAP) ==========
    function displayRiwayat(konsultasiList) {
        const html = konsultasiList.map(item => {
            let currentStatus = item.status;
            if (item.jawaban && (item.status === 'pending' || item.status === 'menunggu')) {
                currentStatus = 'dijawab';
            }

            const statusClass = getStatusClass(currentStatus);
            const statusText = getStatusText(currentStatus);
            const tanggal = formatTanggal(item.tanggal);
            
            return `
                <div class="riwayat-card">
                    <div class="riwayat-header">
                        <div>
                            <h4 class="riwayat-topik">${escapeHtml(item.topik)}</h4>
                            <p class="riwayat-tanggal">${tanggal}</p>
                        </div>
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </div>
                    
                    ${item.ahli ? `<p class="riwayat-ahli">Ahli: <strong>${escapeHtml(item.ahli)}</strong></p>` : ''}
                    
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 10px 0;">
                        <strong style="color: #555;">Pertanyaan:</strong>
                        <p style="margin: 5px 0 0; color: #333; line-height: 1.5; word-wrap: break-word; overflow-wrap: break-word;">
                            ${escapeHtml(item.pertanyaan)}
                        </p>
                    </div>

                    ${item.jawaban ? `
                        <div style="background: #e0f7fa; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 5px solid #3f8686;">
                            <strong style="color: #3f8686;"><i class="bi bi-check-circle-fill"></i> Jawaban Ahli:</strong>
                            <p style="margin: 5px 0 0; color: #2c3e50; line-height: 1.5; word-wrap: break-word; overflow-wrap: break-word;">
                                ${escapeHtml(item.jawaban)}
                            </p>
                        </div>
                    ` : ''}

                    <div class="riwayat-actions">
                        ${currentStatus === 'pending' ? `<button class="btn-cancel" onclick="cancelKonsultasi(${item.id})">Batalkan</button>` : ''}
                        <button class="btn-detail" onclick="showDetailKonsultasi(${item.id})">Lihat Detail Lengkap</button>
                    </div>
                </div>
            `;
        }).join('');
        riwayatContainer.innerHTML = html;
    }

    // ========== FUNGSI BATALKAN ==========
    window.cancelKonsultasi = function(id) {
        if (confirm('Apakah Anda yakin ingin membatalkan konsultasi ini?')) {
            window.location.href = 'index_anggota.php?page=konsultasi_anggota&action=cancel&id=' + id;
        }
    };

    // ========== SHOW DETAIL ==========
    window.showDetailKonsultasi = function(id) {
        fetch(`../../api/get_konsultasi_detail.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) showDetailModal(data.konsultasi);
            else showNotification('Gagal memuat detail', 'error');
        });
    };

    // ========== MODAL DETAIL (UPDATE: FIX TEXT WRAP) ==========
    function showDetailModal(konsultasi) {
        const modal = document.createElement('div');
        modal.className = 'detail-modal';
        modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 99999; padding: 20px;';
        
        const statusText = getStatusText(konsultasi.status);
        const tanggal = formatTanggal(konsultasi.tanggal);
        
        modal.innerHTML = `
            <div style="background: white; padding: 30px; border-radius: 15px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <h3 style="color: #3f8686; margin-top: 0;">Detail Konsultasi</h3>
                <div style="margin-bottom: 15px;"><strong>Status:</strong> ${statusText}</div>
                <div style="margin-bottom: 15px;"><strong>Tanggal:</strong> ${tanggal}</div>
                <div style="margin-bottom: 15px;"><strong>Topik:</strong> ${escapeHtml(konsultasi.topik)}</div>
                <div style="margin-bottom: 15px;">
                    <strong>Pertanyaan:</strong>
                    <p style="background: #f9f9f9; padding: 10px; border-radius: 8px; margin-top: 5px; word-wrap: break-word; overflow-wrap: break-word;">
                        ${escapeHtml(konsultasi.pertanyaan)}
                    </p>
                </div>
                ${konsultasi.jawaban ? `
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #3f8686;">Jawaban Ahli:</strong>
                        <div style="background: #e0f7fa; padding: 15px; border-radius: 8px; margin-top: 5px; border-left: 4px solid #3f8686;">
                             <p style="word-wrap: break-word; overflow-wrap: break-word; margin: 0;">
                                ${escapeHtml(konsultasi.jawaban)}
                             </p>
                        </div>
                    </div>
                ` : ''}
                <button onclick="closeDetailModal()" style="width: 100%; padding: 12px; background-color: #3f8686 !important; color: white !important; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; visibility: visible !important; opacity: 1 !important;">
                    Tutup
                </button>
            </div>
        `;
        document.body.appendChild(modal);
    }

    window.closeDetailModal = function() {
        const modal = document.querySelector('.detail-modal');
        if (modal) modal.remove();
    };

    // ========== MODAL KONFIRMASI ==========
    function showConfirmationModal(id) {
        alert("Konsultasi terkirim! ID Tiket: #" + id + "\nSilakan cek riwayat secara berkala.");
    }

    // ========== HELPER STATUS ==========
    function getStatusClass(status) {
        status = status.toLowerCase();
        if (status === 'dijawab' || status === 'answered' || status === 'selesai') return 'status-dijawab';
        if (status === 'dibatalkan' || status === 'cancelled') return 'status-dibatalkan';
        return 'status-pending'; 
    }

    function getStatusText(status) {
        status = status.toLowerCase();
        if (status === 'dijawab' || status === 'answered' || status === 'selesai') return 'Sudah Dijawab';
        if (status === 'dibatalkan' || status === 'cancelled') return 'Dibatalkan';
        return 'Menunggu Jawaban';
    }

    function formatTanggal(tgl) {
        return new Date(tgl).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }
    function escapeHtml(text) {
        let div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
    function showNotification(msg, type) {
        const notif = document.createElement('div');
        notif.style.cssText = `position: fixed; top: 20px; right: 20px; padding: 15px 25px; background: ${type === 'success' ? '#3f8686' : '#e74c3c'}; color: white; border-radius: 8px; z-index: 99999; font-weight: bold; box-shadow: 0 5px 15px rgba(0,0,0,0.2);`;
        notif.innerText = msg;
        document.body.appendChild(notif);
        setTimeout(() => notif.remove(), 3000);
    }

    // ========== INJECT CSS PAKSA ==========
    const style = document.createElement('style');
    style.textContent = `
        .btn-detail { background-color: #3f8686 !important; color: white !important; padding: 8px 16px !important; border-radius: 6px !important; border: none !important; cursor: pointer !important; visibility: visible !important; opacity: 1 !important; }
        .btn-cancel { background-color: #e74c3c !important; color: white !important; padding: 8px 16px !important; border-radius: 6px !important; border: none !important; cursor: pointer !important; visibility: visible !important; opacity: 1 !important; }
        .riwayat-actions { display: flex !important; gap: 10px !important; margin-top: 15px !important; }
    `;
    document.head.appendChild(style);
});