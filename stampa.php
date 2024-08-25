<?php
session_start();
$ulogovan = isset($_SESSION['korisnik_id']);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Štampa</title>
    <link rel="stylesheet" href="stilovi.css">
</head>
<body>
    <?php
    if ($ulogovan) {
        include 'Meni.php'; // Uključi meni ako je korisnik prijavljen
    } else {
        include 'Meni-neulogovan.php'; // Uključi meni za neulogovane korisnike
    }
    ?>
    
    <div class="sadrzaj">
        <h1>Štampa</h1>
        <p>Ovdje možete pregledati i štampati podatke.</p>
    </div>
</body>
</html>
