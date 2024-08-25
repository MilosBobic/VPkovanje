<?php
require_once 'Baza.php';

class Korisnik {
    private $baza;

    public function __construct() {
        $this->baza = new Baza();
    }

    public function prijaviKorisnika($korisnicko_ime, $lozinka) {
        $sql = "SELECT * FROM Korisnici WHERE korisnicko_ime = ? AND lozinka = ?";
        $parametri = [$korisnicko_ime, $lozinka];
        $rezultat = $this->baza->fetchResults($sql, $parametri, 'ss');
        if (count($rezultat) === 1) {
            session_start();
            $_SESSION['korisnicko_ime'] = $korisnicko_ime;
            $_SESSION['korisnik_id'] = $rezultat[0]['korisnik_id'];
            $_SESSION['ime'] = $rezultat[0]['ime'];
            $_SESSION['prezime'] = $rezultat[0]['prezime'];
            return true;
        }
        return false;
    }

    // Primer funkcije za proveru da li korisnik postoji
public function korisnikPostoji($korisnickoIme) {
    $sql = "SELECT COUNT(*) FROM Korisnici WHERE korisnicko_ime = ?";
    $parametri = [$korisnickoIme];
    $rezultat = $this->baza->executeQuery($sql, $parametri, 's');
    
    if ($rezultat && $rezultat[0]['COUNT(*)'] > 0) {
        return true; // Korisnik već postoji
    }
    return false; // Korisnik ne postoji
}


    public function registrujKorisnika($korisnickoIme, $lozinka, $ime, $prezime) {
        if ($this->korisnikPostoji($korisnickoIme)) {
            return false; // Korisnik već postoji
        }
    
        $sql = "INSERT INTO Korisnici (korisnicko_ime, lozinka, ime, prezime) VALUES (?, ?, ?, ?)";
        $parametri = [$korisnickoIme, $lozinka, $ime, $prezime];
        $this->baza->executeQuery($sql, $parametri, 'ssss');
    
        // Zatvori konekciju nakon upita
        $this->baza->getConnection()->close();
    
        return true; // Registracija uspešna
    }
    
    public function proveriUlogu($korisnik_id, $uloga) {
        $sql = "SELECT uloga FROM Korisnici WHERE korisnik_id = ?";
        $parametri = [$korisnik_id];
        $rezultat = $this->baza->executeQuery($sql, $parametri, 'i');

        if ($rezultat && isset($rezultat[0]['uloga'])) {
            return $rezultat[0]['uloga'] === $uloga;
        }
        return false;
    }

}