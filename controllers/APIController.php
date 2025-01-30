<?php

namespace Controllers;

use Model\Cita;
use Model\CitasServicios;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar() {

        // Almacenamos cita en la base de datos
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $idCita = $resultado['id'];

        // Almacenamos relacion cita-servicio
        $idServicios = explode(',', $_POST['servicios']);
        foreach($idServicios as $idServicio) {
            $args = [
                'cita_id' => $idCita,
                'servicio_id' => $idServicio
            ];
            
            $citaServicio = new CitasServicios($args);
            $citaServicio->guardar();
        }

        $respuesta = [
            'resultado' => $resultado
        ];
    
        // Enviamos una respuesta json
        echo json_encode($respuesta);
    }

    public static function eliminar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            if(filter_var($id, FILTER_VALIDATE_INT)) {
                $cita = Cita::find($id);
                $cita->eliminar();
            }

            header('location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}