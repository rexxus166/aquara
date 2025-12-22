<?php
// FILE: htdocs/aquara/api/get_events.php
require_once 'koneksi.php'; // Atau 'koneksi_mobile.php' (sesuaikan yg kamu pakai)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Ambil data urut dari tanggal mulai paling dekat
$sql = "SELECT * FROM events ORDER BY tanggal_mulai ASC";
$result = $koneksi->query($sql);

$events = array();
// --- PENTING: GANTI IP SESUAI LAPTOP KAMU ---
$base_url = "https://aquara.miomidev.com";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // 1. Cek Gambar
        if (!empty($row['gambar'])) {
            // Asumsi gambar ada di folder 'uploads/event/' (sesuaikan jika beda)
            // Jika di database cuma nama file, kita gabung dengan base URL
            $row['gambar_url'] = $base_url . "/uploads/events/" . $row['gambar'];
        } else {
            $row['gambar_url'] = "https://placehold.co/600x400/013746/white?text=Event+Aquara";
        }

        // 2. Format Tanggal untuk Tampilan (Biar mudah di Flutter)
        // Ambil Tanggal (contoh: 06)
        $row['tgl_display'] = date('d', strtotime($row['tanggal_mulai']));
        // Ambil Bulan (contoh: DES)
        $row['bln_display'] = strtoupper(date('M', strtotime($row['tanggal_mulai'])));

        // 3. Link Pendaftaran (Pastikan tidak null)
        if (empty($row['link_pendaftaran'])) {
            $row['link_pendaftaran'] = "";
        }

        $events[] = $row;
    }
}

echo json_encode($events);
