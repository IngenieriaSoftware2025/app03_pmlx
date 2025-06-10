<?php

namespace Model;

use Model\ActiveRecord;

class DetalleVentas extends ActiveRecord {
    
    public static $tabla = 'detalle_ventas';
    public static $idTabla = 'id_detalle';
    public static $columnasDB = [
        'id_detalle',
        'id_venta',
        'id_inventario',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public $id_detalle;
    public $id_venta;
    public $id_inventario;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;

    public function __construct($detalle = [])
    {
        $this->id_detalle = $detalle['id_detalle'] ?? null;
        $this->id_venta = $detalle['id_venta'] ?? null;
        $this->id_inventario = $detalle['id_inventario'] ?? null;
        $this->cantidad = $detalle['cantidad'] ?? 1;
        $this->precio_unitario = $detalle['precio_unitario'] ?? 0;
        $this->subtotal = $detalle['subtotal'] ?? 0;
    }
}

?>