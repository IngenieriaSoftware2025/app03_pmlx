<?php

namespace Model;

use Model\ActiveRecord;

class Usuarios extends ActiveRecord {
    
    public static $tabla = 'usuarios';
    public static $idTabla = 'id_usuario';
    public static $columnasDB = [
        'id_usuario',
        'nombre',
        'apellido',
        'email',
        'password',
        'id_rol',
        'activo',
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    public $id_usuario;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $id_rol;
    public $activo;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($usuario = [])
    {
        $this->id_usuario = $usuario['id_usuario'] ?? null;
        $this->nombre = $usuario['nombre'] ?? '';
        $this->apellido = $usuario['apellido'] ?? '';
        $this->email = $usuario['email'] ?? '';
        $this->password = $usuario['password'] ?? '';
        $this->id_rol = $usuario['id_rol'] ?? null;
        $this->activo = $usuario['activo'] ?? 'S';
        $this->fecha_creacion = $usuario['fecha_creacion'] ?? null;
        $this->fecha_actualizacion = $usuario['fecha_actualizacion'] ?? null;
    }

    // Buscar usuario por email para login
    public static function buscarPorEmail($email) {
        $query = "SELECT u.*, r.nombre_rol FROM usuarios u 
                  JOIN roles r ON u.id_rol = r.id_rol 
                  WHERE u.email = '" . self::$db->escape_string($email) . "' 
                  AND u.activo = 'S'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Obtener usuarios con rol
    public static function obtenerConRol() {
        $query = "SELECT u.*, r.nombre_rol FROM usuarios u 
                  JOIN roles r ON u.id_rol = r.id_rol 
                  ORDER BY u.fecha_creacion DESC";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Validar login
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }

        return self::$alertas;
    }

    // Validar nuevo usuario
    public function validarNuevoUsuario() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        if(!$this->id_rol) {
            self::$alertas['error'][] = 'Selecciona un Rol';
        }

        return self::$alertas;
    }

    // Hashear password
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Verificar password
    public function comprobarPassword($password) {
        return password_verify($password, $this->password);
    }
}

?>