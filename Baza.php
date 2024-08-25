<?php
class Baza {
    private $veza;

    public function __construct() {
        $this->veza = new mysqli('localhost', 'root', '', 'baza_kika');
        if ($this->veza->connect_error) {
            die("Ne mogu se povezati sa bazom: " . $this->veza->connect_error);
        }
    }

    public function fetchResults($sql, $parametri = [], $tipovi = '') {
        $stmt = $this->veza->prepare($sql);
        if ($parametri) {
            $stmt->bind_param($tipovi, ...$parametri);
        }
        $stmt->execute();
        $rezultat = $stmt->get_result();
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }

    public function executeQuery($sql, $parametri = [], $tipovi = '') {
        $stmt = $this->veza->prepare($sql);
        if ($parametri) {
            $stmt->bind_param($tipovi, ...$parametri);
        }
        $stmt->execute();
        $rezultat = $stmt->get_result();
        
        if ($rezultat === false) {
            return false;
        }
        
        return $rezultat->fetch_all(MYSQLI_ASSOC); // Vraća niz svih rezultata
    }

    public function getConnection() {
        return $this->veza;
    }

    public function __destruct() {
        $this->veza->close();
    }
}
