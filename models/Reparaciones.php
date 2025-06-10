<?php

namespace Model;

use Model\ActiveRecord;

class Reparaciones extends ActiveRecord {
    
    public static $tabla = 'reparaciones';
    public static $idTabla = 'id_reparacion';
    public static $columnasDB = [
        'id_reparacion',
        'id_cliente',
        'tipo_celular',
        'marca_celular',
        'modelo_celular',
        'imei',
        'motivo_ingreso',
        'descripcion_problema',
        'fecha_ingreso',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'id_trabajador',
        'estado_reparacion',
        'costo_reparacion',
        'observaciones'
    ];

    public $id_reparacion;
    public $id_cliente;
    public $tipo_celular;
    public $marca_celular;
    public $modelo_celular;
    public $imei;
    public $motivo_ingreso;
    public $descripcion_problema;
    public $fecha_ingreso;
    public $fecha_entrega_estimada;
    public $fecha_entrega_real;
    public $id_trabajador;
    public $estado_reparacion;
    public $costo_reparacion;
    public $observaciones;

    public function __construct($reparacion = [])
    {
        $this->id_reparacion = $reparacion['id_reparacion'] ?? null;
        $this->id_cliente = $reparacion['id_cliente'] ?? null;
        $this->tipo_celular = $reparacion['tipo_celular'] ?? '';
        $this->marca_celular = $reparacion['marca_celular'] ?? '';
        $this->modelo_celular = $reparacion['modelo_celular'] ?? '';
        $this->imei = $reparacion['imei'] ?? '';
        $this->motivo_ingreso = $reparacion['motivo_ingreso'] ?? '';
        $this->descripcion_problema = $reparacion['descripcion_problema'] ?? '';
        $this->fecha_ingreso = $reparacion['fecha_ingreso'] ?? null;
        $this->fecha_entrega_estimada = $reparacion['fecha_entrega_estimada'] ?? null;
        $this->fecha_entrega_real = $reparacion['fecha_entrega_real'] ?? null;
        $this->id_trabajador = $reparacion['id_trabajador'] ?? null;
        $this->estado_reparacion = $reparacion['estado_reparacion'] ?? 'Recibido';
        $this->costo_reparacion = $reparacion['costo_reparacion'] ?? 0;
        $this->observaciones = $reparacion['observaciones'] ?? '';
    }
}

?>