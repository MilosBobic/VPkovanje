<?php
// Uključivanje konfiguracije
require_once 'konfiguracija.php';

// Funkcija za uspostavljanje konekcije sa bazom podataka
function poveziBazu() {
    $konekcija = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Proveravanje konekcije
    if ($konekcija->connect_error) {
        die("Greška u konekciji: " . $konekcija->connect_error);
    }

    return $konekcija;
}

// Funkcija za zatvaranje konekcije sa bazom podataka
function zatvoriKonekciju($konekcija) {
    $konekcija->close();
}

function fetchData($sql, $parametri = []) {
    $conn = poveziBazu();
    $stmt = $conn->prepare($sql);
    $tipovi = str_repeat('s', count($parametri));
    $stmt->bind_param($tipovi, ...$parametri);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $podaci = $rezultat->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $podaci;
}

function executeQuery($sql, $parametri = []) {
    $conn = poveziBazu();
    $stmt = $conn->prepare($sql);
    $tipovi = str_repeat('s', count($parametri));
    $stmt->bind_param($tipovi, ...$parametri);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
