// forum.js - Sistem Forum dengan Pagination dan Buat Pertanyaan

// ========== DATA FORUM (Simulasi Database) ==========
let forumData = [
    // Page 1
    { id: 1, author: 'Aeri Uchinaga', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Apakah ikan bisa makan kucing?', comments: 0, likes: 0, shares: 0, date: '25/09/25', page: 1 },
    { id: 2, author: 'Choihyunwook', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Ikan bisa mati ga?', comments: 2000, likes: 1678, shares: 87, date: '12/03/25', page: 1 },
    { id: 3, author: 'Ningning', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Ikan makan nya apa?', comments: 0, likes: 0, shares: 0, date: '25/09/25', page: 1 },
    
    // Page 2
    { id: 4, author: 'ALfajar', role: 'Anggota', avatar: 'assets/img/aquara/profil.png', question: 'Bagaimana cara budidaya ikan lele yang baik?', comments: 45, likes: 234, shares: 12, date: '20/09/25', page: 2 },
    { id: 5, author: 'Winter Kim', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Apa perbedaan ikan nila dan ikan mujair?', comments: 23, likes: 156, shares: 8, date: '18/09/25', page: 2 },
    { id: 6, author: 'Giselle Aeri', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Tips memilih bibit ikan berkualitas?', comments: 67, likes: 345, shares: 23, date: '15/09/25', page: 2 },
    
    // Page 3
    { id: 7, author: 'Taeyeon Kim', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Berapa lama masa panen ikan gurame?', comments: 34, likes: 189, shares: 15, date: '10/09/25', page: 3 },
    { id: 8, author: 'Seulgi Kang', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Ikan bawal bisa dibudidaya di kolam terpal?', comments: 56, likes: 267, shares: 19, date: '08/09/25', page: 3 },
    { id: 9, author: 'Joy Park', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Cara mengatasi ikan yang sakit jamur?', comments: 89, likes: 412, shares: 31, date: '05/09/25', page: 3 },
    
    // Page 4
    { id: 10, author: 'Irene Bae', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Apakah sistem bioflok cocok untuk pemula?', comments: 78, likes: 356, shares: 28, date: '01/09/25', page: 4 },
    { id: 11, author: 'Wendy Son', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Rekomendasi pakan ikan yang ekonomis?', comments: 45, likes: 223, shares: 17, date: '28/08/25', page: 4 },
    { id: 12, author: 'Yeri Kim', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Cara mengukur kualitas air kolam ikan?', comments: 34, likes: 178, shares: 11, date: '25/08/25', page: 4 }
];

let currentPage = 1;
let currentUserAvatar = '/assets/img/aquara/profil.png';
let currentUserName = 'Aeri Uciha';

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize forum
    renderForumPosts(currentPage);
    
    // ========== BUAT PERTANYAAN BUTTON ==========
    const createQuestionBtn = document.querySelector('.cta-button');
    
    if (createQuestionBtn) {
        createQuestionBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showCreateQuestionModal();
        });
    }

    // ========== PAGINATION FUNCTIONALITY ==========
    initializePagination();

    // ========== SEARCH FUNCTIONALITY ==========
    initializeSearch();

    // ========== USER PROFILE DROPDOWN ==========
    const userProfile = document.querySelector('.user-profile');
    
    if (userProfile) {
        userProfile.style.cursor = 'pointer';
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleUserDropdown();
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown) {
            dropdown.remove();
        }
    });
});

// ========== RENDER FORUM POSTS ==========
function renderForumPosts(page) {
    const forumPostsContainer = document.querySelector('.forum-posts');
    const postsToShow = forumData.filter(post => post.page === page);
    
    // Clear existing posts
    const existingPosts = forumPostsContainer.querySelectorAll('.forum-post');
    existingPosts.forEach(post => post.remove());
    
    const existingDividers = forumPostsContainer.querySelectorAll('.divider:not(.top-divider)');
    existingDividers.forEach(divider => divider.remove());
    
    // Get top divider
    const topDivider = forumPostsContainer.querySelector('.top-divider');
    
    // Add posts for current page
    postsToShow.forEach((postData, index) => {
        const postElement = createPostElement(postData);
        forumPostsContainer.appendChild(postElement);
        
        // Add divider after each post except last
        if (index < postsToShow.length - 1) {
            const divider = document.createElement('hr');
            divider.className = 'divider';
            forumPostsContainer.appendChild(divider);
        }
    });
    
    // Initialize interactive features for new posts
    initializePostInteractions();
}

// ========== CREATE POST ELEMENT ==========
function createPostElement(postData) {
    const article = document.createElement('article');
    article.className = 'forum-post';
    article.dataset.postId = postData.id;
    
    article.innerHTML = `
        <div class="post-main">
            <img src="${postData.avatar}" alt="${postData.author} avatar" class="post-avatar">
            <div class="post-content">
                <div class="post-author">
                    <h4>${postData.author}</h4>
                    <div class="author-role">
                        <div class="author-role-icon-wrapper">
                            <div class="author-role-icon-shape"></div>
                            <img src="assets/img/aquara/anggotaforum.png" alt="" class="author-role-icon-fg">
                        </div>
                        <span>${postData.role}</span>
                    </div>
                </div>
                <p class="post-question">${postData.question}</p>
            </div>
        </div>
        <div class="post-meta">
            <div class="post-stats">
                <span class="stat-comment" style="cursor: pointer;">
                    <img src="assets/img/aquara/komentarlogo.png" alt=""> ${postData.comments} Komentar
                </span>
                <span class="stat-like" style="cursor: pointer;">
                    <img src="assets/img/aquara/likelogo.png" alt=""> ${postData.likes} Suka
                </span>
                <span class="stat-share" style="cursor: pointer;">
                    <img src="assets/img/aquara/bagikanlogo.png" alt=""> ${postData.shares} Bagikan
                </span>
            </div>
            <span class="post-date">Dibuat pada ${postData.date}</span>
        </div>
    `;
    
    return article;
}

// ========== INITIALIZE POST INTERACTIONS ==========
function initializePostInteractions() {
    // Like buttons
    const likeButtons = document.querySelectorAll('.stat-like');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            handleLike(this);
        });
    });
    
    // Comment buttons
    const commentButtons = document.querySelectorAll('.stat-comment');
    commentButtons.forEach(button => {
        button.addEventListener('click', function() {
            handleComment(this);
        });
    });
    
    // Share buttons
    const shareButtons = document.querySelectorAll('.stat-share');
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            handleShare(this);
        });
    });
}

// ========== HANDLE LIKE ==========
function handleLike(button) {
    const postElement = button.closest('.forum-post');
    const postId = parseInt(postElement.dataset.postId);
    const post = forumData.find(p => p.id === postId);
    
    if (button.classList.contains('liked')) {
        post.likes--;
        button.classList.remove('liked');
        button.style.color = 'var(--primary-color)';
    } else {
        post.likes++;
        button.classList.add('liked');
        button.style.color = '#e74c3c';
    }
    
    updateStatText(button, post.likes, 'Suka');
}

// ========== HANDLE COMMENT ==========
function handleComment(button) {
    const forumPost = button.closest('.forum-post');
    let commentSection = forumPost.querySelector('.comment-section');
    
    if (commentSection) {
        commentSection.remove();
    } else {
        commentSection = createCommentSection();
        forumPost.appendChild(commentSection);
        
        // Setup submit button
        const submitBtn = commentSection.querySelector('.submit-comment-btn');
        submitBtn.addEventListener('click', function() {
            submitComment(this);
        });
    }
}

// ========== HANDLE SHARE ==========
function handleShare(button) {
    const postElement = button.closest('.forum-post');
    const question = postElement.querySelector('.post-question').textContent;
    const author = postElement.querySelector('.post-author h4').textContent;
    const postId = parseInt(postElement.dataset.postId);
    const post = forumData.find(p => p.id === postId);
    
    showShareModal(question, author, post);
}

// ========== CREATE COMMENT SECTION ==========
function createCommentSection() {
    const section = document.createElement('div');
    section.className = 'comment-section';
    section.style.cssText = 'margin-top: 20px; padding: 20px; background: #f5f5f5; border-radius: 10px; margin-left: 187px;';
    
    section.innerHTML = `
        <h4 style="color: var(--primary-color); margin-bottom: 15px; font-size: 20px;">Tulis Komentar</h4>
        <textarea class="comment-textarea" placeholder="Tulis komentar Anda..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; min-height: 80px; resize: vertical;"></textarea>
        <button class="submit-comment-btn" style="margin-top: 10px; padding: 10px 24px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Kirim Komentar</button>
        <div class="comments-list" style="margin-top: 20px;"></div>
    `;
    
    return section;
}

// ========== SUBMIT COMMENT ==========
function submitComment(button) {
    const commentSection = button.closest('.comment-section');
    const textarea = commentSection.querySelector('.comment-textarea');
    const commentText = textarea.value.trim();
    
    if (commentText === '') {
        showNotification('Mohon tulis komentar terlebih dahulu!', 'error');
        return;
    }
    
    const postElement = commentSection.closest('.forum-post');
    const postId = parseInt(postElement.dataset.postId);
    const post = forumData.find(p => p.id === postId);
    
    // Update comment count in data
    post.comments++;
    
    // Add comment to UI
    const commentsList = commentSection.querySelector('.comments-list');
    const commentDiv = document.createElement('div');
    commentDiv.style.cssText = 'padding: 15px; background: white; border-radius: 8px; margin-bottom: 10px; border-left: 3px solid var(--primary-color);';
    commentDiv.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <img src="${currentUserAvatar}" style="width: 30px; height: 30px; border-radius: 50%;">
            <strong style="color: var(--primary-color);">${currentUserName}</strong>
            <span style="color: #999; font-size: 12px;">Baru saja</span>
        </div>
        <p style="color: #333; line-height: 1.6; margin: 0;">${commentText}</p>
    `;
    
    commentsList.insertBefore(commentDiv, commentsList.firstChild);
    
    // Update comment count in UI
    const commentButton = postElement.querySelector('.stat-comment');
    updateStatText(commentButton, post.comments, 'Komentar');
    
    // Clear textarea
    textarea.value = '';
    
    showNotification('Komentar berhasil ditambahkan!', 'success');
}

// ========== SHOW SHARE MODAL ==========
function showShareModal(question, author, post) {
    const modal = document.createElement('div');
    modal.className = 'share-modal';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999;';
    
    modal.innerHTML = `
        <div style="background: white; padding: 40px; border-radius: 20px; max-width: 500px; width: 90%;">
            <h3 style="color: var(--primary-color); margin-bottom: 20px; font-size: 24px;">Bagikan Pertanyaan</h3>
            <p style="margin-bottom: 20px; color: #666; line-height: 1.6;"><strong>${author}</strong> bertanya: "${question}"</p>
            <div style="display: flex; gap: 15px; justify-content: center; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="share-fb-btn" style="padding: 12px 24px; background: #1877f2; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Facebook</button>
                <button class="share-tw-btn" style="padding: 12px 24px; background: #1da1f2; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Twitter</button>
                <button class="share-wa-btn" style="padding: 12px 24px; background: #25d366; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">WhatsApp</button>
            </div>
            <button class="close-modal-btn" style="width: 100%; padding: 12px; background: #ddd; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Tutup</button>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Event listeners
    modal.querySelector('.share-fb-btn').addEventListener('click', () => {
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank');
        post.shares++;
        updateShareCount(post);
        showNotification('Dibagikan ke Facebook!', 'success');
        modal.remove();
    });
    
    modal.querySelector('.share-tw-btn').addEventListener('click', () => {
        window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(question), '_blank');
        post.shares++;
        updateShareCount(post);
        showNotification('Dibagikan ke Twitter!', 'success');
        modal.remove();
    });
    
    modal.querySelector('.share-wa-btn').addEventListener('click', () => {
        window.open('https://wa.me/?text=' + encodeURIComponent(question + ' - ' + window.location.href), '_blank');
        post.shares++;
        updateShareCount(post);
        showNotification('Dibagikan ke WhatsApp!', 'success');
        modal.remove();
    });
    
    modal.querySelector('.close-modal-btn').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function updateShareCount(post) {
    const postElement = document.querySelector(`[data-post-id="${post.id}"]`);
    if (postElement) {
        const shareButton = postElement.querySelector('.stat-share');
        updateStatText(shareButton, post.shares, 'Bagikan');
    }
}

// ========== PAGINATION ==========
function initializePagination() {
    const paginationLinks = document.querySelectorAll('.pagination a');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const text = this.textContent.trim();
            
            if (text === 'Next>') {
                if (currentPage < 4) {
                    currentPage++;
                }
            } else if (!isNaN(text)) {
                currentPage = parseInt(text);
            }
            
            // Update active state
            paginationLinks.forEach(l => l.classList.remove('active'));
            
            // Find and activate the current page number
            paginationLinks.forEach(l => {
                if (l.textContent.trim() === currentPage.toString()) {
                    l.classList.add('active');
                }
            });
            
            // Render posts for current page
            renderForumPosts(currentPage);
            
            // Scroll to top of forum
            document.querySelector('.forum-posts').scrollIntoView({ behavior: 'smooth' });
        });
    });
}

// ========== CREATE QUESTION MODAL ==========
function showCreateQuestionModal() {
    const modal = document.createElement('div');
    modal.className = 'create-question-modal';
    modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; overflow-y: auto; padding: 20px;';
    
    modal.innerHTML = `
        <div style="background: white; padding: 40px; border-radius: 20px; max-width: 600px; width: 100%;">
            <h3 style="color: var(--primary-color); margin-bottom: 20px; font-size: 28px;">Buat Pertanyaan Baru</h3>
            <textarea id="newQuestionText" placeholder="Tulis pertanyaan Anda di sini..." style="width: 100%; padding: 15px; border: 1.5px solid #ddd; border-radius: 10px; font-family: inherit; min-height: 150px; resize: vertical; font-size: 16px; margin-bottom: 20px;"></textarea>
            <div style="display: flex; gap: 15px;">
                <button class="post-question-btn" style="flex: 1; padding: 14px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px;">Posting Pertanyaan</button>
                <button class="cancel-btn" style="flex: 1; padding: 14px; background: #ddd; color: #333; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px;">Batal</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Event listeners
    modal.querySelector('.post-question-btn').addEventListener('click', () => {
        submitNewQuestion(modal);
    });
    
    modal.querySelector('.cancel-btn').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// ========== SUBMIT NEW QUESTION ==========
function submitNewQuestion(modal) {
    const questionText = document.getElementById('newQuestionText').value.trim();
    
    if (questionText === '') {
        showNotification('Mohon tulis pertanyaan terlebih dahulu!', 'error');
        return;
    }
    
    // Create new post data
    const newPost = {
        id: forumData.length + 1,
        author: currentUserName,
        role: 'Anggota',
        avatar: currentUserAvatar,
        question: questionText,
        comments: 0,
        likes: 0,
        shares: 0,
        date: getCurrentDate(),
        page: 1 // Add to first page
    };
    
    // Add to data array at the beginning
    forumData.unshift(newPost);
    
    // Re-organize pages (shift old posts to next pages)
    reorganizePages();
    
    // Close modal
    modal.remove();
    
    // Go to page 1 and re-render
    currentPage = 1;
    renderForumPosts(currentPage);
    
    // Update pagination active state
    document.querySelectorAll('.pagination a').forEach(l => l.classList.remove('active'));
    document.querySelector('.pagination a').classList.add('active');
    
    // Scroll to top
    document.querySelector('.forum-posts').scrollIntoView({ behavior: 'smooth' });
    
    showNotification('Pertanyaan berhasil diposting!', 'success');
}

// ========== REORGANIZE PAGES ==========
function reorganizePages() {
    const postsPerPage = 3;
    forumData.forEach((post, index) => {
        post.page = Math.floor(index / postsPerPage) + 1;
    });
}

// ========== SEARCH ==========
function initializeSearch() {
    const searchForm = document.querySelector('.search-bar');
    const searchInput = searchForm.querySelector('input');
    
    searchInput.placeholder = 'Cari pertanyaan atau nama pengguna...';
    
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        performSearch(searchInput.value);
    });
    
    searchInput.addEventListener('input', () => {
        if (searchInput.value.trim() === '') {
            renderForumPosts(currentPage);
            document.querySelector('.posts-filter-title').textContent = 'Semua';
        }
    });
}

function performSearch(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    
    if (term === '') {
        renderForumPosts(currentPage);
        return;
    }
    
    const results = forumData.filter(post => 
        post.question.toLowerCase().includes(term) || 
        post.author.toLowerCase().includes(term)
    );
    
    // Clear and render results
    const forumPostsContainer = document.querySelector('.forum-posts');
    const existingPosts = forumPostsContainer.querySelectorAll('.forum-post');
    existingPosts.forEach(post => post.remove());
    
    const existingDividers = forumPostsContainer.querySelectorAll('.divider:not(.top-divider)');
    existingDividers.forEach(divider => divider.remove());
    
    if (results.length === 0) {
        const noResults = document.createElement('p');
        noResults.style.cssText = 'text-align: center; padding: 40px; color: #999; font-size: 18px;';
        noResults.textContent = 'Tidak ada hasil yang ditemukan';
        forumPostsContainer.appendChild(noResults);
        document.querySelector('.posts-filter-title').textContent = `Hasil Pencarian (0)`;
    } else {
        results.forEach((postData, index) => {
            const postElement = createPostElement(postData);
            forumPostsContainer.appendChild(postElement);
            
            if (index < results.length - 1) {
                const divider = document.createElement('hr');
                divider.className = 'divider';
                forumPostsContainer.appendChild(divider);
            }
        });
        
        document.querySelector('.posts-filter-title').textContent = `Hasil Pencarian (${results.length})`;
        initializePostInteractions();
    }
}

// ========== HELPER FUNCTIONS ==========
function updateStatText(element, count, label) {
    const img = element.querySelector('img');
    element.innerHTML = '';
    element.appendChild(img);
    element.appendChild(document.createTextNode(` ${count} ${label}`));
}

function getCurrentDate() {
    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = String(now.getFullYear()).slice(-2);
    return `${day}/${month}/${year}`;
}

function toggleUserDropdown() {
    let dropdown = document.querySelector('.user-dropdown');
    
    if (dropdown) {
        dropdown.remove();
        return;
    }
    
    dropdown = document.createElement('div');
    dropdown.className = 'user-dropdown';
    dropdown.style.cssText = 'position: absolute; top: 100%; right: 0; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 10px 0; min-width: 180px; margin-top: 10px; z-index: 1000;';
    
    dropdown.innerHTML = `
        <a href="index.php?page=profile" style="display: block; padding: 12px 20px; color: var(--primary-color); transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='white'">Profil Saya</a>
        <a href="index.php?page=my_questions" style="display: block; padding: 12px 20px; color: var(--primary-color); transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='white'">Pertanyaan Saya</a>
        <a href="index.php?page=settings" style="display: block; padding: 12px 20px; color: var(--primary-color); transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='white'">Pengaturan</a>
        <hr style="margin: 5px 0; border: none; height: 1px; background: #ddd;">
        <a href="index.php?page=logout" style="display: block; padding: 12px 20px; color: #e74c3c; transition: background 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='white'">Keluar</a>
    `;
    
    const userProfile = document.querySelector('.user-profile');
    userProfile.style.position = 'relative';
    userProfile.appendChild(dropdown);
}

function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 'var(--primary-color)' : '#e74c3c';
    
    const notification = document.createElement('div');
    notification.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${bgColor}; color: white; padding: 16px 24px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); z-index: 10000; animation: slideInRight 0.3s ease;`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
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
`;
document.head.appendChild(style);