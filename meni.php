<?php
session_start();

$ulogovan = isset($_SESSION['ime']) && isset($_SESSION['prezime']);
?>

<link rel="stylesheet" href="stilovi.css">
<nav>
    <ul>
        <li class="prijava-link">
            <a href="profil.php"><?php echo $_SESSION['ime'] . ' ' . $_SESSION['prezime']; ?></a>
            </li>
        <li><a href="index.php" class="active">Početna</a></li>
        <li><a href="inventar.php">Inventar</a></li>
        <li><a href="stampa.php">Štampa</a></li>
        <li><a href="narudzbine.php">Narudžbine</a></li>
    </ul>
</nav>
