// konsultasi_pengunjung.js - Halaman konsultasi untuk pengunjung (belum login)

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== FORM ELEMENTS - READ ONLY FOR GUEST ==========
    const formInputs = document.querySelectorAll('.konsultasi-form input, .konsultasi-form select, .konsultasi-form textarea');
    
    // Pastikan semua form input tidak bisa diisi oleh pengunjung
    formInputs.forEach(input => {
        input.addEventListener('click', function() {
            showLoginPrompt('mengisi form konsultasi');
        });
        
        input.addEventListener('focus', function() {
            this.blur(); // Hilangkan fokus
            showLoginPrompt('mengisi form konsultasi');
        });
    });

    // ========== PROMPT LOGIN - Pengunjung harus login ==========
    window.showLoginPrompt = function(action) {
        // Cek jika modal sudah ada, jangan buat lagi
        if (document.querySelector('.login-modal')) {
            return;
        }

        let message = '';
        if (action === 'submit form') {
            message = 'Untuk mengirim konsultasi, Anda harus login atau register terlebih dahulu.';
        } else if (action === 'mengisi form konsultasi') {
            message = 'Untuk mengisi form konsultasi, Anda harus login atau register terlebih dahulu.';
        } else {
            message = `Untuk berkonsultasi dengan <strong style="color: var(--primary-color);">${action}</strong>, Anda harus login atau register terlebih dahulu.`;
        }

        const modal = document.createElement('div');
        modal.className = 'login-modal';
        modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999;';
        
        modal.innerHTML = `
            <div style="background: white; padding: 40px; border-radius: 20px; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3); text-align: center; animation: modalFadeIn 0.3s ease;">
                <div style="width: 60px; height: 60px; background-color: #f39c12; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                        <path d="M18 12V18M18 24H18.015M33 18C33 26.2843 26.2843 33 18 33C9.71573 33 3 26.2843 3 18C3 9.71573 9.71573 3 18 3C26.2843 3 33 9.71573 33 18Z" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 style="color: var(--primary-color); margin-bottom: 10px; font-size: 24px;">Login Diperlukan</h3>
                <p style="color: var(--text-dark); margin-bottom: 20px; font-size: 16px; line-height: 1.6;">
                    ${message}
                </p>
                <div style="display: flex; gap: 10px; margin-top: 25px;">
                    <a href="index.php?page=login" style="flex: 1; padding: 12px; background-color: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; text-decoration: none; display: inline-block; transition: background-color 0.3s;">Login</a>
                    <a href="index.php?page=register" style="flex: 1; padding: 12px; background-color: #60cece; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px; text-decoration: none; display: inline-block; transition: background-color 0.3s;">Register</a>
                </div>
                <button onclick="closeLoginModal()" style="width: 100%; margin-top: 10px; padding: 10px; background-color: transparent; color: var(--text-muted); border: none; cursor: pointer; font-weight: 500; font-size: 14px;">Nanti Saja</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal saat klik di luar
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeLoginModal();
            }
        });

        showNotification('Silakan login untuk melanjutkan', 'warning');
    };

    // ========== CLOSE MODAL ==========
    window.closeLoginModal = function() {
        const modal = document.querySelector('.login-modal');
        if (modal) {
            modal.style.animation = 'modalFadeOut 0.3s ease';
            setTimeout(() => modal.remove(), 300);
        }
    };

    // ========== NOTIFICATION ==========
    function showNotification(message, type = 'success') {
        let bgColor;
        if (type === 'success') bgColor = 'var(--primary-color)';
        else if (type === 'warning') bgColor = '#f39c12';
        else bgColor = '#e74c3c';
        
        const notification = document.createElement('div');
        notification.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${bgColor}; color: white; padding: 16px 24px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); z-index: 10000; animation: slideInRight 0.3s ease; font-weight: 500; max-width: 350px;`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // ========== BUTTON KONSULTASI - AHLI CARDS ==========
    const konsultasiButtons = document.querySelectorAll('.btn-konsultasi');
    konsultasiButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            // Function showLoginPrompt sudah dipanggil dari onclick attribute
        });
    });

    // ========== SUBMIT BUTTON ==========
    const submitButton = document.querySelector('.btn-submit');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            // Function showLoginPrompt sudah dipanggil dari onclick attribute
        });
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
        @keyframes modalFadeIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes modalFadeOut {
            from { transform: scale(1); opacity: 1; }
            to { transform: scale(0.8); opacity: 0; }
        }
        .btn-konsultasi:hover {
            background-color: #307b7b !important;
        }
        .btn-submit:hover {
            background-color: #307b7b !important;
        }
    `;
    document.head.appendChild(style);

    // ========== PREVENT COPY PASTE ==========
    formInputs.forEach(input => {
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            showNotification('Silakan login untuk mengisi form', 'warning');
        });
    });
});