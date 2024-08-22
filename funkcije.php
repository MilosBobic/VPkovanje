<?php
session_start();

// Funkcija za prijavu korisnika
function prijaviKorisnika($korisnickoIme, $lozinka) {
    $sql = "SELECT * FROM Korisnici WHERE korisnicko_ime = ? AND lozinka = ?";
    $rezultat = fetchData($sql, [$korisnickoIme, md5($lozinka)]); // Pretpostavka da je lozinka enkriptovana md5

    if (count($rezultat) > 0) {
        $_SESSION['korisnik_id'] = $rezultat[0]['korisnik_id'];
        $_SESSION['ime'] = $rezultat[0]['ime'];
        $_SESSION['prezime'] = $rezultat[0]['prezime'];
        $_SESSION['uloga'] = $rezultat[0]['uloga'];
        $_SESSION['ulogovan'] = true; // Dodajte ovu liniju da označite da je korisnik prijavljen
        return true;
    }
    return false;
}

// Funkcija za registraciju korisnika
function registrujKorisnika($korisnickoIme, $lozinka, $ime, $prezime) {
    // Proveri da li korisnik već postoji
    $proveriKorisnika = fetchData("SELECT * FROM Korisnici WHERE korisnicko_ime = ?", [$korisnickoIme]);

    if (count($proveriKorisnika) > 0) {
        return false; // Korisnik već postoji
    }

    $sql = "INSERT INTO Korisnici (korisnicko_ime, lozinka, ime, prezime, uloga) VALUES (?, ?, ?, ?, 'user')";
    executeQuery($sql, [$korisnickoIme, md5($lozinka), $ime, $prezime]);
    return true;
}


// Funkcija za proveru da li je korisnik prijavljen
function daLiJeKorisnikUlogovan() {
    return isset($_SESSION['korisnik_id']) && isset($_SESSION['ulogovan']) && $_SESSION['ulogovan'] === true;
}

// Funkcija za dobijanje imena i prezimena korisnika
function dohvatiImeIPrezimeKorisnika() {
    if (daLiJeKorisnikUlogovan()) {
        return $_SESSION['ime'] . ' ' . $_SESSION['prezime'];
    }
    return null;
}
?>
