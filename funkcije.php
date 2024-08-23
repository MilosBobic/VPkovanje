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

// Dohvati sve porudžbine
function dohvatiPorudzbine() {
    $konekcija = poveziBazu();
    $sql = "SELECT * FROM Porudzbine";
    $stmt = $konekcija->prepare($sql);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $Porudzbine = $rezultat->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $konekcija->close();
    return $Porudzbine;
}

// Dohvati detalje porudžbine po ID-u
function dohvatiDetaljePorudzbinePoId($porudzbina_id) {
    $konekcija = poveziBazu();
    $sql = "SELECT * FROM order_details WHERE porudzbina_id = ?";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("i", $porudzbina_id);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $porudzbina = $rezultat->fetch_assoc();
    $stmt->close();
    $konekcija->close();
    return $porudzbina;
}

// Dodaj novu porudžbinu
function dodajPorudzbinu($proizvod_id, $kolicina, $dobavljac_id, $korisnik_id) {
    // Poveži se sa bazom
    $konekcija = poveziBazu();

    // Dodavanje nove porudžbine u tabelu `Porudzbine`
    $sql = "INSERT INTO Porudzbine (proizvod_id, kolicina, dobavljac_id, korisnik_id) VALUES (?, ?, ?, ?)";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("iiii", $proizvod_id, $kolicina, $dobavljac_id, $korisnik_id);
    $stmt->execute();

    // Uzmi poslednji umetnuti ID za porudžbinu
    $porudzbina_id = $stmt->insert_id;
    $stmt->close();

    // Zatvori konekciju sa bazom
    $konekcija->close();

    return $porudzbina_id; // Vrati ID umetnute porudžbine
}


// Dodaj stavke u porudžbinu
function dodajDetaljePorudzbine($porudzbina_id, $proizvod_id, $kolicina) {
    $konekcija = poveziBazu();
    $sql = "INSERT INTO Porudzbine (porudzbina_id, proizvod_id, kolicina, stanje) VALUES (?, ?, ?, 'Poručeno')";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("iii", $porudzbina_id, $proizvod_id, $kolicina);
    $stmt->execute();
    $stmt->close();
    $konekcija->close();
}

// Ažuriraj status porudžbine
function azurirajStatusPorudzbine($porudzbina_id, $status) {
    $konekcija = poveziBazu();
    $sql = "UPDATE Porudzbine SET stanje = ? WHERE porudzbina_id = ?";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("si", $status, $porudzbina_id);
    $stmt->execute();

    if ($status === 'Stiglo') {
        // Dodaj u inventar
        $detaljiPorudzbine = dohvatiDetaljePorudzbinePoId($porudzbina_id);
        foreach ($detaljiPorudzbine as $stavka) {
            dodajUInventar($stavka['proizvod_id'], $stavka['kolicina']);
        }
    }

    $stmt->close();
    $konekcija->close();
}

// Dodaj u inventar
function dodajUInventar($proizvod_id, $kolicina) {
    $konekcija = poveziBazu();
    $sql = "UPDATE proizvodi SET kolicina_na_stanju = kolicina_na_stanju + ? WHERE proizvod_id = ?";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("ii", $kolicina, $proizvod_id);
    $stmt->execute();
    $stmt->close();
    $konekcija->close();
}

// Obriši porudžbinu
function obrisiPorudzbinu($porudzbina_id) {
    $konekcija = poveziBazu();
    $sql = "DELETE FROM order_details WHERE porudzbina_id = ?";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("i", $porudzbina_id);
    $stmt->execute();
    $stmt->close();
    
    // Takođe obriši iz tabele `Porudzbine`
    $sql = "DELETE FROM Porudzbine WHERE porudzbina_id = ?";
    $stmt = $konekcija->prepare($sql);
    $stmt->bind_param("i", $porudzbina_id);
    $stmt->execute();
    $stmt->close();
    
    $konekcija->close();
}

// Funkcija za dohvatanje dobavljača iz baze
function dohvatiDobavljace() {
    $konekcija = poveziBazu();
    $sql = "SELECT * FROM Dobavljaci";
    $stmt = $konekcija->prepare($sql);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $dobavljaci = $rezultat->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $konekcija->close();
    return $dobavljaci;
}

function dohvatiNaziveProizvodaIzPorudzbina() {
    $konekcija = poveziBazu();
    
    // SQL upit koji vraća samo naziv proizvoda
    $sql = "
        SELECT pr.naziv_proizvoda 
        FROM Porudzbine p
        JOIN Proizvodi pr ON p.proizvod_id = pr.proizvod_id";
    
    $stmt = $konekcija->prepare($sql);
    $stmt->execute();
    $rezultat = $stmt->get_result();
    $naziviProizvoda = $rezultat->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $konekcija->close();
    
    return $naziviProizvoda;
}
