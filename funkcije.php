<?php
// Uključivanje datoteke za konekciju sa bazom
require_once 'baza.php';

// Funkcija za sanitizaciju korisničkog unosa
function sanitizujUnos($podaci) {
    return htmlspecialchars(stripslashes(trim($podaci)));
}

// Funkcija za proveru da li je korisnik prijavljen
function daLiJePrijavljen() {
    return isset($_SESSION['korisnik_id']);
}

// Funkcija za dobijanje korisnika po ID
function KorisnikPoId($korisnikId) {
    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare("SELECT * FROM Korisnici WHERE korisnik_id = ?");
    $stmt->bind_param("i", $korisnikId);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $korisnik = $rezultat->fetch_assoc();
    zatvoriKonekciju($konekcija);
    return $korisnik;
}

// Funkcija za proveru kredencijala korisnika
function proveriPrijavu($korisnickoIme, $lozinka) {
    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare("SELECT * FROM Korisnici WHERE korisnicko_ime = ? AND lozinka = ?");
    $stmt->bind_param("ss", $korisnickoIme, $lozinka);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $korisnik = $rezultat->fetch_assoc();
    zatvoriKonekciju($konekcija);
    return $korisnik;
}

// Funkcija za dobijanje svih proizvoda
function prikaziSveProizvode() {
    $konekcija = poveziBazu();
    $sql = "SELECT * FROM Proizvodi";
    $rezultat = $konekcija->query($sql);
    $proizvodi = [];
    if ($rezultat->num_rows > 0) {
        while ($red = $rezultat->fetch_assoc()) {
            $proizvodi[] = $red;
        }
    }
    zatvoriKonekciju($konekcija);
    return $proizvodi;
}

// Funkcija za kreiranje porudžbine
function kreirajPorudzbinu($korisnikId, $stavke) {
    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare("INSERT INTO Porudzbine (korisnik_id, datum_porudzbine) VALUES (?, NOW())");
    $stmt->bind_param("i", $korisnikId);
    $stmt->execute();
    $porudzbinaId = $stmt->insert_id;

    foreach ($stavke as $stavka) {
        $stmt = $konekcija->prepare("INSERT INTO Stavke_Porudzbine (porudzbina_id, proizvod_id, kolicina) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $porudzbinaId, $stavka['proizvod_id'], $stavka['kolicina']);
        $stmt->execute();
    }

    zatvoriKonekciju($konekcija);
    return $porudzbinaId;
}

// Funkcija za brisanje porudžbine (samo za admina)
function obrisiPorudzbinu($porudzbinaId) {
    if (!daLiJePrijavljen() || $_SESSION['uloga'] !== 'admin') {
        return false;
    }

    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare("DELETE FROM Porudzbine WHERE porudzbina_id = ?");
    $stmt->bind_param("i", $porudzbinaId);
    $stmt->execute();

    $stmt = $konekcija->prepare("DELETE FROM Stavke_Porudzbine WHERE porudzbina_id = ?");
    $stmt->bind_param("i", $porudzbinaId);
    $stmt->execute();

    zatvoriKonekciju($konekcija);
    return true;
}

// Funkcija za dodavanje transakcije u inventar
function dodajTransakcijuInventara($proizvodId, $tipTransakcije, $kolicina) {
    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare("INSERT INTO Transakcije_Inventara (proizvod_id, tip_transakcije, kolicina) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $proizvodId, $tipTransakcije, $kolicina);
    $stmt->execute();
    zatvoriKonekciju($konekcija);
}

// Funkcija za dobijanje svih transakcija u inventaru
function prikaziTransakcijeInventara() {
    $konekcija = poveziBazu();
    $sql = "SELECT * FROM Transakcije_Inventara";
    $rezultat = $konekcija->query($sql);
    $transakcije = [];
    if ($rezultat->num_rows > 0) {
        while ($red = $rezultat->fetch_assoc()) {
            $transakcije[] = $red;
        }
    }
    zatvoriKonekciju($konekcija);
    return $transakcije;
}
