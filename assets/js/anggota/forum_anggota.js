// forum_anggota.js - Forum interaktif untuk anggota

document.addEventListener('DOMContentLoaded', function() {
    
    console.log('🚀 Forum Anggota JS Loading...');
    
    // ========== SEARCH BAR ==========
    const searchForm = document.querySelector('.search-bar');
    const searchInput = searchForm?.querySelector('input');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Kita biarkan search ditangani oleh PHP (halaman refresh)
            const query = searchInput.value.trim();
            console.log('🔍 Search (via PHP):', query);
        });
        
        searchInput.addEventListener('focus', function() {
            this.style.borderColor = 'var(--primary-color)';
            this.style.boxShadow = '0 0 0 3px rgba(63, 134, 134, 0.1)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.borderColor = 'rgba(0, 0, 0, 0.2)';
        });
    }
    
    // ========== BUTTON BUAT PERTANYAAN ==========
    const ctaButton = document.querySelector('.cta-button');
    
    if (ctaButton) {
        ctaButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('📝 Buat Pertanyaan clicked');
            buatPertanyaan();
        });
    }
    
    // ========== FORUM POST INTERACTIONS ==========
    const forumPostElements = document.querySelectorAll('.forum-post');
    
    forumPostElements.forEach((postElement) => {
        
        // BACA DATA DARI HTML (data-*)
        const post = {
            id: postElement.dataset.id,
            author: postElement.dataset.author,
            role: postElement.dataset.role,
            avatar: postElement.dataset.avatar,
            question: postElement.dataset.question,
            comments: parseInt(postElement.dataset.comments) || 0,
            likes: parseInt(postElement.dataset.likes) || 0,
            shares: parseInt(postElement.dataset.shares) || 0,
            date: postElement.dataset.date,
            liked: false
        };

        // KLIK POST = BUKA DETAIL
        postElement.addEventListener('click', function(e) {
            if (e.target.closest('.post-stats span')) return;
            console.log('📖 Open post:', post.id);
            // openPostDetail(post); // Kita ganti ke link PHP
            window.location.href = `index_anggota.php?page=forum_detail&id=${post.id}`;
        });
        
        // KOMENTAR
        const commentStat = postElement.querySelector('.post-stats span:nth-child(1)');
        if (commentStat) {
            commentStat.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('💬 Comments clicked:', post.id);
                // openPostDetail(post); // Kita ganti ke link PHP
                window.location.href = `index_anggota.php?page=forum_detail&id=${post.id}`;
            });
        }
        
        // LIKE (Simulasi)
        const likeStat = postElement.querySelector('.post-stats span:nth-child(2)');
        if (likeStat) {
            likeStat.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('👍 Like clicked:', post.id);
                toggleLike(post, likeStat);
            });
        }
        
        // SHARE (Simulasi)
        const shareStat = postElement.querySelector('.post-stats span:nth-child(3)');
        if (shareStat) {
            shareStat.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('📤 Share clicked:', post.id);
                sharePost(post);
            });
        }
    });
    
    // ========== TOGGLE LIKE (Simulasi) ==========
    function toggleLike(post, element) {
        post.liked = !post.liked;
        const icon = element.querySelector('i');
        
        if (post.liked) {
            post.likes++;
            icon.className = 'bi bi-hand-thumbs-up-fill';
            icon.style.color = '#24a2a2';
            element.style.transform = 'scale(1.1)';
            showNotification('Post disukai! ❤', 'success');
            setTimeout(() => { element.style.transform = 'scale(1)'; }, 300);
        } else {
            post.likes--;
            icon.className = 'bi bi-hand-thumbs-up';
            icon.style.color = '';
            showNotification('Suka dibatalkan', 'info');
        }
        
        const text = element.childNodes[2];
        if (text) {
            text.textContent = ` ${post.likes} Suka`;
        }
    }
    
    // ========== SHARE POST (Simulasi) ==========
    function sharePost(post) {
        post.shares++;
        const link = `${window.location.origin}/aquara/views/anggota/index_anggota.php?page=forum_detail&id=${post.id}`;
        
        if (navigator.share) {
            navigator.share({
                title: `Forum AQUARA - ${post.author}`,
                text: post.question,
                url: link
            }).then(() => {
                showNotification('Berhasil dibagikan!', 'success');
            }).catch(() => {
                copyToClipboard(link);
            });
        } else {
            copyToClipboard(link);
        }
    }
    
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link berhasil disalin!', 'success');
        }).catch(() => {
            showNotification('Gagal menyalin link', 'error');
        });
    }
    
    // ========== OPEN POST DETAIL (Fungsi ini tidak dipakai lagi) ==========
    // function openPostDetail(post) { ... }
    
    // ========== MODAL ACTIONS ==========
    window.closeModal = function() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => modal.remove(), 300);
        }
    };
    
    // (Fungsi-fungsi modal detail kita hapus karena detail akan jadi halaman baru)
    
    // ========== BUAT PERTANYAAN (MODAL) ==========
    function buatPertanyaan() {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; animation: fadeIn 0.3s ease; padding: 20px;';
        
        modal.innerHTML = `
            <div class="modal-content" style="background: white; padding: 40px; border-radius: 20px; max-width: 600px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: slideUp 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="color: var(--primary-color); margin: 0; font-size: 28px; font-weight: 700;">
                        <i class="bi bi-plus-circle"></i> Buat Pertanyaan Baru
                    </h3>
                    <button onclick="closeModal()" style="background: none; border: none; font-size: 32px; color: #999; cursor: pointer; line-height: 1;">&times;</button>
                </div>
                
                <form id="formPertanyaan" style="display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary-dark);">
                            <i class="bi bi-question-circle"></i> Judul Pertanyaan
                        </label>
                        <input type="text" id="judulPertanyaan" required placeholder="Tuliskan judul pertanyaan Anda..." style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; font-family: 'Poppins', sans-serif;">
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary-dark);">
                            <i class="bi bi-textarea-t"></i> Deskripsi (Opsional)
                        </label>
                        <textarea id="deskripsiPertanyaan" rows="5" placeholder="Jelaskan pertanyaan Anda secara detail..." style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; font-family: 'Poppins', sans-serif; resize: vertical;"></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="submit" id="btnSubmitPertanyaan" style="flex: 1; background-color: var(--primary-color); color: white; border: none; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            <i class="bi bi-send"></i> Posting
                        </button>
                        <button type="button" onclick="closeModal()" style="flex: 1; background-color: #ddd; color: var(--primary-dark); border: none; padding: 14px; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        document.getElementById('formPertanyaan').addEventListener('submit', function(e) {
            e.preventDefault();
            submitPertanyaan();
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    // =======================================================
    // FUNGSI INI DIUBAH TOTAL
    // =======================================================
    function submitPertanyaan() {
        const judul = document.getElementById('judulPertanyaan').value;
        const deskripsi = document.getElementById('deskripsiPertanyaan').value;
        const submitButton = document.getElementById('btnSubmitPertanyaan');
        
        if (!judul) {
            showNotification('Mohon isi judul pertanyaan', 'error');
            return;
        }
        
        showNotification('Mengirim pertanyaan...', 'info');
        submitButton.disabled = true;
        submitButton.textContent = 'Mengirim...';

        // Kirim data ke API baru
        fetch('/aquara/api/create_forum_topic.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                judul: judul,
                deskripsi: deskripsi
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                showNotification('Pertanyaan berhasil diposting!', 'success');
                // Refresh halaman untuk melihat post baru
                setTimeout(() => {
                    location.reload(); 
                }, 1500); // Beri waktu 1.5 detik untuk notif
            } else {
                showNotification(data.message || 'Gagal memposting pertanyaan', 'error');
                submitButton.disabled = false;
                submitButton.textContent = 'Posting';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan jaringan.', 'error');
            submitButton.disabled = false;
            submitButton.textContent = 'Posting';
        });
    }
    // =======================================================
    
    // ========== PAGINATION (Simulasi) ==========
    const paginationLinks = document.querySelectorAll('.pagination a');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            paginationLinks.forEach(l => l.classList.remove('active'));
            if (!this.textContent.includes('Next')) {
                this.classList.add('active');
            }
            const page = this.textContent.trim();
            showNotification(`Memuat halaman ${page}... (Simulasi)`, 'info');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    
    // ========== NOTIFICATION ==========
    function showNotification(message, type = 'success') {
        let bgColor;
        if (type === 'success') bgColor = 'var(--primary-color)';
        else if (type === 'error') bgColor = '#e74c3c';
        else if (type === 'info') bgColor = '#3498db';
        else if (type === 'warning') bgColor = '#f39c12';
        
        const notif = document.createElement('div');
        notif.className = 'notification';
        notif.style.cssText = `position: fixed !important; top: 20px !important; right: 20px !important; background: ${bgColor} !important; color: white !important; padding: 16px 24px !important; border-radius: 10px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.2) !important; z-index: 10001 !important; animation: slideIn 0.3s ease !important; font-weight: 500 !important; max-width: 350px !important; font-family: 'Poppins', sans-serif !important;;`;
        notif.textContent = message;
        
        document.body.appendChild(notif);
        
        setTimeout(() => {
            notif.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }
    
    // ========== ANIMATIONS ==========
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    `;
    document.head.appendChild(style);
    
    console.log('✅ Forum Anggota JS Loaded!');
});