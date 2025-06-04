<?php 

namespace Controllers;

use Exception;
use Model\Clientes;
use Model\ActiveRecord;
use MVC\Router;

class ClienteController extends ActiveRecord{

    public static function mostrarPagina (Router $router){

        $router->render('clientes/index', []);

    }

    public static function guardarCliente()
    {

        getHeadersApi();
        echo json_encode($_POST);
        $_POST['nombres']= ucwords(strtolower(trim(htmlspecialchars($_POST['nombres']))));
        $cantidad_nombre = strlen($_POST['nombres']);
        if($cantidad_nombre < 2 ){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'EL nombre es invalido'
            ]);
        }


        $_POST['apellidos'] = ucwords(strtolower(trim(htmlspecialchars($_POST['apellidos']))));
        $cantidad_apellido = strlen($_POST['nombres']);
        if($cantidad_apellido < 2 ){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'EL apellido es invalido'
            ]);
        }


         $_POST['telefono'] = filter_var($_POST['telefono']. FILTER_SANITIZE_NUMBER_INT);
         if(strlen($_POST['telefono']) !=8){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Telefono debe tener 8 numeros'
            ]);
         }
        $_POST['nit'];

        $_POST['correo']= filter_var($_POST['correo']. FILTER_SANITIZE_EMAIL);
         if($_POST['correo']){
            http_response_code(200);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'EL correo electronico es invalido'
            ]);

           try{
            $clientes = new Clientes(
                [
                    'nombres' => $_POST['nombres'],
                    'apellidos' => $_POST['apellidos'],
                    'telefono' => $_POST['telefono'],
                    'nit' => $_POST['nit'],
                    'correo' => $_POST['correo'],
                    'situacion' => 1
                ]
            );

            http_response_code(200);
            echo json_encode([
                'coigo' =>1,
                'mensaje' => 'exito al guardar'
            ]);

           } catch (Exception $e){
             http_response_code(200);
            echo json_encode([
                'coigo' =>0,
                'mensaje' => 'error al guardar',
                'detalle' => $e->getMessage()
            ]);

           }
    }
}
    }
