<?php
// Konfiguracija baze podataka
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'baza_kika');

// URL aplikacije
define('BASE_URL', 'http://localhost/VPKovanje/VPkovanje/');

// Ostale konfiguracije
define('APP_NAME', 'Kika Inventar');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Pokretanje sesije
session_start();
