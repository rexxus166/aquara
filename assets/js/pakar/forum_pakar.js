// ============================
// FORUM PAKAR INTERAKTIF SCRIPT
// ============================
// Fitur: Like, Comment, View, Pagination, Filter, Modal Buat Pertanyaan
// Kompatibel dengan file: forum_pakar.php dan forum_pakar.css
// Dibuat untuk akun pakar (memiliki akses penuh)
// ============================

document.addEventListener('DOMContentLoaded', () => {
  const postsPerPage = 3;
  let currentPage = 1;

  const forumPostsWrapper = document.querySelector('.forum-posts');
  const paginationBtns = Array.from(document.querySelectorAll('.pagination-btn'));
  const createQuestionBtn = document.querySelector('.btn-buat-pertanyaan');
  const posts = Array.from(document.querySelectorAll('.forum-post'));

  // Init semua fungsi
  initPagination();
  renderPage(currentPage);
  attachGlobalDelegation();
  attachFilterButtons();
  attachCreateQuestion();

  // ==========================================
  //  PAGINATION
  // ==========================================
  function initPagination() {
    if (!paginationBtns.length) return;
    paginationBtns.forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        if (btn.disabled) return;

        const text = btn.textContent.trim();
        const maxPage = Math.ceil(posts.length / postsPerPage);

        if (/^Next/i.test(text)) {
          if (currentPage < maxPage) currentPage++;
        } else if (/^Prev|^</i.test(text)) {
          if (currentPage > 1) currentPage--;
        } else if (!isNaN(Number(text))) {
          currentPage = Number(text);
        }

        updatePaginationActive();
        renderPage(currentPage);
        scrollToTop();
      });
    });
  }

  function renderPage(page) {
    const start = (page - 1) * postsPerPage;
    const end = start + postsPerPage;
    posts.forEach((p, idx) => {
      p.style.display = idx >= start && idx < end ? '' : 'none';
    });
  }

  function updatePaginationActive() {
    paginationBtns.forEach(b => b.classList.remove('active'));
    const btn = paginationBtns.find(b => b.textContent.trim() === String(currentPage));
    if (btn) btn.classList.add('active');
  }

  function scrollToTop() {
    window.scrollTo({ top: forumPostsWrapper.offsetTop - 80, behavior: 'smooth' });
  }

  // ==========================================
  //  GLOBAL EVENT DELEGATION (LIKE, COMMENT, VIEW)
  // ==========================================
  function attachGlobalDelegation() {
    document.querySelector('.forum-posts').addEventListener('click', function (e) {
      // LIKE
      if (e.target.closest('.stat-item')?.querySelector('.bi-hand-thumbs-up')) {
        const stat = e.target.closest('.stat-item');
        toggleLike(stat);
        return;
      }

      // KOMENTAR
      if (e.target.closest('.stat-item')?.querySelector('.bi-chat-dots')) {
        const post = e.target.closest('.forum-post');
        toggleCommentSection(post);
        return;
      }

      // LIHAT
      if (e.target.closest('.stat-item')?.querySelector('.bi-eye')) {
        const stat = e.target.closest('.stat-item');
        incrementView(stat);
        return;
      }
    });
  }

  // ==========================================
  //  AKSI: LIKE / KOMENTAR / VIEW
  // ==========================================
  function toggleLike(item) {
    const num = extractNumber(item.textContent) || 0;
    const liked = item.classList.toggle('liked');
    const newCount = liked ? num + 1 : Math.max(num - 1, 0);
    setStatNumber(item, newCount);
    item.style.color = liked ? '#e74c3c' : '';
    pulse(item);
  }

  function incrementView(item) {
    const num = extractNumber(item.textContent) || 0;
    setStatNumber(item, num + 1);
    flash(item);
  }

  function toggleCommentSection(postEl) {
    let commentSection = postEl.querySelector('.comment-section');
    if (commentSection) {
      commentSection.remove();
      return;
    }

    commentSection = document.createElement('div');
    commentSection.className = 'comment-section';
    commentSection.innerHTML = `
      <div class="comment-box" style="margin-top:16px;padding:15px;background:#fff;border:1px solid #ddd;border-radius:10px;">
        <textarea class="comment-textarea" placeholder="Tulis komentar..." style="width:100%;min-height:80px;padding:10px;border:1px solid #ddd;border-radius:8px;"></textarea>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
          <button class="btn-cancel-comment" style="background:#f0f0f0;border:1px solid #ddd;border-radius:8px;padding:8px 14px;cursor:pointer">Batal</button>
          <button class="btn-submit-comment" style="background:var(--primary-color);color:#fff;border:none;border-radius:8px;padding:8px 14px;cursor:pointer">Kirim</button>
        </div>
        <div class="comments-list" style="margin-top:12px;"></div>
      </div>
    `;
    postEl.appendChild(commentSection);

    const cancelBtn = commentSection.querySelector('.btn-cancel-comment');
    const submitBtn = commentSection.querySelector('.btn-submit-comment');
    const textarea = commentSection.querySelector('.comment-textarea');
    const list = commentSection.querySelector('.comments-list');

    cancelBtn.addEventListener('click', () => commentSection.remove());
    submitBtn.addEventListener('click', () => {
      const text = textarea.value.trim();
      if (!text) return toast('Komentar tidak boleh kosong', 'error');

      const div = document.createElement('div');
      div.style.cssText = 'padding:8px;background:#f8f8f8;border-radius:8px;margin-bottom:6px;';
      div.innerHTML = `<strong style="color:var(--primary-color)">Anda</strong>: ${escapeHtml(text)}`;
      list.appendChild(div);

      textarea.value = '';
      const stat = postEl.querySelector('.stat-item i.bi-chat-dots').closest('.stat-item');
      const num = extractNumber(stat.textContent) || 0;
      setStatNumber(stat, num + 1);
      toast('Komentar ditambahkan', 'success');
    });
  }

  // ==========================================
  //  BUAT PERTANYAAN BARU
  // ==========================================
  function attachCreateQuestion() {
    createQuestionBtn?.addEventListener('click', () => showCreateModal());
  }

  function showCreateModal() {
    const modal = document.createElement('div');
    modal.className = 'aq-modal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;';
    modal.innerHTML = `
      <div style="background:#fff;border-radius:14px;padding:20px;width:90%;max-width:700px;">
        <h3 style="color:var(--primary-color);margin-bottom:12px;">Buat Pertanyaan Baru</h3>
        <textarea id="newQuestion" placeholder="Tulis pertanyaan Anda..." style="width:100%;min-height:120px;padding:12px;border:1px solid #ddd;border-radius:10px;"></textarea>
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:10px;">
          <button class="btn-cancel" style="background:#f0f0f0;border:1px solid #ddd;border-radius:10px;padding:8px 16px;">Batal</button>
          <button class="btn-submit" style="background:var(--primary-color);color:white;border:none;border-radius:10px;padding:8px 16px;">Posting</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    modal.querySelector('.btn-cancel').onclick = () => modal.remove();
    modal.querySelector('.btn-submit').onclick = () => {
      const val = modal.querySelector('#newQuestion').value.trim();
      if (!val) return toast('Tulis pertanyaan terlebih dahulu', 'error');
      const post = buildPostElement({
        avatar: '../../assets/img/aquara/profil.png',
        author: 'Anda (Pakar)',
        role: 'Pakar',
        question: val,
        comments: 0,
        likes: 0,
        views: 0
      });
      forumPostsWrapper.prepend(post);
      posts.unshift(post);
      attachGlobalDelegation();
      renderPage(1);
      updatePaginationActive();
      toast('Pertanyaan berhasil diposting', 'success');
      modal.remove();
    };
  }

  function buildPostElement(data) {
    const div = document.createElement('div');
    div.className = 'forum-post';
    div.innerHTML = `
      <div class="post-avatar"><img src="${data.avatar}" alt="${data.author}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;"></div>
      <div class="post-content">
        <div class="post-header"><h4 class="post-author">${data.author}</h4><span class="post-role">${data.role}</span></div>
        <p class="post-question">${data.question}</p>
        <div class="post-stats">
          <span class="stat-item"><i class="bi bi-chat-dots"></i> ${data.comments} Komentar</span>
          <span class="stat-item"><i class="bi bi-hand-thumbs-up"></i> ${data.likes} Suka</span>
          <span class="stat-item"><i class="bi bi-eye"></i> ${data.views} Lihat</span>
          <span class="post-time">Baru saja</span>
        </div>
      </div>
    `;
    return div;
  }

  // ==========================================
  //  FILTER
  // ==========================================
  function attachFilterButtons() {
    const filters = document.querySelectorAll('.filter-btn');
    filters.forEach(btn => {
      btn.addEventListener('click', () => {
        filters.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        toast(`Filter "${btn.textContent.trim()}" aktif`, 'success');
      });
    });
  }

  // ==========================================
  //  UTILITAS VISUAL DAN TEKS
  // ==========================================
  function extractNumber(text) {
    const match = text.match(/(\d+)/);
    return match ? parseInt(match[1]) : 0;
  }

  function setStatNumber(el, num) {
    const icon = el.querySelector('i');
    const label = el.textContent.match(/[A-Za-z]+$/);
    el.textContent = '';
    el.appendChild(icon);
    el.append(' ' + num + ' ' + (label ? label[0] : ''));
  }

  function escapeHtml(s) {
    return s.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }

  function pulse(el) {
    el.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.08)' }, { transform: 'scale(1)' }], { duration: 220 });
  }

  function flash(el) {
    el.style.transition = 'background 0.2s';
    el.style.background = 'rgba(63,134,134,0.1)';
    setTimeout(() => (el.style.background = ''), 200);
  }

  function toast(msg, type = 'success') {
    const div = document.createElement('div');
    div.textContent = msg;
    div.style.cssText = `
      position:fixed;top:20px;right:20px;z-index:99999;
      background:${type === 'success' ? 'var(--primary-color)' : '#e74c3c'};
      color:white;padding:10px 16px;border-radius:10px;
      box-shadow:0 4px 14px rgba(0,0,0,0.2);font-weight:600;
      opacity:0;transform:translateY(-10px);
      transition:all .3s ease;
    `;
    document.body.appendChild(div);
    setTimeout(() => (div.style.opacity = '1', div.style.transform = 'translateY(0)'), 10);
    setTimeout(() => (div.style.opacity = '0', div.style.transform = 'translateY(-10px)'), 2400);
    setTimeout(() => div.remove(), 2800);
  }
});
