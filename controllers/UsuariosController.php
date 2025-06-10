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
            // Consulta personalizada para obtener usuarios con roles
            $query = "SELECT u.*, r.nombre_rol 
                     FROM usuarios u 
                     JOIN roles r ON u.id_rol = r.id_rol 
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
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['id_rol'])) {
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
            $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $_POST['password'] = trim($_POST['password']);
            $_POST['id_rol'] = filter_var($_POST['id_rol'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['activo'] = $_POST['activo'] ?? 'S';

            // Validaciones
            if (strlen($_POST['nombre']) < 2) {
                throw new Exception('El nombre es inválido');
            }
            if (strlen($_POST['apellido']) < 2) {
                throw new Exception('El apellido es inválido');
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El email es inválido');
            }
            if (strlen($_POST['password']) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }

            // Verificar si el email ya existe
            $query = "SELECT * FROM usuarios WHERE email = '" . ActiveRecord::$db->escape_string($_POST['email']) . "'";
            $existe = Usuarios::consultarSQL($query);
            
            if (!empty($existe)) {
                throw new Exception('El email ya está registrado');
            }

            // Encriptar contraseña
            $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $usuario = new Usuarios([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'email' => $_POST['email'],
                'password' => $passwordHash,
                'id_rol' => $_POST['id_rol'],
                'activo' => $_POST['activo']
            ]);

            $resultado = $usuario->crear();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario guardado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar el usuario'
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