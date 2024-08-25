<?php
require_once 'klasaProizvod.php';
require_once 'klasaKorisnik.php';
require_once 'klasaKonfiguracija.php';

$konfiguracija = Konfiguracija::getInstanca();

session_start();
$ulogovan = isset($_SESSION['korisnik_id']);
$proizvod = new Proizvod();
$korisnik = new Korisnik();
$isAdmin = $ulogovan && $korisnik->proveriUlogu($_SESSION['korisnik_id'], 'admin');

// Obrada zahteva za brisanje
if (isset($_POST['akcija']) && $_POST['akcija'] === 'obrisati' && $isAdmin) {
    $proizvod_id = $_POST['proizvod_id'];
    $proizvod->obrisatiProizvod($proizvod_id);
    header('Location: inventar.php');
    exit;
}

// Obrada zahteva za izmenu
if (isset($_POST['akcija']) && $_POST['akcija'] === 'izmeniti' && $isAdmin) {
    $proizvod_id = $_POST['proizvod_id'];
    $naziv_proizvoda = $_POST['naziv_proizvoda'] ?? null;
    $opis = $_POST['opis'] ?? null;
    $cena = $_POST['cena'] ?? null;
    $kolicina_na_stanju = $_POST['kolicina_na_stanju'] ?? null;

    if ($naziv_proizvoda && $opis && $cena !== null && $kolicina_na_stanju !== null) {
        $proizvod->izmeniProizvod($proizvod_id, $naziv_proizvoda, $opis, $cena, $kolicina_na_stanju);
        header('Location: inventar.php');
        exit;
    } else {
        echo "Svi podaci moraju biti popunjeni!";
    }
}

// Filter proizvoda po ceni
$minCena = $_GET['min_cena'] ?? null;
$maxCena = $_GET['max_cena'] ?? null;
if ($minCena !== null && $maxCena !== null) {
    $proizvodi = $proizvod->dohvatiProizvodePoCeni($minCena, $maxCena);
} else {
    $proizvodi = $proizvod->dohvatiProizvode();
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventar</title>
    <link rel="stylesheet" href="stilovi.css">
</head>
<body>
    <?php
    if ($ulogovan) {
        include 'Meni.php';
    } else {
        include 'Meni-neulogovan.php';
    }
    ?>

    <?php if ($ulogovan): ?>
    <div class="sadrzaj">
        <h1>Inventar</h1>
        <form method="get" action="inventar.php">
            <label for="min_cena">Minimalna Cena:</label>
            <input type="number" id="min_cena" name="min_cena" step="0.01" value="<?php echo htmlspecialchars($_GET['min_cena'] ?? ''); ?>">
            <label for="max_cena">Maksimalna Cena:</label>
            <input type="number" id="max_cena" name="max_cena" step="0.01" value="<?php echo htmlspecialchars($_GET['max_cena'] ?? ''); ?>">
            <button type="submit">Filtriraj</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Naziv Proizvoda</th>
                    <th>Opis</th>
                    <th>Cena</th>
                    <th>Količina na Stanju</th>
                    <?php if ($isAdmin): ?>
                        <th>Akcije</th>
                    <?php endif; ?>
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
                        <?php if ($isAdmin): ?>
                            <td>
                                <form method="post" action="inventar.php" style="display:inline;">
                                    <input type="hidden" name="proizvod_id" value="<?php echo htmlspecialchars($proizvod['proizvod_id']); ?>">
                                    <input type="hidden" name="akcija" value="obrisati">
                                    <button type="submit" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?');">Obriši</button>
                                </form>
                                <form method="get" action="inventar.php" style="display:inline;">
                                    <input type="hidden" name="proizvod_id" value="<?php echo htmlspecialchars($proizvod['proizvod_id']); ?>">
                                    <button type="submit">Izmeni</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($isAdmin && isset($_GET['proizvod_id'])): ?>
            <div>
                <h2>Izmena Proizvoda</h2>
                <?php
                $proizvod = new Proizvod();
                $proizvod_id = $_GET['proizvod_id'];
                $proizvodInfo = $proizvod->dohvatiProizvodPoId($proizvod_id);
                if ($proizvodInfo): ?>
                    <form method="post" action="inventar.php">
                        <input type="hidden" name="proizvod_id" value="<?php echo htmlspecialchars($proizvodInfo['proizvod_id']); ?>">
                        <input type="hidden" name="akcija" value="izmeniti">
                        <label for="naziv_proizvoda">Naziv Proizvoda:</label>
                        <input type="text" id="naziv_proizvoda" name="naziv_proizvoda" value="<?php echo htmlspecialchars($proizvodInfo['naziv_proizvoda']); ?>" required>
                        <br>
                        <label for="opis">Opis:</label>
                        <textarea id="opis" name="opis" required><?php echo htmlspecialchars($proizvodInfo['opis']); ?></textarea>
                        <br>
                        <label for="cena">Cena:</label>
                        <input type="number" id="cena" name="cena" step="0.01" value="<?php echo htmlspecialchars($proizvodInfo['cena']); ?>" required>
                        <br>
                        <label for="kolicina_na_stanju">Količina na Stanju:</label>
                        <input type="number" id="kolicina_na_stanju" name="kolicina_na_stanju" value="<?php echo htmlspecialchars($proizvodInfo['kolicina_na_stanju']); ?>" required>
                        <br>
                        <button type="submit">Sačuvaj Promene</button>
                    </form>
                <?php else: ?>
                    <p>Proizvod nije pronađen.</p>
                <?php endif; ?>
            </div>
        <?php elseif ($isAdmin): ?>
            <p>Odaberite proizvod za izmenu.</p>
        <?php endif; ?>
    </div>
    <?php else : ?>
    <div class="sadrzaj">
        <h1>Dobrodošli na inventar stranicu!</h1>
        <p>Morate se ulogovati da bi videli sadržaj</p>
    </div>
    <?php endif; ?>
</body>
</html>
