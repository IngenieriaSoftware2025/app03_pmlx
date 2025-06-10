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
}
?>