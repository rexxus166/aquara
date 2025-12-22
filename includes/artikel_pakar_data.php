<?php
// Pastikan koneksi $conn tersedia.
// File ini biasanya di-include setelah config.php, jadi $conn seharusnya ada.
// Jika tidak, kita bisa menambah 'global $conn;' di dalam fungsi.

function getAllArtikelPakar() {
    global $conn; // Gunakan variabel koneksi global

    $articles = [];
    // Query untuk mengambil semua artikel, diurutkan dari yang terbaru
    // Kita JOIN dengan tabel 'users' untuk mendapatkan nama penulis
    // dan tabel 'categories' untuk nama kategori.
    $sql = "SELECT 
                a.id, 
                a.judul, 
                LEFT(a.konten, 150) AS excerpt, -- Ambil 150 karakter pertama untuk excerpt
                a.gambar, 
                a.created_at AS tanggal,
                u.nama AS penulis,
                c.nama AS kategori
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            ORDER BY a.created_at DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format tanggal agar lebih rapi (opsional)
            $row['tanggal'] = date('F Y', strtotime($row['tanggal']));
            // Bersihkan excerpt dari tag HTML jika ada
            $row['excerpt'] = strip_tags($row['excerpt']) . '...';
            
            // Pastikan path gambar benar. Jika kosong, pakai default.
            if (empty($row['gambar'])) {
                $row['gambar'] = 'assets/img/aquara/artikel.png';
            } else {
                // Asumsi gambar disimpan di 'uploads/articles/'
                $row['gambar'] = 'uploads/articles/' . $row['gambar'];
            }
            
            $articles[$row['id']] = $row;
        }
    }
    return $articles;
}

function getArtikelPakarById($id) {
    global $conn;

    // Query untuk mengambil satu artikel lengkap berdasarkan ID
    $sql = "SELECT 
                a.id, a.judul, a.konten, a.gambar, a.created_at AS tanggal,
                u.nama AS penulis,
                c.nama AS kategori
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $artikel = $result->fetch_assoc();
        
        // Format tanggal
        $artikel['tanggal'] = date('F Y', strtotime($artikel['tanggal']));
        
        // Format gambar
        if (empty($artikel['gambar'])) {
            $artikel['gambar'] = 'assets/img/aquara/artikel.png';
        } else {
            $artikel['gambar'] = 'uploads/articles/' . $artikel['gambar'];
        }
        
        // Tambahkan data 'views' (simulasi, karena belum ada kolom views di DB)
        // Nanti Anda bisa menambahkan kolom 'views' di tabel 'articles'
        $artikel['views'] = rand(1000, 5000); 

        return $artikel;
    }
    return null;
}

function getArtikelPakarTerkait($currentId, $limit = 3) {
    global $conn;

    $related = [];
    // Ambil artikel lain selain artikel saat ini, acak 3 biji
    $sql = "SELECT 
                a.id, a.judul, a.gambar, a.created_at AS tanggal,
                c.nama AS kategori
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.id != ?
            ORDER BY RAND()
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $currentId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
             $row['tanggal'] = date('F Y', strtotime($row['tanggal']));
             if (empty($row['gambar'])) {
                $row['gambar'] = 'assets/img/aquara/artikel.png';
            } else {
                $row['gambar'] = 'uploads/articles/' . $row['gambar'];
            }
            $related[] = $row;
        }
    }
    return $related;
}
?>