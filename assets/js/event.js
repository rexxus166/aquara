// event_pengunjung.js - Halaman event untuk pengunjung (belum login)

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== DATA EVENTS ==========
    const eventsData = {
        1: {
            title: 'Pelatihan Budidaya Lele Modern',
            type: 'Offline',
            date: '20 Mei 2025',
            time: '09:00 - 16:00 WIB',
            location: 'Kampus utama Polindra',
            participants: '45/50 peserta',
            description: 'Pelatihan ini akan membahas teknik budidaya lele modern, termasuk pemilihan bibit berkualitas, pengelolaan pakan efisien, pencegahan penyakit, dan optimalisasi produksi. Acara ini cocok untuk pemula dan ahli yang ingin meningkatkan skill budidaya lele mereka.'
        },
        2: {
            title: 'Pelatihan Budidaya Lele Modern (Online)',
            type: 'Online',
            date: '20 Mei 2025',
            time: '09:00 - 16:00 WIB',
            location: 'Platform Zoom',
            participants: '45/50 peserta',
            description: 'Pelatihan virtual ini mencakup webinar interaktif tentang budidaya lele modern dengan akses ke materi digital lengkap dan sesi tanya-jawab langsung dengan expert. Anda akan mendapatkan sertifikat digital setelah menyelesaikan program.'
        },
        3: {
            title: 'Workshop Praktik Lapangan Budidaya Lele',
            type: 'Offline',
            date: '20 Mei 2025',
            time: '09:00 - 16:00 WIB',
            location: 'Kampus utama Polindra',
            participants: '45/50 peserta',
            description: 'Workshop intensif dengan fokus pada praktik lapangan, termasuk demo langsung di kolam ikan, observasi teknik pemberian pakan, dan pengendalian penyakit. Peserta akan mendapatkan pengalaman langsung terbimbing oleh ahli.'
        },
        4: {
            title: 'Seminar Intensif Budidaya Lele Berkelanjutan',
            type: 'Offline',
            date: '20 Mei 2025',
            time: '09:00 - 16:00 WIB',
            location: 'Kampus utama Polindra',
            participants: '45/50 peserta',
            description: 'Sesi seminar intensif dengan ahli industri, termasuk materi tentang budidaya berkelanjutan, manajemen lingkungan, dan strategi bisnis. Peserta akan menerima sertifikat partisipasi dan akses ke forum alumni untuk networking berkelanjutan.'
        }
    };

    // ========== DESKRIPSI EVENT - TOGGLE (Pengunjung bisa lihat deskripsi) ==========
    const descriptionButtons = document.querySelectorAll('.btn-description');
    
    descriptionButtons.forEach((button, index) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const eventCard = this.closest('.event-card');
            const descriptionDiv = eventCard.querySelector('.event-description');
            const eventIndex = index + 1;
            const eventInfo = eventsData[eventIndex];
            
            if (descriptionDiv.style.display === 'none' || descriptionDiv.style.display === '') {
                // Show description
                descriptionDiv.innerHTML = `
                    <div style="background-color: #f0f0f0; padding: 20px; border-radius: 10px; margin-top: 15px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 10px; font-size: 20px; font-weight: 600;">Deskripsi Event</h4>
                        <p style="color: var(--text-dark); line-height: 1.8; font-size: 16px; margin-bottom: 15px;">${eventInfo.description}</p>
                        <div style="background-color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid var(--primary-color);">
                            <p style="color: var(--text-muted); margin: 5px 0;"><strong>Tipe:</strong> ${eventInfo.type}</p>
                            <p style="color: var(--text-muted); margin: 5px 0;"><strong>Lokasi:</strong> ${eventInfo.location}</p>
                            <p style="color: var(--text-muted); margin: 5px 0;"><strong>Peserta:</strong> ${eventInfo.participants}</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn-daftar-guest" onclick="showLoginPrompt('${eventInfo.title}')" style="flex: 1; background-color: var(--primary-color); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; transition: background-color 0.3s;">Daftar Event</button>
                            <button class="btn-close-desc" onclick="tutupDeskripsi(this)" style="flex: 1; background-color: #ddd; color: var(--text-dark); border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; transition: background-color 0.3s;">Tutup</button>
                        </div>
                    </div>
                `;
                descriptionDiv.style.display = 'block';
                this.textContent = 'Sembunyikan Deskripsi';
                this.style.backgroundColor = 'var(--primary-color)';
                
                // Smooth scroll ke deskripsi
                descriptionDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                // Hide description
                tutupDeskripsi(button);
            }
        });
    });

    // ========== CLOSE BUTTON DALAM DESKRIPSI ==========
    function tutupDeskripsi(button) {
        const eventCard = button.closest('.event-card') || button.closest('.event-description').closest('.event-card');
        const descriptionDiv = eventCard.querySelector('.event-description');
        const descButton = eventCard.querySelector('.btn-description');
        
        descriptionDiv.style.display = 'none';
        descButton.textContent = 'Deskripsi Event';
        descButton.style.backgroundColor = 'rgba(63, 134, 134, 0.69)';
    }

    // ========== PROMPT LOGIN - Pengunjung harus login untuk daftar event ==========
    window.showLoginPrompt = function(eventTitle) {
        const modal = document.createElement('div');
        modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        modal.innerHTML = `
            <div style="background: white; padding: 40px; border-radius: 20px; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3); text-align: center;">
                <div style="width: 60px; height: 60px; background-color: #f39c12; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                        <path d="M18 12V18M18 24H18.015M33 18C33 26.2843 26.2843 33 18 33C9.71573 33 3 26.2843 3 18C3 9.71573 9.71573 3 18 3C26.2843 3 33 9.71573 33 18Z" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 style="color: var(--primary-color); margin-bottom: 10px; font-size: 24px;">Login Diperlukan</h3>
                <p style="color: var(--text-dark); margin-bottom: 20px; font-size: 16px; line-height: 1.6;">
                    Untuk mendaftar event <strong style="color: var(--primary-color);">${eventTitle}</strong>, Anda harus login atau register terlebih dahulu.
                </p>
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <a href="index.php?page=login" style="flex: 1; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; text-decoration: none; display: inline-block; transition: background-color 0.3s;">Login</a>
                    <a href="index.php?page=register" style="flex: 1; padding: 12px; background-color: #60cece; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; text-decoration: none; display: inline-block; transition: background-color 0.3s;">Register</a>
                </div>
                <button onclick="this.closest('div').parentElement.remove()" style="width: 100%; margin-top: 10px; padding: 10px; background-color: transparent; color: var(--text-muted); border: none; cursor: pointer; font-weight: 500; font-size: 14px;">Nanti Saja</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        showNotification('Silakan login untuk mendaftar event', 'warning');
    };

    // ========== NOTIFICATION ==========
    function showNotification(message, type = 'success') {
        let bgColor;
        if (type === 'success') bgColor = 'var(--primary-color)';
        else if (type === 'warning') bgColor = '#f39c12';
        else bgColor = '#e74c3c';
        
        const notification = document.createElement('div');
        notification.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${bgColor}; color: white; padding: 16px 24px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); z-index: 10000; animation: slideInRight 0.3s ease; font-weight: 500;`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // ========== ANIMATIONS ==========
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
        .btn-daftar-guest:hover {
            background-color: #307b7b !important;
        }
        .btn-close-desc:hover {
            background-color: #ccc !important;
        }
    `;
    document.head.appendChild(style);
});

// ========== HELPER FUNCTION (Global) ==========
window.tutupDeskripsi = function(button) {
    const eventCard = button.closest('.event-card');
    const descriptionDiv = eventCard.querySelector('.event-description');
    const descButton = eventCard.querySelector('.btn-description');
    
    descriptionDiv.style.display = 'none';
    descButton.textContent = 'Deskripsi Event';
    descButton.style.backgroundColor = 'rgba(63, 134, 134, 0.69)';
};