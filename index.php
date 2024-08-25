<?php
session_start();
$ulogovan = isset($_SESSION['korisnik_id']);

require_once 'klasaProizvod.php';
require_once 'klasaPorudzbina.php';

$proizvod = new Proizvod();
$porudzbina = new Porudzbina();

$brojProizvoda = $proizvod->brojProizvoda();
$brojPorudzbina = $porudzbina->brojPorudzbina();
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
        include 'Meni.php'; // Uključi meni ako je korisnik prijavljen
    } else {
        include 'Meni-neulogovan.php'; // Uključi meni za neulogovane korisnike
    }
    ?>
    
    <div class="sadrzaj">
        <h1>Dobrodošli na početnu stranicu!</h1>
        <p>Broj proizvoda: <?php echo htmlspecialchars($brojProizvoda); ?></p>
        <p>Broj porudžbina: <?php echo htmlspecialchars($brojPorudzbina); ?></p>
    </div>
</body>
</html>
