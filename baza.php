<?php
// Uključivanje konfiguracije
require_once 'config.php';

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