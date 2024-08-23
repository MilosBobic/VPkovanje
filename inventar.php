<?php
 require_once 'funkcije.php';

 $ulogovan = daLiJeKorisnikUlogovan();
 $proizvodi = dohvatiProizvode();
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
    <?php if (isset($_SESSION['ulogovan']) && $_SESSION['ulogovan'] === true): ?>
        <?php include 'meni.php'; ?>
    <?php endif; ?>

    <div class="sadrzaj">
        <?php if (isset($_SESSION['ulogovan']) && $_SESSION['ulogovan'] === true): ?>
            <h1>Dobrodošli, <?php echo htmlspecialchars($_SESSION['ime']); ?>!</h1>
            <div style="width:80%; margin: 0 auto">
        <h1>Inventar</h1>

        <?php if (!empty($proizvodi)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naziv proizvoda</th>
                        <th>Opis</th>
                        <th>Cena</th>
                        <th>Količina na stanju</th>
                        <th>Kategorija ID</th>
                        <th>Dobavljač ID</th>
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
                            <td><?php echo htmlspecialchars($proizvod['kategorija_id']); ?></td>
                            <td><?php echo htmlspecialchars($proizvod['dobavljac_id']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nemate proizvoda u inventaru.</p>
        <?php endif; ?>
    </div>
        <?php else: ?>
            <h1>Dobrodošli!</h1>
            <p>Morate se ulogovati da biste videli sadržaj aplikacije.</p>
        <?php endif; ?>
    </div>
</body>
</html>
