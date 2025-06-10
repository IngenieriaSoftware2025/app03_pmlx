<?php

namespace Model;

use Model\ActiveRecord;

class Inventario extends ActiveRecord {
    
    public static $tabla = 'inventario';
    public static $idTabla = 'id_inventario';
    public static $columnasDB = [
        'id_inventario',
        'modelo',
        'id_marca',
        'imei',
        'numero_serie',
        'estado_dispositivo',
        'precio_compra',
        'precio_venta',
        'stock',
        'descripcion',
        'fecha_ingreso',
        'fecha_actualizacion'
    ];

    public $id_inventario;
    public $modelo;
    public $id_marca;
    public $imei;
    public $numero_serie;
    public $estado_dispositivo;
    public $precio_compra;
    public $precio_venta;
    public $stock;
    public $descripcion;
    public $fecha_ingreso;
    public $fecha_actualizacion;

    public function __construct($inventario = [])
    {
        $this->id_inventario = $inventario['id_inventario'] ?? null;
        $this->modelo = $inventario['modelo'] ?? '';
        $this->id_marca = $inventario['id_marca'] ?? null;
        $this->imei = $inventario['imei'] ?? '';
        $this->numero_serie = $inventario['numero_serie'] ?? '';
        $this->estado_dispositivo = $inventario['estado_dispositivo'] ?? '';
        $this->precio_compra = $inventario['precio_compra'] ?? 0;
        $this->precio_venta = $inventario['precio_venta'] ?? 0;
        $this->stock = $inventario['stock'] ?? 0;
        $this->descripcion = $inventario['descripcion'] ?? '';
        $this->fecha_ingreso = $inventario['fecha_ingreso'] ?? null;
        $this->fecha_actualizacion = $inventario['fecha_actualizacion'] ?? null;
    }
}

?>