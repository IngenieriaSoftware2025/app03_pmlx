<?php

namespace Model;

use Model\ActiveRecord;

class HistorialVentas extends ActiveRecord {
    
    public static $tabla = 'historial_ventas';
    public static $idTabla = 'id_historial';
    public static $columnasDB = [
        'id_historial',
        'id_venta',
        'tipo_operacion',
        'fecha_operacion',
        'monto',
        'id_usuario',
        'descripcion'
    ];

    public $id_historial;
    public $id_venta;
    public $tipo_operacion;
    public $fecha_operacion;
    public $monto;
    public $id_usuario;
    public $descripcion;

    public function __construct($historial = [])
    {
        $this->id_historial = $historial['id_historial'] ?? null;
        $this->id_venta = $historial['id_venta'] ?? null;
        $this->tipo_operacion = $historial['tipo_operacion'] ?? '';
        $this->fecha_operacion = $historial['fecha_operacion'] ?? null;
        $this->monto = $historial['monto'] ?? 0;
        $this->id_usuario = $historial['id_usuario'] ?? null;
        $this->descripcion = $historial['descripcion'] ?? '';
    }
}

?>