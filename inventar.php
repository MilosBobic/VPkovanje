<?php
require_once 'funkcije.php';

$ulogovan = daLiJeKorisnikUlogovan();
$proizvodi = dohvatiProizvode(); // Dohvati proizvode iz baze kao niz
$admin = daLiJeKorisnikAdmin(); // Proveri da li je korisnik admin

// Obrada forme za dodavanje novog proizvoda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dodaj_proizvod'])) {
    $naziv_proizvoda = $_POST['naziv_proizvoda'];
    $opis = $_POST['opis'];
    $cena = $_POST['cena'];
    $kolicina_na_stanju = $_POST['kolicina_na_stanju'];

    // Koristimo funkciju koja kreira novu vezu sa bazom
    $konekcija = poveziBazu();

    if (dodajProizvod($konekcija, $naziv_proizvoda, $opis, $cena, $kolicina_na_stanju)) {
        echo "Uspešno ste dodali novi proizvod!";
    } else {
        echo "Došlo je do greške prilikom dodavanja novog proizvoda.";
    }

    // Zatvori konekciju nakon upita
    zatvoriKonekciju($konekcija);
}

// Obrada forme za brisanje proizvoda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['obrisi_proizvod'])) {
    $proizvod_id = $_POST['proizvod_id'];

    if ($admin) { // Proveri da li je korisnik admin
        $konekcija = poveziBazu();
        if (obrisiProizvod($konekcija, $proizvod_id)) {
            echo "Proizvod je uspešno obrisan!";
        } else {
            echo "Došlo je do greške prilikom brisanja proizvoda.";
        }
        zatvoriKonekciju($konekcija);
    } else {
        echo "Nemate ovlašćenja za brisanje proizvoda.";
    }
}
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
    } else {
        include 'meni-neulogovan.php'; // Uključi meni-neulogovan.php ako nije prijavljen
    }
    ?>

    <div class="sadrzaj">
        <?php if ($ulogovan): ?>
            <h1>Dobrodošli, <?php echo htmlspecialchars(dohvatiImeIPrezimeKorisnika()); ?>!</h1>
            <p>Možete sada koristiti aplikaciju.</p>
            <div>
                <h1>Inventar</h1>
                <div>
                    <?php if (!empty($proizvodi)): ?>
                        <table border="1">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Naziv proizvoda</th>
                                    <th>Opis</th>
                                    <th>Cena</th>
                                    <th>Količina na stanju</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proizvodi as $proizvod): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($proizvod['proizvod_id']); ?></td>
                                        <td><?php echo htmlspecialchars($proizvod['naziv_proizvoda']); ?></td>
                                        <td><?php echo htmlspecialchars($proizvod['opis']); ?></td>
                                        <td><?php echo htmlspecialchars($proizvod['cena']); ?></td>
                                        <td><?php echo htmlspecialchars($proizvod['kolicina_na_stanju']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nemate proizvoda u inventaru.</p>
                    <?php endif; ?>
                    <h2>Dodaj novi proizvod</h2>
                    <form action="inventar.php" method="post">
                        <label for="naziv_proizvoda">Naziv proizvoda:</label>
                        <input type="text" name="naziv_proizvoda" id="naziv_proizvoda" required><br><br>

                        <label for="opis">Opis:</label>
                        <textarea name="opis" id="opis" required></textarea><br><br>

                        <label for="cena">Cena:</label>
                        <input type="number" name="cena" id="cena" step="0.01" required><br><br>

                        <label for="kolicina_na_stanju">Količina na stanju:</label>
                        <input type="number" name="kolicina_na_stanju" id="kolicina_na_stanju" min="0" required><br><br>

                        <input type="submit" name="dodaj_proizvod" value="Dodaj proizvod">
                    </form>

                    <?php if ($admin): ?>
                        <h2>Obriši proizvod</h2>
                        <form action="inventar.php" method="post">
                            <label for="proizvod_id">ID proizvoda:</label>
                            <input type="number" name="proizvod_id" id="proizvod_id" required><br><br>
                            <input type="submit" name="obrisi_proizvod" value="Obriši proizvod">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <h1>Dobrodošli!</h1>
            <p>Morate se ulogovati da biste videli sadržaj aplikacije.</p>
        <?php endif; ?>
    </div>
</body>

</html>
