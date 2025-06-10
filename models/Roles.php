<?php

namespace Model;

use Model\ActiveRecord;

class Roles extends ActiveRecord {
    
    public static $tabla = 'roles';
    public static $idTabla = 'id_rol';
    public static $columnasDB = [
        'id_rol',
        'nombre_rol',
        'descripcion',
        'fecha_creacion'
    ];

    public $id_rol;
    public $nombre_rol;
    public $descripcion;
    public $fecha_creacion;

    public function __construct($rol = [])
    {
        $this->id_rol = $rol['id_rol'] ?? null;
        $this->nombre_rol = $rol['nombre_rol'] ?? '';
        $this->descripcion = $rol['descripcion'] ?? '';
        $this->fecha_creacion = $rol['fecha_creacion'] ?? null;
    }
}
?>