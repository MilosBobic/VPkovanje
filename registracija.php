<?php
require_once 'klasaKorisnik.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnicko_ime = $_POST['korisnicko_ime'] ?? '';
    $lozinka = $_POST['lozinka'] ?? '';
    $ime = $_POST['ime'] ?? '';
    $prezime = $_POST['prezime'] ?? '';

    $korisnik = new Korisnik();

    if ($korisnik->registrujKorisnika($korisnicko_ime, $lozinka, $ime, $prezime)) {
        header('Location: prijava.php');
        exit;
    } else {
        $greska = "Korisnik sa tim korisničkim imenom već postoji.";
    }
}


?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija</title>
    <link rel="stylesheet" href="stilovi.css">
</head>
<body>
    <div style="width:50%; margin: 0 auto">
        <h1>Registracija</h1>
        <?php if (isset($greska)): ?>
            <div class="greska"><?php echo htmlspecialchars($greska); ?></

<?php endif; ?>
<form action="registracija.php" method="post">
    <label for="korisnicko_ime">Korisničko ime:</label>
    <input type="text" id="korisnicko_ime" name="korisnicko_ime" required>
    
    <label for="lozinka">Lozinka:</label>
    <input type="password" id="lozinka" name="lozinka" required>
    
    <label for="ime">Ime:</label>
    <input type="text" id="ime" name="ime" required>
    
    <label for="prezime">Prezime:</label>
    <input type="text" id="prezime" name="prezime" required>
    
    <button type="submit">Registruj se</button>
    <p>Imate nalog? <a href="prijava.php">Prijavite se</a></p>
</form>
</div>
</body>
</html>
