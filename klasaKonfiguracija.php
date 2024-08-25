<?php

class Konfiguracija
{
    private static $instance = null;

    // Privatni konstruktor za implementaciju Singleton obrasca
    private function __construct()
    {
        $this->postaviGreske();
    }

    // Dobija jedinstvenu instancu klase (Singleton)
    public static function getInstanca()
    {
        if (self::$instance == null) {
            self::$instance = new Konfiguracija();
        }

        return self::$instance;
    }

    // Metoda za postavljanje podešavanja za greške
    private function postaviGreske()
    {
        error_reporting(E_ALL); // Prikazuje sve vrste grešaka
        ini_set('display_errors', '1'); // Prikazuje greške na izlaznom ekranu
        ini_set('log_errors', '1'); // Loguje greške u error log
        ini_set('error_log', __DIR__ . '/error_log.txt'); // Putanja do fajla gde će se logovati greške
    }
}
