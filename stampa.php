<?php
require_once 'klasaStampa.php';
require_once 'klasaKorisnik.php';

session_start();

// Proveravamo da li je korisnik ulogovan i da li je admin
$ulogovan = isset($_SESSION['korisnik_id']);
$korisnik = new Korisnik(); // Dodajemo instancu klase Korisnik za proveru uloge
$isAdmin = $ulogovan && $korisnik->proveriUlogu($_SESSION['korisnik_id'], 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kreiramo instancu klase Stampa
    $stampa = new Stampa();

    // Proveravamo akciju da bismo odlučili koju funkciju da pozovemo
    if ($isAdmin) {
        // Admin može preuzeti izveštaj koristeći specijalnu funkciju
        $stampa->generisiTxtFajlAdmin();
    } else {
        // Običan korisnik preuzima izveštaj
        $stampa->generisiTxtFajl();
    }

    exit; // Prekidamo izvršavanje skripte nakon preuzimanja fajla
}

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
        <?php if ($ulogovan): ?>
            <h1>Štampa</h1>
            <h2>Preuzmi mesečni izveštaj</h2>
            <form method="post" action="stampa.php">
                <button type="submit">Preuzmi izveštaj</button>
            </form>
        <?php else: ?>

            <h1>Štampa</h1>
            <h2>Morate se prijaviti da biste preuzeli mesečni izveštaj.</h2>
        <?php endif; ?>
    </div>
</body>

</html>