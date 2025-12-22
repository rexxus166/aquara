// user-profile-dropdown.js

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== USER PROFILE DROPDOWN ==========
    const userProfile = document.querySelector('.user-profile');
    
    if (!userProfile) {
        console.warn('Element .user-profile tidak ditemukan');
        return;
    }

    // Tambahkan style untuk dropdown
    const style = document.createElement('style');
    style.textContent = `
        .user-profile {
            position: relative;
            cursor: pointer;
        }

        .user-dropdown-menu {
            position: absolute;
            top: 80px;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .user-dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 14px 20px;
            color: #435454;
            text-decoration: none;
            font-size: 15px;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-item:first-child {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .dropdown-item:last-child {
            border-bottom: none;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .dropdown-item:hover {
            background-color: #f5f5f5;
            padding-left: 24px;
        }

        .dropdown-item.logout {
            color: #e74c3c;
            font-weight: 500;
        }

        .dropdown-item.logout:hover {
            background-color: #ffe8e8;
        }
    `;
    document.head.appendChild(style);

    // ========== CREATE DROPDOWN MENU ==========
    function createDropdownMenu() {
        const dropdownMenu = document.createElement('div');
        dropdownMenu.className = 'user-dropdown-menu';
        
        dropdownMenu.innerHTML = `
            <a href="../../views/pakar/profile_pakar.php" class="dropdown-item">Profil Saya</a>
            <a href="index_pakar.php?page=logout" class="dropdown-item logout">Logout</a>
        `;
        
        userProfile.appendChild(dropdownMenu);
        return dropdownMenu;
    }

    let dropdownMenu = null;

    // ========== TOGGLE DROPDOWN ==========
    userProfile.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (!dropdownMenu) {
            dropdownMenu = createDropdownMenu();
        }
        
        // Toggle active class
        dropdownMenu.classList.toggle('active');
    });

    // ========== CLOSE DROPDOWN WHEN CLICKING OUTSIDE ==========
    document.addEventListener('click', function(e) {
        if (!userProfile.contains(e.target)) {
            if (dropdownMenu) {
                dropdownMenu.classList.remove('active');
            }
        }
    });

    // ========== CLOSE DROPDOWN WHEN CLICKING MENU ITEM ==========
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('dropdown-item')) {
            if (dropdownMenu) {
                dropdownMenu.classList.remove('active');
            }
        }
    });

    // ========== HANDLE LOGOUT ==========
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('logout')) {
            e.preventDefault();
            
            // Optional: Show confirmation
            if (confirm('Apakah Anda yakin ingin logout?')) {
                // Redirect ke logout
                window.location.href = 'index_pakar.php?page=logout';
            }
        }
    });
});