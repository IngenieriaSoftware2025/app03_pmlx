<?php 

namespace Controllers;

use Exception;
use Model\Reparaciones;
use Model\Clientes;
use Model\Usuarios;
use Model\HistorialVentas;
use Model\ActiveRecord;
use MVC\Router;

class ReparacionesController {

    public static function mostrarPagina(Router $router) {
        $router->render('reparaciones/index', []);
    }

    public static function buscarReparaciones() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Consulta con JOIN para obtener información completa
            $query = "SELECT r.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             c.telefono as telefono_cliente,
                             CONCAT(u.nombre, ' ', u.apellido) as nombre_trabajador
                      FROM reparaciones r 
                      JOIN clientes c ON r.id_cliente = c.id_cliente
                      LEFT JOIN usuarios u ON r.id_trabajador = u.id_usuario
                      ORDER BY r.fecha_ingreso DESC";
            
            $reparaciones = Reparaciones::consultarSQL($query);
            
            if ($reparaciones) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparaciones encontradas',
                    'data' => $reparaciones
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron reparaciones',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar reparaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarReparacion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_cliente']) || empty($_POST['tipo_celular']) || empty($_POST['marca_celular']) || empty($_POST['motivo_ingreso'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        try {
            // Validar y limpiar datos
            $_POST['id_cliente'] = filter_var($_POST['id_cliente'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['tipo_celular'] = trim(htmlspecialchars($_POST['tipo_celular']));
            $_POST['marca_celular'] = trim(htmlspecialchars($_POST['marca_celular']));
            $_POST['modelo_celular'] = trim(htmlspecialchars($_POST['modelo_celular'] ?? ''));
            $_POST['imei'] = trim(htmlspecialchars($_POST['imei'] ?? ''));
            $_POST['motivo_ingreso'] = trim(htmlspecialchars($_POST['motivo_ingreso']));
            $_POST['descripcion_problema'] = trim(htmlspecialchars($_POST['descripcion_problema'] ?? ''));
            $_POST['fecha_ingreso'] = $_POST['fecha_ingreso'] ?? date('Y-m-d H:i:s');
            $_POST['fecha_entrega_estimada'] = $_POST['fecha_entrega_estimada'] ?? null;
            $_POST['id_trabajador'] = !empty($_POST['id_trabajador']) ? filter_var($_POST['id_trabajador'], FILTER_SANITIZE_NUMBER_INT) : null;
            $_POST['estado_reparacion'] = $_POST['estado_reparacion'] ?? 'Recibido';
            $_POST['costo_reparacion'] = filter_var($_POST['costo_reparacion'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $_POST['observaciones'] = trim(htmlspecialchars($_POST['observaciones'] ?? ''));

            // Validaciones
            if (strlen($_POST['tipo_celular']) < 2) {
                throw new Exception('El tipo de celular es inválido');
            }
            if (strlen($_POST['marca_celular']) < 2) {
                throw new Exception('La marca del celular es inválida');
            }
            if (strlen($_POST['motivo_ingreso']) < 5) {
                throw new Exception('El motivo de ingreso debe ser más específico');
            }
            if ($_POST['costo_reparacion'] < 0) {
                throw new Exception('El costo no puede ser negativo');
            }

            // Si se asigna trabajador, cambiar estado automáticamente
            if (!empty($_POST['id_trabajador']) && $_POST['estado_reparacion'] === 'Recibido') {
                $_POST['estado_reparacion'] = 'En Proceso';
            }

            $reparacion = new Reparaciones([
                'id_cliente' => $_POST['id_cliente'],
                'tipo_celular' => $_POST['tipo_celular'],
                'marca_celular' => $_POST['marca_celular'],
                'modelo_celular' => $_POST['modelo_celular'],
                'imei' => $_POST['imei'],
                'motivo_ingreso' => $_POST['motivo_ingreso'],
                'descripcion_problema' => $_POST['descripcion_problema'],
                'fecha_ingreso' => $_POST['fecha_ingreso'],
                'fecha_entrega_estimada' => $_POST['fecha_entrega_estimada'],
                'id_trabajador' => $_POST['id_trabajador'],
                'estado_reparacion' => $_POST['estado_reparacion'],
                'costo_reparacion' => $_POST['costo_reparacion'],
                'observaciones' => $_POST['observaciones']
            ]);

            $resultado = $reparacion->crear();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparación guardada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar la reparación'
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

    public static function modificarReparacion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_reparacion'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación requerido'
            ]);
            return;
        }

        try {
            $reparacion = Reparaciones::find($_POST['id_reparacion']);
            
            if (!$reparacion) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Reparación no encontrada'
                ]);
                return;
            }

            // Actualizar datos
            $reparacion->id_cliente = filter_var($_POST['id_cliente'], FILTER_SANITIZE_NUMBER_INT);
            $reparacion->tipo_celular = trim(htmlspecialchars($_POST['tipo_celular']));
            $reparacion->marca_celular = trim(htmlspecialchars($_POST['marca_celular']));
            $reparacion->modelo_celular = trim(htmlspecialchars($_POST['modelo_celular'] ?? ''));
            $reparacion->imei = trim(htmlspecialchars($_POST['imei'] ?? ''));
            $reparacion->motivo_ingreso = trim(htmlspecialchars($_POST['motivo_ingreso']));
            $reparacion->descripcion_problema = trim(htmlspecialchars($_POST['descripcion_problema'] ?? ''));
            $reparacion->fecha_entrega_estimada = $_POST['fecha_entrega_estimada'] ?? null;
            $reparacion->id_trabajador = !empty($_POST['id_trabajador']) ? filter_var($_POST['id_trabajador'], FILTER_SANITIZE_NUMBER_INT) : null;
            $reparacion->estado_reparacion = $_POST['estado_reparacion'] ?? $reparacion->estado_reparacion;
            $reparacion->costo_reparacion = filter_var($_POST['costo_reparacion'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $reparacion->observaciones = trim(htmlspecialchars($_POST['observaciones'] ?? ''));

            // Si se marca como entregado, guardar fecha de entrega real
            if ($reparacion->estado_reparacion === 'Entregado' && empty($reparacion->fecha_entrega_real)) {
                $reparacion->fecha_entrega_real = date('Y-m-d H:i:s');
            }

            $resultado = $reparacion->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparación modificada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la reparación'
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

    public static function eliminarReparacion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_reparacion'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación requerido'
            ]);
            return;
        }

        try {
            $reparacion = Reparaciones::find($_POST['id_reparacion']);
            
            if (!$reparacion) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Reparación no encontrada'
                ]);
                return;
            }

            // Solo permitir eliminar si está en estado "Recibido" o "Cancelado"
            if (!in_array($reparacion->estado_reparacion, ['Recibido', 'Cancelado'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar una reparación en proceso o terminada'
                ]);
                return;
            }

            $resultado = $reparacion->eliminar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparación eliminada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar la reparación'
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

    public static function cambiarEstadoReparacion() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_reparacion']) || empty($_POST['estado'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación y estado requeridos'
            ]);
            return;
        }

        try {
            $reparacion = Reparaciones::find($_POST['id_reparacion']);
            
            if (!$reparacion) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Reparación no encontrada'
                ]);
                return;
            }

            $estados_validos = ['Recibido', 'En Proceso', 'Terminado', 'Entregado', 'Cancelado'];
            if (!in_array($_POST['estado'], $estados_validos)) {
                throw new Exception('Estado no válido');
            }

            $reparacion->estado_reparacion = $_POST['estado'];
            
            // Si se marca como entregado, guardar fecha de entrega real
            if ($_POST['estado'] === 'Entregado') {
                $reparacion->fecha_entrega_real = date('Y-m-d H:i:s');
                
                // Registrar en historial si tiene costo
                if ($reparacion->costo_reparacion > 0) {
                    $historial = new HistorialVentas([
                        'id_venta' => $reparacion->id_reparacion,
                        'tipo_operacion' => 'Reparacion',
                        'fecha_operacion' => date('Y-m-d H:i:s'),
                        'monto' => $reparacion->costo_reparacion,
                        'id_usuario' => $reparacion->id_trabajador ?? 1,
                        'descripcion' => 'Entrega de reparación - ' . $reparacion->motivo_ingreso
                    ]);
                    $historial->crear();
                }
            }

            $resultado = $reparacion->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => "Reparación {$_POST['estado']} exitosamente"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al cambiar estado de reparación'
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

    public static function asignarTrabajador() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_reparacion']) || empty($_POST['id_trabajador'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación y trabajador requeridos'
            ]);
            return;
        }

        try {
            $reparacion = Reparaciones::find($_POST['id_reparacion']);
            
            if (!$reparacion) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Reparación no encontrada'
                ]);
                return;
            }

            $reparacion->id_trabajador = $_POST['id_trabajador'];
            
            // Si está en "Recibido", cambiar a "En Proceso"
            if ($reparacion->estado_reparacion === 'Recibido') {
                $reparacion->estado_reparacion = 'En Proceso';
            }

            $resultado = $reparacion->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Trabajador asignado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al asignar trabajador'
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

    public static function buscarReparacionesPorEstado() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['estado'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Estado requerido'
            ]);
            return;
        }

        try {
            $query = "SELECT r.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             c.telefono as telefono_cliente,
                             CONCAT(u.nombre, ' ', u.apellido) as nombre_trabajador
                      FROM reparaciones r 
                      JOIN clientes c ON r.id_cliente = c.id_cliente
                      LEFT JOIN usuarios u ON r.id_trabajador = u.id_usuario
                      WHERE r.estado_reparacion = '" . ActiveRecord::$db->escape_string($_POST['estado']) . "'
                      ORDER BY r.fecha_ingreso DESC";
            
            $reparaciones = Reparaciones::consultarSQL($query);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reparaciones encontradas',
                'data' => $reparaciones
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar reparaciones por estado',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarReparacionesVencidas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $query = "SELECT r.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             c.telefono as telefono_cliente,
                             CONCAT(u.nombre, ' ', u.apellido) as nombre_trabajador,
                             DATEDIFF(CURDATE(), r.fecha_entrega_estimada) as dias_vencidos
                      FROM reparaciones r 
                      JOIN clientes c ON r.id_cliente = c.id_cliente
                      LEFT JOIN usuarios u ON r.id_trabajador = u.id_usuario
                      WHERE r.fecha_entrega_estimada < CURDATE() 
                      AND r.estado_reparacion NOT IN ('Entregado', 'Cancelado')
                      ORDER BY r.fecha_entrega_estimada ASC";
            
            $reparaciones = Reparaciones::consultarSQL($query);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reparaciones vencidas encontradas',
                'data' => $reparaciones
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar reparaciones vencidas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarPorImei() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['imei'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'IMEI requerido'
            ]);
            return;
        }

        try {
            $query = "SELECT r.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             c.telefono as telefono_cliente
                      FROM reparaciones r 
                      JOIN clientes c ON r.id_cliente = c.id_cliente
                      WHERE r.imei = '" . ActiveRecord::$db->escape_string($_POST['imei']) . "'
                      ORDER BY r.fecha_ingreso DESC";
            
            $reparaciones = Reparaciones::consultarSQL($query);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reparaciones encontradas',
                'data' => $reparaciones
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar reparaciones por IMEI',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerEstadisticasReparaciones() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $mes = $_POST['mes'] ?? date('m');
            $año = $_POST['año'] ?? date('Y');
            
            $query = "SELECT 
                        COUNT(*) as total_reparaciones,
                        SUM(CASE WHEN estado_reparacion = 'Entregado' THEN costo_reparacion ELSE 0 END) as ingresos_total,
                        COUNT(CASE WHEN estado_reparacion = 'Recibido' THEN 1 END) as recibidas,
                        COUNT(CASE WHEN estado_reparacion = 'En Proceso' THEN 1 END) as en_proceso,
                        COUNT(CASE WHEN estado_reparacion = 'Terminado' THEN 1 END) as terminadas,
                        COUNT(CASE WHEN estado_reparacion = 'Entregado' THEN 1 END) as entregadas,
                        COUNT(CASE WHEN estado_reparacion = 'Cancelado' THEN 1 END) as canceladas
                      FROM reparaciones 
                      WHERE MONTH(fecha_ingreso) = " . $mes . " 
                      AND YEAR(fecha_ingreso) = " . $año;
            
            $resultado = ActiveRecord::$db->query($query);
            $estadisticas = $resultado->fetch_assoc();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas obtenidas',
                'data' => $estadisticas
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerTrabajadores() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Obtener usuarios activos que pueden ser trabajadores
            $query = "SELECT id_usuario, CONCAT(nombre, ' ', apellido) as nombre_completo 
                     FROM usuarios 
                     WHERE activo = 'S' 
                     ORDER BY nombre ASC";
            
            $trabajadores = Usuarios::consultarSQL($query);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Trabajadores encontrados',
                'data' => $trabajadores
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener trabajadores',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}

?>