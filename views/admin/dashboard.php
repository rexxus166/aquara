<?php
// --- 1. HITUNG TOTAL KARTU ATAS (SAMA SEPERTI SEBELUMNYA) ---
$total_pengguna = $conn->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetch_row()[0];
$total_pakar = $conn->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetch_row()[0];
$total_artikel = $conn->query("SELECT COUNT(*) FROM articles")->fetch_row()[0];
$total_event = $conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0];

// --- 2. SIAPKAN DATA GRAFIK REAL DARI DATABASE ---
// Kita akan menghitung jumlah artikel & event per bulan di tahun ini

$tahun_ini = date('Y');
$data_artikel_per_bulan = array_fill(1, 12, 0); // Siapkan array kosong 12 bulan (Jan-Des)
$data_event_per_bulan = array_fill(1, 12, 0);

// Query hitung artikel per bulan
$sql_art = "SELECT MONTH(created_at) as bulan, COUNT(*) as total 
            FROM articles WHERE YEAR(created_at) = '$tahun_ini' 
            GROUP BY MONTH(created_at)";
$res_art = $conn->query($sql_art);
while ($row = $res_art->fetch_assoc()) {
    $data_artikel_per_bulan[$row['bulan']] = $row['total'];
}

// Query hitung event per bulan
$sql_evt = "SELECT MONTH(created_at) as bulan, COUNT(*) as total 
            FROM events WHERE YEAR(created_at) = '$tahun_ini' 
            GROUP BY MONTH(created_at)";
$res_evt = $conn->query($sql_evt);
while ($row = $res_evt->fetch_assoc()) {
    $data_event_per_bulan[$row['bulan']] = $row['total'];
}

// Konversi array PHP ke format JSON biar bisa dibaca JavaScript
$json_artikel = json_encode(array_values($data_artikel_per_bulan));
$json_event = json_encode(array_values($data_event_per_bulan));
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary h-100 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><h6 class="card-title mb-0">Pengguna</h6><h2 class="mb-0 fw-bold"><?= $total_pengguna; ?></h2></div>
                <i class="fas fa-users fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success h-100 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><h6 class="card-title mb-0">Pakar</h6><h2 class="mb-0 fw-bold"><?= $total_pakar; ?></h2></div>
                <i class="fas fa-user-tie fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info h-100 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><h6 class="card-title mb-0">Artikel</h6><h2 class="mb-0 fw-bold"><?= $total_artikel; ?></h2></div>
                <i class="far fa-newspaper fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning h-100 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div><h6 class="card-title mb-0 text-dark">Event</h6><h2 class="mb-0 fw-bold text-dark"><?= $total_event; ?></h2></div>
                <i class="far fa-calendar-alt fa-3x opacity-50 text-dark"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Publikasi Bulanan (Tahun <?= $tahun_ini; ?>)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 350px;">
                    <canvas id="realDataChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('realDataChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar', // Ganti jadi 'bar' (batang) biar lebih cocok untuk data jumlah kecil
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Artikel Baru',
                    // Ambil data asli dari variabel PHP tadi
                    data: <?= $json_artikel; ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)', // Biru
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Event Baru',
                    // Ambil data asli dari variabel PHP tadi
                    data: <?= $json_event; ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.6)', // Kuning
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Paksa sumbu Y pakai bilangan bulat (karena jumlah artikel gak mungkin desimal)
                        }
                    }
                }
            }
        });
    }
});
</script>