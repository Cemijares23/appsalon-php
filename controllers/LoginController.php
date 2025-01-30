<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Verificar que el usuario existe
                $usuario = Usuario::where('email', $auth->email); // devuelve una instancia de usuario si lo consigue en la DB

                if(!$usuario) {
                    Usuario::setAlerta('error', 'Usuario no encontrado'); // agregar alertas al array de alertas
                } else {
                    // Verificar el password
                    $verificado = $usuario->comprobarPasswordAndToken($auth->password);
                    if($verificado) {
                        // Autenticar usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . ' ' . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        // Redireccionamiento
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin;

                            header('location: /admin');
                        } else {
                            header('location: /cita');
                        }
                    }
                }
            }
        }
        
        $alertas = Usuario::getAlertas(); // traer todas las alertas antes del render
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(Router $router) {
        // Vaciar el array de sesion
        session_start();
        $_SESSION = [];

        // Redireccionar al usuario     
        header('location: /');
    }

    public static function olvidado(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                // Verificar que el usuario existe
                $usuario = Usuario::where('email', $auth->email);

                if(!$usuario || $usuario->confirmado === "0") {
                    Usuario::setAlerta('error', 'Usuario no existente o no está confirmado');
                    
                } else {
                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/password-olvidado', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $token = s($_GET['token'] ?? null);
        $error = false;

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);
        if(empty($usuario) || !$token) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Instanciamos nuevo password
            $password = new Usuario($_POST);

            // Validamos el password
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                // Borramos el passwod antiguo (no se asigna directamente por seguridad)
                $usuario->password = null;

                // Asignamos nuevo password a la instancia de usuario
                $usuario->password = $password->password;
                
                // Hasheamos nuevo password
                $usuario->hashPassword();

                // Reestablecer token
                $usuario->token = null;

                // Guardamos en la DB
                $resultado = $usuario->guardar();
                if($resultado) {
                    Usuario::setAlerta('exito', 'Tu password ha sido actualizado con exito!');
                    header('refresh: 3; url=/');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/password-recuperar', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarCuenta();

            // Validar en caso de que alertas este vacio
            if(empty($alertas)) {
                // Verificar que el usuario no exista
                $existe = $usuario->existeUsuario();

                if($existe->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Usuario no existente

                    $usuario->hashPassword(); // Hashear el password
                    $usuario->crearToken(); // Crear un token

                    // Generar email de confirmacion
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    // Guardar usuario en la DB
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        header('location: /mensaje?user=' . $usuario->email);
                    }
                }
            }
        }
        
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas'=> $alertas
        ]);
    }

    public static function confirmar (Router $router) {
        $alertas = [];
        $token = s($_GET['token']); // Sanitiza pq se va a ingresar a la DB
        $usuario = Usuario::where('token', $token); // SELECT * FROM $tabla WHERE token = '$token';
        
        if(!$usuario || !$token) {
            // Token no encontrado
            $alertas = Usuario::setAlerta('error', 'Token no válido'); // Llenar el array alertas 
        } else {
            // Token encontrado
            
            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar();
            $alertas = Usuario::setAlerta('exito', 'Tu cuenta ha sido comprobada correctamente!');
        }

        // Metodo para obtener alertas (recuerda que es protected)
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }

    public static function mensaje (Router $router) {
        $email = $_GET['user'];

        $router->render('auth/mensaje', [
            'email' => $email
        ]);
    }
}