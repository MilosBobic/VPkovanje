<?php
require_once 'baza.php';

function dohvatiPodatke($sql, $parametri = []) {
    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare($sql);
    $tipovi = str_repeat('s', count($parametri));
    $stmt->bind_param($tipovi, ...$parametri);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $podaci = $rezultat->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $konekcija->close();
    return $podaci;
}

function executeQuery($sql, $parametri = []) {
    $konekcija = poveziBazu();
    $stmt = $konekcija->prepare($sql);
    $tipovi = str_repeat('s', count($parametri));
    $stmt->bind_param($tipovi, ...$parametri);
    $stmt->execute();
    $stmt->close();
    $konekcija->close();
}

// Funkcija za prijavu korisnika
function prijaviKorisnika($korisnickoIme, $lozinka) {
    $sql = "SELECT * FROM Korisnici WHERE korisnicko_ime = ? AND lozinka = ?";
    $rezultat = dohvatiPodatke($sql, [$korisnickoIme, md5($lozinka)]); // Pretpostavka da je lozinka enkriptovana md5

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
    $proveriKorisnika = dohvatiPodatke("SELECT * FROM Korisnici WHERE korisnicko_ime = ?", [$korisnickoIme]);

    if (count($proveriKorisnika) > 0) {
        echo "Korisnik vec postoji";
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

function daLiJeKorisnikAdmin() {
    // Proverite da li je korisnik ulogovan
    if (!daLiJeKorisnikUlogovan()) {
        return false;
    }

    // Uzmi ID trenutno ulogovanog korisnika iz sesije
    $korisnik_id = $_SESSION['korisnik_id'];

    // Poveži se sa bazom
    $konekcija = poveziBazu();

    // Pretraži bazu da bi se proverilo da li je korisnik admin
    $upit = "SELECT uloga FROM Korisnici WHERE korisnik_id = ?";
    $stmt = $konekcija->prepare($upit);
    $stmt->bind_param('i', $korisnik_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $korisnik = $result->fetch_assoc();

    // Zatvori konekciju
    zatvoriKonekciju($konekcija);

    // Proveri ulogu korisnika
    return $korisnik['uloga'] === 'admin';
}


function dohvatiProizvode() {
    $konekcija = poveziBazu();
    $sql = "SELECT * FROM Proizvodi";
    $rezultat = $konekcija->query($sql);

    if ($rezultat === false) {
        die("Problem u hvatanju podataka: " . $konekcija->error);
    }

    $proizvodi = $rezultat->fetch_all(MYSQLI_ASSOC);
    $konekcija->close();

    return $proizvodi;
}

function dodajPorudzbinu($konekcija, $korisnik_id, $proizvod_id, $kolicina) {
    $upit_porudzbina = "INSERT INTO Porudzbine (korisnik_id, proizvod_id, kolicina) VALUES (?, ?, ?)";
    $stmt = $konekcija->prepare($upit_porudzbina);
    $stmt->bind_param('iii', $korisnik_id, $proizvod_id, $kolicina);
    if ($stmt->execute()) {
        return $konekcija->insert_id; // Vraća ID nove porudžbine
    } else {
        echo "Greška pri kreiranju porudžbine: " . $konekcija->error;
        return false;
    }
}
function dodajProizvod($konekcija, $naziv_proizvoda, $opis, $cena, $kolicina_na_stanju) {
    $upit = "INSERT INTO Proizvodi (naziv_proizvoda, opis, cena, kolicina_na_stanju) VALUES (?, ?, ?, ?)";
    $stmt = $konekcija->prepare($upit);
    $stmt->bind_param('ssdi', $naziv_proizvoda, $opis, $cena, $kolicina_na_stanju);
    return $stmt->execute();
}

function obrisiProizvod($konekcija, $proizvod_id) {
    $upit = "DELETE FROM Proizvodi WHERE proizvod_id = ?";
    $stmt = $konekcija->prepare($upit);
    $stmt->bind_param('i', $proizvod_id);
    if ($stmt->execute()) {
        return true;
    } else {
        echo "Greška pri brisanju proizvoda: " . $konekcija->error;
        return false;
    }
}

function azurirajKolicinuProizvoda($konekcija, $proizvod_id, $kolicina) {
    // Upit za ažuriranje količine na stanju
    $upit = "UPDATE Proizvodi SET kolicina_na_stanju = kolicina_na_stanju + ? WHERE proizvod_id = ?";
    $stmt = $konekcija->prepare($upit);
    
    // Proveri da li je priprema upita uspešna
    if ($stmt === false) {
        echo "Greška pri pripremi upita: " . $konekcija->error;
        return false;
    }
    
    // Bind parametri i izvrši upit
    $stmt->bind_param('ii', $kolicina, $proizvod_id);
    
    // Izvrši upit i proveri rezultat
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        echo "Greška pri ažuriranju količine proizvoda: " . $konekcija->error;
        $stmt->close();
        return false;
    }
}

function dodajStavkuPorudzbine($konekcija, $porudzbina_id, $proizvod_id, $kolicina) {
    $upit_stavka_porudzbine = "INSERT INTO Stavke_Porudzbine (porudzbina_id, proizvod_id, kolicina) VALUES (?, ?, ?)";
    $stmt = $konekcija->prepare($upit_stavka_porudzbine);
    $stmt->bind_param('iii', $porudzbina_id, $proizvod_id, $kolicina);
    if ($stmt->execute()) {
        return true;
    } else {
        echo "Greška pri dodavanju stavke porudžbine: " . $konekcija->error;
        return false;
    }
}