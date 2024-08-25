<?php
require_once 'Baza.php';

class Proizvod {
    private $baza;

    public function __construct() {
        $this->baza = new Baza();
    }

    public function dohvatiProizvode() {
        $sql = "SELECT * FROM Proizvodi";
        return $this->baza->fetchResults($sql);
    }


    public function dohvatiProizvodePoCeni($minCena, $maxCena) {
        $sql = "SELECT * FROM Proizvodi WHERE cena BETWEEN ? AND ?";
        $parametri = [$minCena, $maxCena];
        $result = $this->baza->executeQuery($sql, $parametri, 'dd'); // 'dd' za decimal

        if (!$result) {
            echo "SQL greška: " . $this->baza->getConnection()->error;
            return false;
        }

        return $result;
    }

    public function brojProizvoda() {
        $sql = "SELECT COUNT(*) AS broj FROM Proizvodi";
        $rezultat = $this->baza->executeQuery($sql, [], '');
        return $rezultat[0]['broj'];
    }

    public function izmeniProizvod($proizvod_id, $naziv_proizvoda, $opis, $cena, $kolicina_na_stanju) {
        if ($naziv_proizvoda === null || $opis === null || $cena === null || $kolicina_na_stanju === null) {
            throw new InvalidArgumentException('Svi parametri moraju biti navedeni.');
        }
        
        $sql = "UPDATE Proizvodi SET naziv_proizvoda = ?, opis = ?, cena = ?, kolicina_na_stanju = ? WHERE proizvod_id = ?";
        $parametri = [$naziv_proizvoda, $opis, $cena, $kolicina_na_stanju, $proizvod_id];
        $this->baza->executeQuery($sql, $parametri, 'ssddi');
    }
    
    public function dohvatiProizvodPoId($proizvod_id) {
        $sql = "SELECT * FROM Proizvodi WHERE proizvod_id = ?";
        $rezultat = $this->baza->executeQuery($sql, [$proizvod_id], 'i');
        return $rezultat[0] ?? null;
    }

    public function obrisatiProizvod($proizvod_id) {
        $sql = "DELETE FROM Proizvodi WHERE proizvod_id = ?";
        $this->baza->executeQuery($sql, [$proizvod_id], 'i');
    }
}
