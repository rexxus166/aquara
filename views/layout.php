<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'AQUARA - Platform Edukasi Perikanan') ?></title>
    
    <link rel="icon" href="assets/img/aquara/logo.png" type="image/png">

    <link rel="stylesheet" href="assets/css/layout.css?v=<?= time() ?>">

    <?= $this->section('styles') ?>
</head>
<body>

    <?php include __DIR__ . '/../includes/pengunjung/header_pengunjung_2.php'; ?>

    <main>
        <?= $this->section('body') ?>
    </main>

    <?php include __DIR__ . '/../includes/pengunjung/footer_pengunjung.php'; ?>

    <script src="assets/js/main.js"></script>
    <?= $this->section('scripts') ?>
</body>
</html>