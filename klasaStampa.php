<?php
require_once 'Baza.php'; // Uključivanje fajla sa klasom Baza

class Stampa
{
    private $baza;

    public function __construct()
    {
        // Kreiramo instancu klase Baza za rad sa bazom podataka
        $this->baza = new Baza();
    }

    // Funkcija za preuzimanje podataka iz tabele Stavke_Porudzbine za trenutni mesec
    private function preuzmiPodatkeZaTrenutniMesec()
    {
        $trenutniMesec = date('Y-m'); // Dohvatamo trenutni mesec u formatu YYYY-MM
        $sql = "SELECT * FROM Stavke_Porudzbine WHERE DATE_FORMAT(datum_porudzbine, '%Y-%m') = ?";
        
        // Koristimo fetchResults metodu iz klase Baza
        $rezultat = $this->baza->fetchResults($sql, [$trenutniMesec], 's');

        if (!$rezultat) {
            die("Došlo je do greške prilikom preuzimanja podataka.");
        }

        return $rezultat;
    }

    private function preuzmiPodatkeZaTrenutniMesecAdmin()
    {
        $trenutniMesec = date('Y-m'); // Dohvatamo trenutni mesec u formatu YYYY-MM
        $sql = "SELECT * FROM Porudzbine WHERE DATE_FORMAT(datum_porudzbine, '%Y-%m') = ?";
        
        // Koristimo fetchResults metodu iz klase Baza
        $rezultat = $this->baza->fetchResults($sql, [$trenutniMesec], 's');

        if (!$rezultat) {
            die("Došlo je do greške prilikom preuzimanja podataka.");
        }

        return $rezultat;
    }

    // Funkcija za generisanje i preuzimanje txt fajla
    public function generisiTxtFajl()
    {
        $rezultat = $this->preuzmiPodatkeZaTrenutniMesec();

        // Naziv fajla sa trenutnim datumom
        $nazivFajla = date('Y-m-d') . ".txt";

        // Postavljamo HTTP zaglavlja za preuzimanje fajla
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $nazivFajla . '"');

        // Iteriramo kroz rezultate i generišemo sadržaj fajla
        foreach ($rezultat as $red) {
            echo "stavka_id: " . $red['stavka_id'] . "\n";
            echo "porudzbina_id: " . $red['porudzbina_id'] . "\n";
            echo "proizvod_id: " . $red['proizvod_id'] . "\n";
            echo "kolicina: " . $red['kolicina'] . "\n";
            echo "datum_porudzbine: " . $red['datum_porudzbine'] . "\n";
            echo "---------------------------\n";
        }
    }

    public function generisiTxtFajlAdmin()
    {
        $rezultat = $this->preuzmiPodatkeZaTrenutniMesecAdmin();

        // Naziv fajla sa trenutnim datumom
        $nazivFajla = date('Y-m-d') . "-admin" .  ".txt";

        // Postavljamo HTTP zaglavlja za preuzimanje fajla
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $nazivFajla . '"');

        // Iteriramo kroz rezultate i generišemo sadržaj fajla
        foreach ($rezultat as $red) {
            echo "stavka_id: " . $red['stavka_id'] . "\n";
            echo "porudzbina_id: " . $red['porudzbina_id'] . "\n";
            echo "proizvod_id: " . $red['proizvod_id'] . "\n";
            echo "kolicina: " . $red['kolicina'] . "\n";
            echo "datum_porudzbine: " . $red['datum_porudzbine'] . "\n";
            echo "korisnik_id: " . $red['korisnik_id'] . "\n";
            echo "---------------------------\n";
        }
    }
}