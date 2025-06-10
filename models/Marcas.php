<?php

namespace Model;

use Model\ActiveRecord;

class Marcas extends ActiveRecord {
    
    public static $tabla = 'marcas';
    public static $idTabla = 'id_marca';
    public static $columnasDB = [
        'id_marca',
        'nombre_marca',
        'descripcion',
        'activo',
        'fecha_creacion'
    ];

    public $id_marca;
    public $nombre_marca;
    public $descripcion;
    public $activo;
    public $fecha_creacion;

    public function __construct($marca = [])
    {
        $this->id_marca = $marca['id_marca'] ?? null;
        $this->nombre_marca = $marca['nombre_marca'] ?? '';
        $this->descripcion = $marca['descripcion'] ?? '';
        $this->activo = $marca['activo'] ?? 'S';
        $this->fecha_creacion = $marca['fecha_creacion'] ?? null;
    }
}

?>