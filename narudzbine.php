<?php
require_once 'funkcije.php';

$ulogovan = daLiJeKorisnikUlogovan();
$proizvodi = dohvatiProizvode(); // Dohvati proizvode iz baze kao niz

// Obrada porudžbine proizvoda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['posalji'])) {
    $proizvod_id = $_POST['proizvod_id'];
    $kolicina = $_POST['kolicina'];
    $korisnik_id = $_SESSION['korisnik_id']; // ID trenutnog korisnika

    // Koristimo funkciju koja kreira novu vezu sa bazom
    $konekcija = poveziBazu();

    // Dodavanje nove porudžbine
    $porudzbina_id = dodajPorudzbinu($konekcija, $korisnik_id, $proizvod_id, $kolicina);
    if ($porudzbina_id) {
        // Dodavanje stavke porudžbine
        if (dodajStavkuPorudzbine($konekcija, $porudzbina_id, $proizvod_id, $kolicina)) {
            // Ažuriranje količine proizvoda
            if (azurirajKolicinuProizvoda($konekcija, $proizvod_id, $kolicina)) {
                echo "Uspešno ste poručili proizvod!";
            } else {
                echo "Došlo je do greške prilikom ažuriranja količine proizvoda.";
            }
        } else {
            echo "Došlo je do greške prilikom dodavanja stavke porudžbine.";
        }
    } else {
        echo "Došlo je do greške prilikom kreiranja porudžbine.";
    }

    // Zatvori konekciju nakon upita
    zatvoriKonekciju($konekcija);
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porudžbine</title>
    <link rel="stylesheet" href="stilovi.css">
</head>
<body>
    <?php
    if ($ulogovan) {
        include 'meni.php'; // Uključi meni ako je korisnik prijavljen
    } else {
        include 'meni-neulogovan.php'; // Uključi meni za neulogovane korisnike
    }
    ?>
    
    <div class="sadrzaj">
        <?php if ($ulogovan): ?>
            <h1>Porudžbine</h1>
            <form method="POST">
                <label for="proizvod_id">Izaberite proizvod:</label>
                <select name="proizvod_id" id="proizvod_id" required>
                    <?php foreach ($proizvodi as $proizvod): ?>
                        <option value="<?php echo htmlspecialchars($proizvod['proizvod_id']); ?>">
                            <?php echo htmlspecialchars($proizvod['naziv_proizvoda']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="kolicina">Količina:</label>
                <input type="number" name="kolicina" id="kolicina" min="1" required>

                <button type="submit" name="posalji">Pošalji Porudžbinu</button>
            </form>
        <?php else: ?>
            <h1>Dobrodošli!</h1>
            <p>Morate se ulogovati da biste videli sadržaj aplikacije.</p>
        <?php endif; ?>
    </div>
</body>
</html>
