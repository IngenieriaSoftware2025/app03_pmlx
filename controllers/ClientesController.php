<?php 

namespace Controllers;

use Exception;
use Model\Clientes;
use Model\ActiveRecord;
use MVC\Router;

class ClientesController {

    public static function mostrarPagina(Router $router) {
        $router->render('clientes/index', []);
    }

    public static function buscarClientes() {
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
        
        if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['telefono'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        try {
            // Validar y limpiar datos
            $_POST['nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre']))));
            $_POST['apellido'] = ucwords(strtolower(trim(htmlspecialchars($_POST['apellido']))));
            $_POST['cedula'] = trim(htmlspecialchars($_POST['cedula'] ?? ''));
            $_POST['nit'] = trim(htmlspecialchars($_POST['nit'] ?? ''));
            $_POST['email'] = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $_POST['telefono'] = filter_var($_POST['telefono'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['direccion'] = trim(htmlspecialchars($_POST['direccion'] ?? ''));

            // Validaciones
            if (strlen($_POST['nombre']) < 2) {
                throw new Exception('El nombre es inválido');
            }
            if (strlen($_POST['apellido']) < 2) {
                throw new Exception('El apellido es inválido');
            }
            if (strlen($_POST['telefono']) < 8) {
                throw new Exception('El teléfono debe tener al menos 8 dígitos');
            }
            if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El email es inválido');
            }

            // Verificar si la cédula ya existe (si se proporciona)
            if (!empty($_POST['cedula'])) {
                $query = "SELECT * FROM clientes WHERE cedula = '" . ActiveRecord::$db->escape_string($_POST['cedula']) . "'";
                $existe = Clientes::consultarSQL($query);
                
                if (!empty($existe)) {
                    throw new Exception('La cédula ya está registrada');
                }
            }

            $cliente = new Clientes([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'cedula' => $_POST['cedula'],
                'nit' => $_POST['nit'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion']
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

            // Verificar cédula única (excluyendo el cliente actual)
            if (!empty($_POST['cedula'])) {
                $query = "SELECT * FROM clientes WHERE cedula = '" . ActiveRecord::$db->escape_string($_POST['cedula']) . "' AND id_cliente != " . $_POST['id_cliente'];
                $existe = Clientes::consultarSQL($query);
                
                if (!empty($existe)) {
                    throw new Exception('La cédula ya está registrada');
                }
            }

            // Actualizar datos
            $cliente->nombre = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre']))));
            $cliente->apellido = ucwords(strtolower(trim(htmlspecialchars($_POST['apellido']))));
            $cliente->cedula = trim(htmlspecialchars($_POST['cedula'] ?? ''));
            $cliente->nit = trim(htmlspecialchars($_POST['nit'] ?? ''));
            $cliente->email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $cliente->telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_NUMBER_INT);
            $cliente->direccion = trim(htmlspecialchars($_POST['direccion'] ?? ''));

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
    
    // ✅ CAMBIO: Usar $_GET en lugar de $_POST
    $id_cliente = $_GET['id_cliente'] ?? '';
    
    if (empty($id_cliente)) {
        http_response_code(400);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'ID de cliente requerido'
        ]);
        return;
    }

    try {
        $cliente = Clientes::find($id_cliente);
        
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
    public static function buscarClientePorCedula() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['cedula'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Cédula requerida'
            ]);
            return;
        }

        try {
            $query = "SELECT * FROM clientes WHERE cedula = '" . ActiveRecord::$db->escape_string($_POST['cedula']) . "'";
            $cliente = Clientes::consultarSQL($query);
            
            if (!empty($cliente)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Cliente encontrado',
                    'data' => $cliente[0]
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Cliente no encontrado',
                    'data' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarClientesPorNombre() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre requerido'
            ]);
            return;
        }

        try {
            $nombre = ActiveRecord::$db->escape_string($_POST['nombre']);
            $query = "SELECT * FROM clientes 
                     WHERE nombre LIKE '%{$nombre}%' 
                     OR apellido LIKE '%{$nombre}%'
                     ORDER BY nombre ASC LIMIT 10";
            
            $clientes = Clientes::consultarSQL($query);
            
            if (!empty($clientes)) {
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

    public static function obtenerClientesRecientes() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $limite = $_POST['limite'] ?? 5;
            $query = "SELECT * FROM clientes ORDER BY fecha_creacion DESC LIMIT " . $limite;
            
            $clientes = Clientes::consultarSQL($query);
            
            if (!empty($clientes)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Clientes recientes encontrados',
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
                'mensaje' => 'Error al obtener clientes recientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}

?>