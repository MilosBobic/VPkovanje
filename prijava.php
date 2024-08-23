<?php
require_once 'funkcije.php';

$greska = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnickoIme = $_POST['korisnicko_ime'];
    $lozinka = $_POST['lozinka'];

    if (prijaviKorisnika($korisnickoIme, $lozinka)) {
        // Preusmeri korisnika na početnu stranicu
        header('Location: index.php');
        exit;
    } else {
        $greska = "Neispravno korisničko ime ili lozinka.";
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava</title>
    <link rel="stylesheet" href="stilovi.css">
</head>
<body>

<div style="width:50%; margin: 0 auto">
        <h1>Prijava</h1>
        <?php if ($greska): ?>
            <p class="greska"><?php echo htmlspecialchars($greska); ?></p>
        <?php endif; ?>
        <form method="post" action="prijava.php">
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" id="korisnicko_ime" name="korisnicko_ime" required>
            <br>
            <label for="lozinka">Lozinka:</label>
            <input type="password" id="lozinka" name="lozinka" required>
            <br>
            <button type="submit">Prijavi se</button>
        </form>
    </div>
</body>
</html>
