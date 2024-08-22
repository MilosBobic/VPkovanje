<?php
session_start();
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Početna</title>
    <link rel="stylesheet" href="stilovi.css">
</head>
<body>
    <?php if (isset($_SESSION['ulogovan']) && $_SESSION['ulogovan'] === true): ?>
        <?php include 'meni.php'; ?>
        <?php else: ?>
        <?php include 'meni-neulogovan.php'; ?>
    <?php endif; ?>

    <div class="sadrzaj">
        <?php if (isset($_SESSION['ulogovan']) && $_SESSION['ulogovan'] === true): ?>
            <h1>Dobrodošli, <?php echo htmlspecialchars($_SESSION['ime']); ?>!</h1>
            <p>Možete sada koristiti aplikaciju.</p>
        <?php else: ?>
            <h1>Dobrodošli!</h1>
            <p>Morate se ulogovati da biste videli sadržaj aplikacije.</p>
        <?php endif; ?>
    </div>
</body>
</html>
