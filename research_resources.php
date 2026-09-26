<?php
session_start();
$view = ($_GET['view'] ?? '') === 'agenda' ? 'agenda' : 'sdg';
$agenda = $view === 'agenda';
$title = $agenda ? 'Research Agenda' : 'Sustainable Development Goals';
$description = $agenda ? 'CEU Malolos research priorities and their alignment with the SDGs.' : 'A reference for aligning research with the 17 global goals.';
$image = $agenda ? 'Research_Matrix.png' : 'SDG.jpg';
$alt = $agenda ? 'Centro Escolar University Malolos Research Agenda 2023–2028: six research clusters and their corresponding Sustainable Development Goals.' : 'The 17 United Nations Sustainable Development Goals, from No Poverty to Partnerships for the Goals.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> | PRISM</title>
    <link rel="icon" type="image/png" href="assets/images/prismicon.png">
    <script>try{if(localStorage.getItem('prismTheme')==='dark')document.documentElement.classList.add('dark-theme')}catch(_){}</script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-sidebar.css">
    <link rel="stylesheet" href="assets/css/research-resources.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<div class="container">
    <?php require __DIR__ . '/admin_navigation.php'; ?>
    <main class="main-content resource-page">
        <header class="topbar">
            <div><span class="resource-eyebrow">Research Resources</span><h1><?= $title ?></h1><p><?= $description ?></p></div>
        </header>
        <nav class="resource-tabs" aria-label="Research references">
            <a href="research_resources.php?view=sdg" <?= !$agenda ? 'aria-current="page"' : '' ?>>Sustainable Development Goals</a>
            <a href="research_resources.php?view=agenda" <?= $agenda ? 'aria-current="page"' : '' ?>>Research Agenda</a>
        </nav>
        <figure class="resource-figure <?= $agenda ? 'resource-agenda' : 'resource-sdg' ?>">
            <div class="resource-image-surface"><img src="assets/images/<?= $image ?>" alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') ?>" width="<?= $agenda ? 612 : 2048 ?>" height="<?= $agenda ? 786 : 1448 ?>"></div>
            <figcaption><i class="fa-regular fa-file-image" aria-hidden="true"></i><span><?= $agenda ? 'CEU Malolos · Research Agenda 2023–2028' : 'United Nations · 17 Sustainable Development Goals' ?></span></figcaption>
        </figure>
    </main>
</div>
<script src="assets/js/dashboard-sidebar.js"></script>
</body>
</html>
