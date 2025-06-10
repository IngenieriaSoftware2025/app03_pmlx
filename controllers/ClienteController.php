<?php 

namespace Controllers;

use Exception;
use Model\Clientes;
use Model\ActiveRecord;
use MVC\Router;

class ClienteController {

    public static function mostrarPagina(Router $router) {
        $router->render('clientes/index', []);
    }

    public static function buscarCliente() {
        // ✅ Agregar esta función si existe en tu proyecto
        if (function_exists('getHeadersApi')) {
            getHeadersApi();
        }
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $clientes = Clientes::all();
            
            if ($clientes) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Clientes encontrados',
                    'data' => $clientes
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron clientes',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar clientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarCliente() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['nombres']) || empty($_POST['apellidos']) || empty($_POST['telefono']) || empty($_POST['correo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        try {
            // Validar y limpiar datos
            $_POST['nombres'] = ucwords(strtolower(trim(htmlspecialchars($_POST['nombres']))));
            $_POST['apellidos'] = ucwords(strtolower(trim(htmlspecialchars($_POST['apellidos']))));
            $_POST['telefono'] = filter_var($_POST['telefono'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['nit'] = trim(htmlspecialchars($_POST['nit']));
            $_POST['correo'] = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);

            // Validaciones
            if (strlen($_POST['nombres']) < 2) {
                throw new Exception('El nombre es inválido');
            }
            if (strlen($_POST['apellidos']) < 2) {
                throw new Exception('El apellido es inválido');
            }
            if (strlen($_POST['telefono']) != 8) {
                throw new Exception('Teléfono debe tener 8 números');
            }
            if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico es inválido');
            }

            $cliente = new Clientes([
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'telefono' => $_POST['telefono'],
                'nit' => $_POST['nit'],
                'correo' => $_POST['correo'],
                'situacion' => 1
            ]);

            $resultado = $cliente->crear();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Cliente guardado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar el cliente'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function modificarCliente() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_cliente'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de cliente requerido'
            ]);
            return;
        }

        try {
            $cliente = Clientes::find($_POST['id_cliente']);
            
            if (!$cliente) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }

            // Actualizar datos
            $cliente->nombres = ucwords(strtolower(trim(htmlspecialchars($_POST['nombres']))));
            $cliente->apellidos = ucwords(strtolower(trim(htmlspecialchars($_POST['apellidos']))));
            $cliente->telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_NUMBER_INT);
            $cliente->nit = trim(htmlspecialchars($_POST['nit']));
            $cliente->correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);

            $resultado = $cliente->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Cliente modificado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el cliente'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarCliente() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_cliente'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de cliente requerido'
            ]);
            return;
        }

        try {
            $cliente = Clientes::find($_POST['id_cliente']);
            
            if (!$cliente) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }

            $resultado = $cliente->eliminar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Cliente eliminado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar el cliente'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}