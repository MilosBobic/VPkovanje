<?php
require_once 'funkcije.php'; // Uključi funkcije.php da bi mogao da koristiš funkcije

// Proveri da li je korisnik prijavljen
$ulogovan = daLiJeKorisnikUlogovan();
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
    <?php
    if ($ulogovan) {
        include 'meni.php'; // Uključi meni samo ako je korisnik prijavljen
    }
    else {
        include 'meni-neulogovan.php'; // Uključi meni-neulogovan.php ako nije prijavljen
    }
    ?>

    <div class="sadrzaj">
        <?php if ($ulogovan): ?>
            <h1>Dobrodošli, <?php echo htmlspecialchars(dohvatiImeIPrezimeKorisnika()); ?>!</h1>
            <p>Možete sada koristiti aplikaciju.</p>
        <?php else: ?>
            <h1>Dobrodošli!</h1>
            <p>Morate se ulogovati da biste videli sadržaj aplikacije.</p>
        <?php endif; ?>
    </div>
</body>
</html>
