<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController {
    public static function index(Router $router) {
        session_start();
        // Verificar que es admin
        isAdmin();

        $servicios = Servicio::all();

        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear(Router $router) {
        session_start();
        // Verificar que es admin
        isAdmin();
        
        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('location: /servicios');
            }
        }

        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router) {
        session_start();
        // Verificar que es admin
        isAdmin();

        // importante traerse el id del elemento a actualizar y VALIDAR
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if(!$id) return;

        $servicio = Servicio::find($id);
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    // Tipo 
    public static function eliminar() {
        session_start();
        // Verificar que es admin
        isAdmin();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // validar
            $id = $_POST['id'];
            if(filter_var($id, FILTER_VALIDATE_INT)) {
                
                $servicio = Servicio::find($id);
                $servicio->eliminar();
            };

            header('location: /servicios');
        }
    }
}