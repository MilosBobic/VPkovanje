<?php
$ulogovan = isset($_SESSION['ime']) && isset($_SESSION['prezime']);
$trenutnaStranica = $_SERVER['REQUEST_URI'];
?>

<link rel="stylesheet" href="stilovi.css">
<nav>
    <ul>
        <li class="prijava-link">
            <a><?php echo $_SESSION['ime'] . ' ' . $_SESSION['prezime']; ?></a>
            <a href="odjava.php" onclick="return confirm('Da li ste sigurni da želite da se odjavite?');">Odjavi se</a>
        </li>
        <li><a href="index.php" class="<?php echo strpos($trenutnaStranica, 'index.php') !== false ? 'active' : ''; ?>">Početna</a></li>
        <li><a href="inventar.php" class="<?php echo strpos($trenutnaStranica, 'inventar.php') !== false ? 'active' : ''; ?>">Inventar</a></li>
        <li><a href="stampa.php" class="<?php echo strpos($trenutnaStranica, 'stampa.php') !== false ? 'active' : ''; ?>">Štampa</a></li>
        <li><a href="narudzbine.php" class="<?php echo strpos($trenutnaStranica, 'narudzbine.php') !== false ? 'active' : ''; ?>">Narudžbine</a></li>
    </ul>
</nav>
