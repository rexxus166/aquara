// forum_pengunjung.js - Forum Versi Pengunjung (Read-Only)

// ========== DATA FORUM (Simulasi Database) ==========
let forumData = [
  { id: 1, author: 'Aeri Uchinaga', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Apakah ikan bisa makan kucing?', comments: 0, likes: 0, shares: 0, date: '25/09/25', page: 1 },
  { id: 2, author: 'Choihyunwook', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Ikan bisa mati ga?', comments: 2000, likes: 1678, shares: 87, date: '12/03/25', page: 1 },
  { id: 3, author: 'Ningning', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Ikan makan nya apa?', comments: 0, likes: 0, shares: 0, date: '25/09/25', page: 1 },
  { id: 4, author: 'ALfajar', role: 'Anggota', avatar: 'assets/img/aquara/profil.png', question: 'Bagaimana cara budidaya ikan lele yang baik?', comments: 45, likes: 234, shares: 12, date: '20/09/25', page: 2 },
  { id: 5, author: 'Winter Kim', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Apa perbedaan ikan nila dan ikan mujair?', comments: 23, likes: 156, shares: 8, date: '18/09/25', page: 2 },
  { id: 6, author: 'Giselle Aeri', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Tips memilih bibit ikan berkualitas?', comments: 67, likes: 345, shares: 23, date: '15/09/25', page: 2 },
  { id: 7, author: 'Taeyeon Kim', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Berapa lama masa panen ikan gurame?', comments: 34, likes: 189, shares: 15, date: '10/09/25', page: 3 },
  { id: 8, author: 'Seulgi Kang', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Ikan bawal bisa dibudidaya di kolam terpal?', comments: 56, likes: 267, shares: 19, date: '08/09/25', page: 3 },
  { id: 9, author: 'Joy Park', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Cara mengatasi ikan yang sakit jamur?', comments: 89, likes: 412, shares: 31, date: '05/09/25', page: 3 },
  { id: 10, author: 'Irene Bae', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Apakah sistem bioflok cocok untuk pemula?', comments: 78, likes: 356, shares: 28, date: '01/09/25', page: 4 },
  { id: 11, author: 'Wendy Son', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Rekomendasi pakan ikan yang ekonomis?', comments: 45, likes: 223, shares: 17, date: '28/08/25', page: 4 },
  { id: 12, author: 'Yeri Kim', role: 'Anggota', avatar: 'assets/img/aquara/profilforum1.png', question: 'Cara mengukur kualitas air kolam ikan?', comments: 34, likes: 178, shares: 11, date: '25/08/25', page: 4 }
];

let currentPage = 1;

document.addEventListener("DOMContentLoaded", function () {
  renderForumPosts(currentPage);
  initializePagination();
  initializeSearch();

  // Hapus tombol "Buat Pertanyaan" kalau ada
  const createQuestionBtn = document.querySelector(".cta-button");
  if (createQuestionBtn) createQuestionBtn.remove();
});

// ========== RENDER FORUM POSTS ==========
function renderForumPosts(page) {
  const forumPostsContainer = document.querySelector(".forum-posts");
  if (!forumPostsContainer) return;

  const postsToShow = forumData.filter(post => post.page === page);

  forumPostsContainer.innerHTML = ""; // clear
  const topDivider = document.createElement("hr");
  topDivider.className = "divider top-divider";
  forumPostsContainer.appendChild(topDivider);

  postsToShow.forEach((postData, index) => {
    const post = document.createElement("article");
    post.className = "forum-post";
    post.dataset.postId = postData.id;

    post.innerHTML = `
      <div class="post-main">
          <img src="${postData.avatar}" alt="${postData.author}" class="post-avatar">
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
              <span class="stat-comment disabled">
                  <img src="assets/img/aquara/komentarlogo.png" alt=""> ${postData.comments} Komentar
              </span>
              <span class="stat-like disabled">
                  <img src="assets/img/aquara/likelogo.png" alt=""> ${postData.likes} Suka
              </span>
              <span class="stat-share disabled">
                  <img src="assets/img/aquara/bagikanlogo.png" alt=""> ${postData.shares} Bagikan
              </span>
          </div>
          <span class="post-date">Dibuat pada ${postData.date}</span>
      </div>
    `;

    forumPostsContainer.appendChild(post);

    if (index < postsToShow.length - 1) {
      const divider = document.createElement("hr");
      divider.className = "divider";
      forumPostsContainer.appendChild(divider);
    }
  });

  // Tambahkan efek alert saat pengunjung mencoba klik
  document.querySelectorAll(".disabled").forEach(el => {
    el.style.opacity = "0.7";
    el.style.cursor = "not-allowed";
    el.addEventListener("click", () => {
      alert("Silakan login untuk memberikan komentar, menyukai, atau membagikan pertanyaan.");
    });
  });
}

// ========== PAGINATION ==========
function initializePagination() {
  const paginationLinks = document.querySelectorAll(".pagination a");
  paginationLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      const text = link.textContent.trim();

      if (text === "Next>") {
        if (currentPage < 4) currentPage++;
      } else if (!isNaN(text)) {
        currentPage = parseInt(text);
      }

      paginationLinks.forEach(l => l.classList.remove("active"));
      link.classList.add("active");

      renderForumPosts(currentPage);
      document.querySelector(".forum-posts").scrollIntoView({ behavior: "smooth" });
    });
  });
}

// ========== SEARCH ==========
function initializeSearch() {
  const searchForm = document.querySelector(".search-bar");
  if (!searchForm) return;

  const searchInput = searchForm.querySelector("input");
  searchForm.addEventListener("submit", e => {
    e.preventDefault();
    performSearch(searchInput.value);
  });

  searchInput.addEventListener("input", () => {
    if (searchInput.value.trim() === "") {
      renderForumPosts(currentPage);
      document.querySelector(".posts-filter-title").textContent = "Semua";
    }
  });
}

function performSearch(searchTerm) {
  const term = searchTerm.toLowerCase().trim();
  const forumPostsContainer = document.querySelector(".forum-posts");

  forumPostsContainer.innerHTML = "";

  if (term === "") {
    renderForumPosts(currentPage);
    return;
  }

  const results = forumData.filter(
    post => post.question.toLowerCase().includes(term) || post.author.toLowerCase().includes(term)
  );

  if (results.length === 0) {
    const noResults = document.createElement("p");
    noResults.textContent = "Tidak ada hasil yang ditemukan.";
    noResults.style.textAlign = "center";
    noResults.style.padding = "40px";
    noResults.style.color = "#999";
    forumPostsContainer.appendChild(noResults);
    document.querySelector(".posts-filter-title").textContent = `Hasil Pencarian (0)`;
  } else {
    results.forEach((postData, index) => {
      const postElement = document.createElement("article");
      postElement.className = "forum-post";
      postElement.innerHTML = `
          <div class="post-main">
              <img src="${postData.avatar}" alt="${postData.author}" class="post-avatar">
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
                  <span class="stat-comment disabled"><img src="assets/img/aquara/komentarlogo.png" alt=""> ${postData.comments} Komentar</span>
                  <span class="stat-like disabled"><img src="assets/img/aquara/likelogo.png" alt=""> ${postData.likes} Suka</span>
                  <span class="stat-share disabled"><img src="assets/img/aquara/bagikanlogo.png" alt=""> ${postData.shares} Bagikan</span>
              </div>
              <span class="post-date">Dibuat pada ${postData.date}</span>
          </div>
      `;
      forumPostsContainer.appendChild(postElement);
      if (index < results.length - 1) {
        const divider = document.createElement("hr");
        divider.className = "divider";
        forumPostsContainer.appendChild(divider);
      }
    });

    document.querySelector(".posts-filter-title").textContent = `Hasil Pencarian (${results.length})`;

    // Nonaktifkan klik
    document.querySelectorAll(".disabled").forEach(el => {
      el.style.opacity = "0.7";
      el.style.cursor = "not-allowed";
      el.addEventListener("click", () => {
        alert("Silakan login untuk memberikan komentar, menyukai, atau membagikan pertanyaan.");
      });
    });
  }
}
