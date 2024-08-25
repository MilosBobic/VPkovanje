<?php
require_once 'Baza.php';

class Porudzbina {
    private $baza;

    public function __construct() {
        $this->baza = new Baza();
    }

    public function dodajPorudzbinu($korisnik_id, $proizvod_id, $kolicina) {
        $this->baza->getConnection()->begin_transaction();
        try {
            $sql = "INSERT INTO Porudzbine (korisnik_id, proizvod_id, kolicina) VALUES (?, ?, ?)";
            $parametri = [$korisnik_id, $proizvod_id, $kolicina];
            $this->baza->executeQuery($sql, $parametri, 'iii');
            $porudzbina_id = $this->baza->getConnection()->insert_id;

            $sql = "INSERT INTO Stavke_Porudzbine (porudzbina_id, proizvod_id, kolicina) VALUES (?, ?, ?)";
            $parametri = [$porudzbina_id, $proizvod_id, $kolicina];
            $this->baza->executeQuery($sql, $parametri, 'iii');

            $sql = "UPDATE Proizvodi SET kolicina_na_stanju = kolicina_na_stanju + ? WHERE proizvod_id = ?";
            $parametri = [$kolicina, $proizvod_id];
            $this->baza->executeQuery($sql, $parametri, 'ii');

            $this->baza->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->baza->getConnection()->rollback();
            return false;
        }
    }

    public function brojPorudzbina() {
        $sql = "SELECT COUNT(*) AS broj FROM Porudzbine";
        $rezultat = $this->baza->executeQuery($sql, [], '');
        return $rezultat[0]['broj'];
    }
}
