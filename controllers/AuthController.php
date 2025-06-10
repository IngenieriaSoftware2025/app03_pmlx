<?php 

namespace Controllers;

use Exception;
use Model\Usuarios;
use Model\ActiveRecord;
use MVC\Router;

class AuthController {

    public static function mostrarLogin(Router $router) {
        // Verificar si ya está logueado
        session_start();
        if (isset($_SESSION['usuario'])) {
            header('Location: /dashboard');
            exit;
        }
        
        $router->render('auth/login', []);
    }

    public static function iniciarSesion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['email']) || empty($_POST['password'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Email y contraseña son obligatorios'
            ]);
            return;
        }

        try {
            // Limpiar y validar datos
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = trim($_POST['password']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email no válido');
            }

            if (strlen($password) < 6) {
                throw new Exception('Contraseña debe tener al menos 6 caracteres');
            }

            // Buscar usuario por email con rol
            $query = "SELECT u.*, r.nombre_rol 
                     FROM usuarios u 
                     JOIN roles r ON u.id_rol = r.id_rol 
                     WHERE u.email = '" . ActiveRecord::$db->escape_string($email) . "' 
                     AND u.activo = 'S'";
            
            $resultado = Usuarios::consultarSQL($query);
            
            if (empty($resultado)) {
                throw new Exception('Usuario no encontrado o inactivo');
            }

            $usuario = $resultado[0];

            // Verificar contraseña
            if (!password_verify($password, $usuario->password)) {
                // Registrar intento fallido
                self::registrarIntentoFallido($email);
                throw new Exception('Contraseña incorrecta');
            }

            // Iniciar sesión
            session_start();
            session_regenerate_id(true);

            // Guardar datos en sesión
            $_SESSION['usuario'] = [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'id_rol' => $usuario->id_rol,
                'nombre_rol' => $usuario->nombre_rol,
                'ultimo_acceso' => date('Y-m-d H:i:s')
            ];

            // Actualizar último acceso en base de datos
            self::actualizarUltimoAcceso($usuario->id_usuario);

            // Recordar usuario si se solicitó
            if (isset($_POST['recordar']) && $_POST['recordar'] === 'true') {
                $token = bin2hex(random_bytes(32));
                setcookie('recordar_token', $token, time() + (86400 * 30), '/'); // 30 días
                
                // Guardar token en base de datos (opcional)
                self::guardarTokenRecordar($usuario->id_usuario, $token);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inicio de sesión exitoso',
                'usuario' => [
                    'nombre' => $usuario->nombre . ' ' . $usuario->apellido,
                    'rol' => $usuario->nombre_rol,
                    'email' => $usuario->email
                ],
                'redirect' => '/dashboard'
            ]);

        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function cerrarSesion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            session_start();
            
            // Limpiar cookies de recordar
            if (isset($_COOKIE['recordar_token'])) {
                setcookie('recordar_token', '', time() - 3600, '/');
                
                // Eliminar token de base de datos
                if (isset($_SESSION['usuario']['id_usuario'])) {
                    self::eliminarTokenRecordar($_SESSION['usuario']['id_usuario']);
                }
            }

            // Destruir sesión
            session_unset();
            session_destroy();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Sesión cerrada exitosamente',
                'redirect' => '/login'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al cerrar sesión',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function verificarSesion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            session_start();
            
            if (!isset($_SESSION['usuario'])) {
                // Verificar cookie de recordar
                if (isset($_COOKIE['recordar_token'])) {
                    $token_valido = self::verificarTokenRecordar($_COOKIE['recordar_token']);
                    if ($token_valido) {
                        http_response_code(200);
                        echo json_encode([
                            'codigo' => 1,
                            'sesion_activa' => true,
                            'usuario' => $_SESSION['usuario']
                        ]);
                        return;
                    }
                }

                http_response_code(401);
                echo json_encode([
                    'codigo' => 0,
                    'sesion_activa' => false,
                    'mensaje' => 'Sesión expirada'
                ]);
                return;
            }

            // Verificar si la sesión no ha expirado (opcional: 8 horas)
            $tiempo_expiracion = 8 * 60 * 60; // 8 horas en segundos
            if (isset($_SESSION['ultimo_actividad']) && 
                (time() - $_SESSION['ultimo_actividad']) > $tiempo_expiracion) {
                
                session_unset();
                session_destroy();
                
                http_response_code(401);
                echo json_encode([
                    'codigo' => 0,
                    'sesion_activa' => false,
                    'mensaje' => 'Sesión expirada por inactividad'
                ]);
                return;
            }

            // Actualizar última actividad
            $_SESSION['ultimo_actividad'] = time();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'sesion_activa' => true,
                'usuario' => $_SESSION['usuario']
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar sesión',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function cambiarPassword() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['password_actual']) || empty($_POST['password_nueva'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Contraseña actual y nueva son obligatorias'
            ]);
            return;
        }

        try {
            session_start();
            
            if (!isset($_SESSION['usuario'])) {
                throw new Exception('Sesión no válida');
            }

            $usuario = Usuarios::find($_SESSION['usuario']['id_usuario']);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }

            // Verificar contraseña actual
            if (!password_verify($_POST['password_actual'], $usuario->password)) {
                throw new Exception('Contraseña actual incorrecta');
            }

            // Validar nueva contraseña
            if (strlen($_POST['password_nueva']) < 6) {
                throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
            }

            if ($_POST['password_nueva'] !== $_POST['confirmar_password']) {
                throw new Exception('Las contraseñas no coinciden');
            }

            // Actualizar contraseña
            $usuario->password = password_hash($_POST['password_nueva'], PASSWORD_BCRYPT);
            $resultado = $usuario->actualizar();

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Contraseña cambiada exitosamente'
                ]);
            } else {
                throw new Exception('Error al actualizar la contraseña');
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerPerfilUsuario() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            session_start();
            
            if (!isset($_SESSION['usuario'])) {
                throw new Exception('Sesión no válida');
            }

            $usuario = Usuarios::find($_SESSION['usuario']['id_usuario']);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Perfil obtenido',
                'usuario' => [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'email' => $usuario->email,
                    'nombre_rol' => $_SESSION['usuario']['nombre_rol'],
                    'fecha_creacion' => $usuario->fecha_creacion
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // Métodos auxiliares privados
    private static function registrarIntentoFallido($email) {
        try {
            $query = "INSERT INTO intentos_login (email, fecha_intento, ip_address) 
                     VALUES ('" . ActiveRecord::$db->escape_string($email) . "', 
                             NOW(), 
                             '" . ActiveRecord::$db->escape_string($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "')";
            ActiveRecord::$db->query($query);
        } catch (Exception $e) {
            // No hacer nada si la tabla no existe
        }
    }

    private static function actualizarUltimoAcceso($id_usuario) {
        try {
            $query = "UPDATE usuarios 
                     SET fecha_actualizacion = NOW() 
                     WHERE id_usuario = " . $id_usuario;
            ActiveRecord::$db->query($query);
        } catch (Exception $e) {
            // No es crítico si falla
        }
    }

    private static function guardarTokenRecordar($id_usuario, $token) {
        try {
            $hash_token = hash('sha256', $token);
            $query = "INSERT INTO tokens_recordar (id_usuario, token_hash, fecha_expiracion) 
                     VALUES (" . $id_usuario . ", 
                             '" . $hash_token . "', 
                             DATE_ADD(NOW(), INTERVAL 30 DAY))
                     ON DUPLICATE KEY UPDATE 
                     token_hash = '" . $hash_token . "', 
                     fecha_expiracion = DATE_ADD(NOW(), INTERVAL 30 DAY)";
            ActiveRecord::$db->query($query);
        } catch (Exception $e) {
            // No es crítico si falla
        }
    }

    private static function eliminarTokenRecordar($id_usuario) {
        try {
            $query = "DELETE FROM tokens_recordar WHERE id_usuario = " . $id_usuario;
            ActiveRecord::$db->query($query);
        } catch (Exception $e) {
            // No es crítico si falla
        }
    }

    private static function verificarTokenRecordar($token) {
        try {
            $hash_token = hash('sha256', $token);
            $query = "SELECT u.*, r.nombre_rol 
                     FROM tokens_recordar t
                     JOIN usuarios u ON t.id_usuario = u.id_usuario
                     JOIN roles r ON u.id_rol = r.id_rol
                     WHERE t.token_hash = '" . $hash_token . "' 
                     AND t.fecha_expiracion > NOW()
                     AND u.activo = 'S'";
            
            $resultado = Usuarios::consultarSQL($query);
            
            if (!empty($resultado)) {
                $usuario = $resultado[0];
                
                // Recrear sesión
                $_SESSION['usuario'] = [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'email' => $usuario->email,
                    'id_rol' => $usuario->id_rol,
                    'nombre_rol' => $usuario->nombre_rol,
                    'ultimo_acceso' => date('Y-m-d H:i:s')
                ];
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Middleware para verificar autenticación
    public static function verificarAuth() {
        session_start();
        
        if (!isset($_SESSION['usuario'])) {
            header('Location: /login');
            exit;
        }
        
        return $_SESSION['usuario'];
    }

    // Middleware para verificar rol
    public static function verificarRol($roles_permitidos = []) {
        $usuario = self::verificarAuth();
        
        if (!empty($roles_permitidos) && !in_array($usuario['id_rol'], $roles_permitidos)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'No tienes permisos para acceder a este recurso'
            ]);
            exit;
        }
        
        return $usuario;
    }
}

?>