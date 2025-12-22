// kalkulator_pakar.js - Versi FINAL Terkoneksi Database

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== DATA HARGA DEFAULT ==========
    const dataIkan = {
        lele: { hargaBibit: 500, hargaPakan: 8000, durasi: 3, beratPanen: 0.1, hargaJual: 25000 },
        nila: { hargaBibit: 800, hargaPakan: 9000, durasi: 4, beratPanen: 0.15, hargaJual: 30000 },
        gurame: { hargaBibit: 1500, hargaPakan: 10000, durasi: 6, beratPanen: 0.3, hargaJual: 40000 },
        bawal: { hargaBibit: 1000, hargaPakan: 9500, durasi: 4, beratPanen: 0.2, hargaJual: 35000 }
    };

    // ========== ELEMEN FORM ==========
    const luasKolam = document.getElementById('luasKolam');
    const jenisIkan = document.getElementById('jenisIkan');
    const jumlahBibit = document.getElementById('jumlahBibit');
    const hargaBibit = document.getElementById('hargaBibit');
    const hargaPakan = document.getElementById('hargaPakan');
    const targetPanen = document.getElementById('targetPanen');
    const btnHitung = document.querySelector('.btn-hitung');

    // Variabel global untuk menyimpan hasil perhitungan terakhir
    let hasilTerakhir = null;

    // ========== AUTO FILL BERDASARKAN JENIS IKAN ==========
    if (jenisIkan) {
        jenisIkan.addEventListener('change', function() {
            const jenis = this.value;
            if (jenis && dataIkan[jenis]) {
                const data = dataIkan[jenis];
                hargaBibit.value = data.hargaBibit;
                hargaPakan.value = data.hargaPakan;
                targetPanen.value = data.durasi;
                showNotification('Data default berhasil dimuat!', 'info');
            }
        });
    }

    // ========== TOMBOL HITUNG ==========
    if (btnHitung) {
        btnHitung.addEventListener('click', function(e) {
            e.preventDefault();
            if (!validasiForm()) {
                showNotification('Mohon lengkapi semua data!', 'error');
                return;
            }

            // Tampilkan efek loading
            btnHitung.disabled = true;
            btnHitung.innerHTML = '<i class="bi bi-hourglass-split"></i> Menghitung...';
            
            setTimeout(() => {
                hasilTerakhir = kalkulasi(); // Simpan hasil ke variabel global
                tampilkanHasil(hasilTerakhir);
                
                // Kembalikan tombol seperti semula
                btnHitung.disabled = false;
                btnHitung.innerHTML = '<i class="bi bi-calculator"></i> Hitung Proyeksi';
                showNotification('Perhitungan selesai!', 'success');
                
                // Scroll otomatis ke bagian hasil di layar kecil
                if (window.innerWidth < 992) {
                    document.querySelector('.hasil-card').scrollIntoView({ behavior: 'smooth' });
                }
            }, 1000); // Simulasi delay 1 detik agar terlihat prosesnya
        });
    }

    // ========== FUNGSI VALIDASI ==========
    function validasiForm() {
        return (luasKolam.value && jenisIkan.value && jumlahBibit.value && 
                hargaBibit.value && hargaPakan.value && targetPanen.value);
    }

    // ========== RUMUS PERHITUNGAN UTAMA ==========
    function kalkulasi() {
        const luas = parseFloat(luasKolam.value);
        const bibit = parseInt(jumlahBibit.value);
        const hBibit = parseFloat(hargaBibit.value);
        const hPakan = parseFloat(hargaPakan.value);
        const durasi = parseInt(targetPanen.value);
        const jenis = jenisIkan.value;
        const dataJenis = dataIkan[jenis] || dataIkan.lele; // Fallback ke lele jika data kosong

        // Rumus Biaya
        const biayaBibit = bibit * hBibit;
        // Asumsi FCR (Feed Conversion Ratio) sederhana: butuh pakan 3% dari bobot total per hari
        // Ini rumus penyederhanaan untuk estimasi kasar
        const biayaPakan = bibit * 0.03 * hPakan * durasi * 30 * 0.5; 
        const biayaOperasional = luas * 2000 * durasi; // Asumsi Rp 2.000/m2/bulan untuk listrik, air, dll.
        const totalBiaya = biayaBibit + biayaPakan + biayaOperasional;

        // Rumus Pendapatan
        const survivalRate = 0.85; // Tingkat kehidupan 85%
        const jumlahPanen = Math.floor(bibit * survivalRate);
        const beratTotal = jumlahPanen * dataJenis.beratPanen;
        const totalPendapatan = beratTotal * dataJenis.hargaJual;

        // Rumus Keuntungan & ROI
        const keuntungan = totalPendapatan - totalBiaya;
        const roi = (totalBiaya > 0) ? ((keuntungan / totalBiaya) * 100).toFixed(2) : 0;

        return {
            biayaBibit, biayaPakan, biayaOperasional, totalBiaya,
            jumlahPanen, beratTotal, totalPendapatan,
            keuntungan, roi, durasi
        };
    }

    // ========== FUNGSI TAMPILKAN HASIL KE HTML ==========
    function tampilkanHasil(hasil) {
        const hasilContent = document.querySelector('.hasil-content');
        if (!hasilContent) return;

        // Warna dinamis berdasarkan keuntungan (hijau untung, merah rugi)
        const warnaHasil = hasil.keuntungan >= 0 ? '#2ecc71' : '#e74c3c';
        const bgHasil = hasil.keuntungan >= 0 ? 'rgba(46, 204, 113, 0.1)' : 'rgba(231, 76, 60, 0.1)';

        hasilContent.innerHTML = `
            <div style="animation: fadeIn 0.5s ease;">
                <div style="background: ${bgHasil}; padding: 20px; border-radius: 12px; border-left: 5px solid ${warnaHasil}; margin-bottom: 20px; text-align: center;">
                    <h4 style="color: ${warnaHasil}; margin-bottom: 10px;">Estimasi Keuntungan</h4>
                    <div style="font-size: 28px; font-weight: 700; color: ${warnaHasil};">
                        Rp ${formatRupiah(hasil.keuntungan)}
                    </div>
                    <div style="font-size: 16px; color: #555; margin-top: 5px;">
                        ROI: ${hasil.roi}%
                    </div>
                </div>

                <div style="display: grid; gap: 10px; font-size: 15px;">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee;">
                        <span>Total Biaya Produksi:</span>
                        <strong>Rp ${formatRupiah(hasil.totalBiaya)}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee;">
                        <span>Estimasi Pendapatan:</span>
                        <strong>Rp ${formatRupiah(hasil.totalPendapatan)}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                        <span>Perkiraan Panen:</span>
                        <strong>${hasil.beratTotal.toFixed(0)} Kg</strong>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 25px;">
                    <button onclick="simpanHasil()" style="background: var(--primary-color); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <button onclick="resetForm()" style="background: #95a5a6; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        `;
    }

    // ========== FUNGSI SIMPAN KE DATABASE (AJAX) ==========
    // Fungsi ini sekarang menempel di window agar bisa dipanggil oleh onclick="" di HTML
    window.simpanHasil = function() {
        if (!hasilTerakhir) {
            showNotification('Belum ada hasil untuk disimpan!', 'error');
            return;
        }

        showNotification('Menyimpan data...', 'info');

        // Siapkan data untuk dikirim ke PHP
        const formData = new FormData();
        formData.append('ajax_simpan_kalkulasi', '1'); // Penanda request AJAX
        
        // Data Input
        formData.append('luas_kolam', luasKolam.value);
        formData.append('jenis_ikan', jenisIkan.value);
        formData.append('jumlah_bibit', jumlahBibit.value);
        formData.append('harga_bibit', hargaBibit.value);
        formData.append('harga_pakan', hargaPakan.value);
        formData.append('durasi', targetPanen.value);

        // Data Hasil
        formData.append('total_biaya', hasilTerakhir.totalBiaya);
        formData.append('estimasi_pendapatan', hasilTerakhir.totalPendapatan);
        formData.append('estimasi_keuntungan', hasilTerakhir.keuntungan);
        formData.append('roi', hasilTerakhir.roi);

        // Kirim Request
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Parse respons JSON dari PHP
        .then(data => {
            if (data.status === 'success') {
                showNotification(data.message, 'success');
                    setTimeout(() => {
                    location.reload();
                    }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Gagal terhubung ke server.', 'error');
        });
    };

    // ========== FUNGSI RESET ==========
    window.resetForm = function() {
        if (confirm('Kosongkan formulir?')) {
            document.getElementById('kalkulatorForm').reset();
            hasilTerakhir = null;
            // Kembalikan tampilan hasil ke placeholder awal
            document.querySelector('.hasil-content').innerHTML = `
                <div class="hasil-placeholder">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <p>Isi form di samping untuk melihat hasil perhitungan</p>
                </div>
            `;
            showNotification('Formulir telah direset.', 'info');
        }
    };

    // ========== HELPER: FORMAT RUPIAH ==========
    function formatRupiah(angka) {
        return Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // ========== HELPER: NOTIFIKASI ==========
    function showNotification(message, type = 'success') {
        // Hapus notifikasi lama jika ada
        const oldNotif = document.querySelector('.custom-notification');
        if (oldNotif) oldNotif.remove();

        const colors = { success: '#2ecc71', error: '#e74c3c', info: '#3498db' };
        const notif = document.createElement('div');
        notif.className = 'custom-notification';
        notif.style.cssText = `
            position: fixed; top: 20px; right: 20px; 
            background: ${colors[type] || colors.info}; color: white; 
            padding: 15px 25px; border-radius: 8px; font-weight: 600; 
            z-index: 9999; box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out;
        `;
        notif.textContent = message;
        document.body.appendChild(notif);

        setTimeout(() => {
            notif.style.opacity = '0';
            notif.style.transform = 'translateY(-20px)';
            notif.style.transition = 'all 0.3s ease';
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }

    // Inject CSS animasi untuk notifikasi
    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }`;
    document.head.appendChild(style);
});