<?php 

namespace Controllers;

use Exception;
use Model\Usuarios;
use Model\Roles;
use Model\ActiveRecord;
use MVC\Router;

class UsuariosController {

    public static function mostrarPagina(Router $router) {
        $router->render('usuarios/index', []);
    }

    public static function buscarUsuarios() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    try {
        // ✅ AÑADIR JOIN con la tabla roles
        $query = "SELECT u.*, r.nombre_rol 
                 FROM usuarios u 
                 LEFT JOIN roles r ON u.id_rol = r.id_rol 
                 ORDER BY u.fecha_creacion DESC";
        
        $usuarios = Usuarios::consultarSQL($query);
        
        if ($usuarios) {
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios encontrados',
                'data' => $usuarios
            ]);
        } else {
            http_response_code(200);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'No se encontraron usuarios',
                'data' => []
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error al buscar usuarios',
            'detalle' => $e->getMessage()
        ]);
    }
}

  public static function guardarUsuario() {
    // ✅ DEBUG: Verificar si el método se está ejecutando
    error_log("=== INICIANDO guardarUsuario ===");
    error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
    error_log("POST data: " . print_r($_POST, true));
    
    // Limpiar buffer de salida
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Headers correctos
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // Manejo de preflight OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    try {
        // ✅ RESPUESTA DE PRUEBA INMEDIATA
        echo json_encode([
            'codigo' => 1,
            'mensaje' => 'Conexión exitosa - Método ejecutándose correctamente',
            'debug' => [
                'post_data' => $_POST,
                'method' => $_SERVER['REQUEST_METHOD']
            ]
        ]);
        exit;
        
        // TODO: Aquí irá tu lógica real después de confirmar que funciona
        
    } catch (Exception $e) {
        error_log("Error en guardarUsuario: " . $e->getMessage());
        
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error: ' . $e->getMessage()
        ]);
        exit;
    }
}

    public static function modificarUsuario() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_usuario'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de usuario requerido'
            ]);
            return;
        }

        try {
            $usuario = Usuarios::find($_POST['id_usuario']);
            
            if (!$usuario) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            // Validar email único (excluyendo el usuario actual)
            if (!empty($_POST['email'])) {
                $query = "SELECT * FROM usuarios WHERE email = '" . ActiveRecord::$db->escape_string($_POST['email']) . "' AND id_usuario != " . $_POST['id_usuario'];
                $existe = Usuarios::consultarSQL($query);
                
                if (!empty($existe)) {
                    throw new Exception('El email ya está registrado');
                }
            }

            // Actualizar datos
            $usuario->nombre = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre']))));
            $usuario->apellido = ucwords(strtolower(trim(htmlspecialchars($_POST['apellido']))));
            $usuario->email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $usuario->id_rol = filter_var($_POST['id_rol'], FILTER_SANITIZE_NUMBER_INT);
            $usuario->activo = $_POST['activo'] ?? 'S';

            // Actualizar contraseña solo si se proporciona
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    throw new Exception('La contraseña debe tener al menos 6 caracteres');
                }
                $usuario->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            $resultado = $usuario->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario modificado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el usuario'
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

    public static function eliminarUsuario() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_usuario'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de usuario requerido'
            ]);
            return;
        }

        try {
            $usuario = Usuarios::find($_POST['id_usuario']);
            
            if (!$usuario) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            $resultado = $usuario->eliminar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario eliminado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar el usuario'
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

    public static function cambiarEstadoUsuario() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_usuario'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de usuario requerido'
            ]);
            return;
        }

        try {
            $usuario = Usuarios::find($_POST['id_usuario']);
            
            if (!$usuario) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            // Cambiar estado
            $usuario->activo = ($usuario->activo === 'S') ? 'N' : 'S';
            $resultado = $usuario->actualizar();
            
            if ($resultado) {
                $estado = ($usuario->activo === 'S') ? 'activado' : 'desactivado';
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => "Usuario {$estado} exitosamente",
                    'nuevo_estado' => $usuario->activo
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al cambiar el estado del usuario'
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

    public static function buscarRoles() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $roles = Roles::all();
            
            if ($roles) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Roles encontrados',
                    'data' => $roles
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron roles',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar roles',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}

?>