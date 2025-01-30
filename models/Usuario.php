<?php

namespace Model;

class Usuario extends ActiveRecord {

    public static $tabla = 'usuarios';
    public static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {

        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    public function validarCuenta() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->apellido) {
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        } else if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Por favor ingresa un email válido';
        }

        if(!$this->telefono) {
            self::$alertas['error'][] = 'El télefono es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'Introduce una contraseña';
        } else if(strlen($this->password) < 8) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 8 caracteres';
        }

        return self::$alertas;
    }

    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'Introduce un email';
        } else if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Por favor ingresa un email válido';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'Introduce una contraseña';
        } 

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'Introduce un email';
        } else if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Por favor ingresa un email válido';
        }

        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'Introduce una contraseña';
        } else if(strlen($this->password) < 8) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 8 caracteres';
        }

        return self::$alertas;
    }

    // Verifica que un usuario existe
    public function existeUsuario() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);
        
        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya esta registrado. Intenta otro email ';
        }

        return $resultado;
    }

    // Hashear password
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Crear token
    public function crearToken() {
        // $this->token = uniqid();
        $this->token = bin2hex(random_bytes(6));
    }

    // Comprobar el password y si el usuario esta verificado
    public function comprobarPasswordAndToken($password) {
        $resultado = password_verify($password, $this->password);
        
        if(!$this->confirmado) {
            self::$alertas['error'][] = 'Usuario no confirmado';
        } else if(!$resultado) {
            self::$alertas['error'][] = 'Contraseña incorrecta';
        } else {
            return true;
        }
    }

}