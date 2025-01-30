<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Redirecciona si no esta auntenticado
function isAuth() : void {

    if(!isset($_SESSION['login'])) {
        header('location: /');
    }
}

// Redirecciona si no es admin
function isAdmin() : void {
    if(!isset($_SESSION['admin'])) {
        header('location: /');
    }
}