<?php
 require_once 'funkcije.php';

 $ulogovan = daLiJeKorisnikUlogovan();


// Dobavljanje proizvoda i dobavljača iz baze za popunjavanje padajućeg menija
$proizvodi = dohvatiProizvode(); // Pretpostavimo da funkcija vraća sve proizvode
$dobavljaci = dohvatiDobavljace(); // Pretpostavimo da funkcija vraća sve dobavljače

// Obrada forme za dodavanje nove porudžbine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_porudzbinu'])) {
    $proizvod_id = $_POST['proizvod_id'];
    $kolicina = $_POST['kolicina'];
    $dobavljac_id = $_POST['dobavljac_id'];
    $korisnik_id = $_SESSION['korisnik_id']; // Preuzmi korisnik_id iz sesije

    // Dodaj porudžbinu u bazu
    dodajPorudzbinu($proizvod_id, $kolicina, $dobavljac_id, $korisnik_id);
}

// Dohvati sve porudžbine za prikaz u tabeli
$porudzbine = dohvatiPorudzbine();

$naziviProizvoda = dohvatiNaziveProizvodaIzPorudzbina();
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
        <?php if (isset($_SESSION['ulogovan']) && $_SESSION['ulogovan'] === true): ?>
            <h1>Dobrodošli, <?php echo htmlspecialchars($_SESSION['ime']); ?>!</h1>
            <div style="width: 80%; margin: 0 auto;">
        <h1>Narudžbine</h1>
        
        <h2>Dodaj novu porudžbinu</h2>
        <form action="narudzbine.php" method="post">
            <label for="proizvod_id">Proizvod:</label>
            <select id="proizvod_id" name="proizvod_id" required>
                <?php foreach ($naziviProizvoda as $proizvod): ?>
                    <option value="<?php echo htmlspecialchars($proizvod['naziv_proizvoda']); ?>">
                        <?php echo htmlspecialchars($proizvod['naziv_proizvoda']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="kolicina">Količina:</label>
            <input type="number" id="kolicina" name="kolicina" required min="1">

            <label for="dobavljac_id">Dobavljač:</label>
            <select id="dobavljac_id" name="dobavljac_id" required>
                <?php foreach ($dobavljaci as $dobavljac): ?>
                    <option value="<?php echo htmlspecialchars($dobavljac['dobavljac_id']); ?>">
                        <?php echo htmlspecialchars($dobavljac['naziv_dobavljaca']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="dodaj_porudzbinu">Dodaj porudžbinu</button>
        </form>
        
        <h2>Pregled svih porudžbina</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proizvod</th>
                    <th>Korisnik</th>
                    <th>Datum porudžbine</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($porudzbine as $porudzbina): ?>
                <tr>
                    <td><?php echo htmlspecialchars($porudzbina['porudzbina_id']); ?></td>
                    <td><?php echo htmlspecialchars($naziviProizvoda['naziv_proizvoda']); ?></td>
                    <td><?php echo htmlspecialchars($porudzbina['korisnik_id']); ?></td>
                    <td><?php echo htmlspecialchars($porudzbina['datum_porudzbine']); ?></td>
                    <?php if ($_SESSION['uloga'] === 'admin') : ?>
                    <td>
                        <form action="narudzbine.php" method="post" style="display: inline;">
                        <input type="hidden" name="porudzbina_id" value="<?php echo htmlspecialchars($porudzbina['porudzbina_id']); ?>">
                        <button type="submit" name="obrisi" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu narudžbinu?');">Obriši</button>
                    </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        <?php else: ?>
            <h1>Dobrodošli!</h1>
            <p>Morate se ulogovati da biste videli sadržaj aplikacije.</p>
        <?php endif; ?>
    </div>
</body>
</html>
