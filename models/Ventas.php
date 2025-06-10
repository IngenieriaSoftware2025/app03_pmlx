<?php

namespace Model;

use Model\ActiveRecord;

class Ventas extends ActiveRecord {
    
    public static $tabla = 'ventas';
    public static $idTabla = 'id_venta';
    public static $columnasDB = [
        'id_venta',
        'id_cliente',
        'id_usuario',
        'fecha_venta',
        'subtotal',
        'impuesto',
        'total',
        'estado_venta',
        'observaciones'
    ];

    public $id_venta;
    public $id_cliente;
    public $id_usuario;
    public $fecha_venta;
    public $subtotal;
    public $impuesto;
    public $total;
    public $estado_venta;
    public $observaciones;

    public function __construct($venta = [])
    {
        $this->id_venta = $venta['id_venta'] ?? null;
        $this->id_cliente = $venta['id_cliente'] ?? null;
        $this->id_usuario = $venta['id_usuario'] ?? null;
        $this->fecha_venta = $venta['fecha_venta'] ?? null;
        $this->subtotal = $venta['subtotal'] ?? 0;
        $this->impuesto = $venta['impuesto'] ?? 0;
        $this->total = $venta['total'] ?? 0;
        $this->estado_venta = $venta['estado_venta'] ?? 'Completada';
        $this->observaciones = $venta['observaciones'] ?? '';
    }
}

?>