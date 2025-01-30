<?php

namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index(Router $router) {
        session_start();
        $nombre = $_SESSION['nombre'] ?? '';
        $id = $_SESSION['id'] ?? '';

        // Redireccionamos si no esta autenticado
        isAuth();

        $router->render('cita/index', [
            'nombre' => $nombre,
            'id' => $id
        ]);
    }
}