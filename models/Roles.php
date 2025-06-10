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

    // Validar nuevo rol
    public function validar() {
        if(!$this->nombre_rol) {
            self::$alertas['error'][] = 'El Nombre del Rol es Obligatorio';
        }
        if(!$this->descripcion) {
            self::$alertas['error'][] = 'La Descripción es Obligatoria';
        }

        return self::$alertas;
    }

    // Obtener roles activos para select
    public static function obtenerActivos() {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY nombre_rol ASC";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
}

?>