<?php

namespace Model;

use Model\ActiveRecord;

class Clientes extends ActiveRecord {
    
    public static $tabla = 'clientes';
    public static $idTabla = 'id_cliente';
    public static $columnasDB = [
        'id_cliente',
        'nombre',
        'apellido',
        'cedula',
        'nit',
        'email',
        'telefono',
        'direccion',
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    public $id_cliente;
    public $nombre;
    public $apellido;
    public $cedula;
    public $nit;
    public $email;
    public $telefono;
    public $direccion;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($cliente = [])
    {
        $this->id_cliente = $cliente['id_cliente'] ?? null;
        $this->nombre = $cliente['nombre'] ?? '';
        $this->apellido = $cliente['apellido'] ?? '';
        $this->cedula = $cliente['cedula'] ?? '';
        $this->nit = $cliente['nit'] ?? '';
        $this->email = $cliente['email'] ?? '';
        $this->telefono = $cliente['telefono'] ?? '';
        $this->direccion = $cliente['direccion'] ?? '';
        $this->fecha_creacion = $cliente['fecha_creacion'] ?? null;
        $this->fecha_actualizacion = $cliente['fecha_actualizacion'] ?? null;
    }
}

?>