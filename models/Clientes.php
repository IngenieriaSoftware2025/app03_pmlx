<?php

// crea nombre de espacio Model
namespace Model;

// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;

// Crea la clase de instancia Clientes y hereda las funciones de ActiveRecord
class Clientes extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'clientes';
    public static $idTabla = 'id_cliente';
    public static $columnasDB = 
    [
        'nombres',
        'apellidos',
        'telefono',
        'nit',
        'correo',
        'situacion'
    ];

    // Crea las variables para almacenar los datos
    public $id_cliente;
    public $nombres;
    public $apellidos;
    public $telefono;
    public $nit;
    public $correo;
    public $situacion;

    public function __construct($cliente = [])
    {
        $this->id_cliente = $cliente['id_cliente'] ?? null;
        $this->nombres = $cliente['nombres'] ?? '';
        $this->apellidos = $cliente['apellidos'] ?? '';
        $this->telefono = $cliente['telefono'] ?? '';
        $this->nit = $cliente['nit'] ?? '';
        $this->correo = $cliente['correo'] ?? '';
        $this->situacion = $cliente['situacion'] ?? 1;
    }
}