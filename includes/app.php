<?php 
use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';

// Configuracion de variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require 'funciones.php';
require 'database.php';

// Conectarnos a la base de datos
ActiveRecord::setDB($db);