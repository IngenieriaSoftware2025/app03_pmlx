<?php 

namespace Controllers;

use Exception;
use Model\Ventas;
use Model\Reparaciones;
use Model\Inventario;
use Model\Clientes;
use Model\HistorialVentas;
use Model\ActiveRecord;
use MVC\Router;

class DashboardController {

    public static function mostrarPagina(Router $router) {
        // Verificar autenticación
        AuthController::verificarAuth();
        
        $router->render('dashboard/index', []);
    }

    public static function obtenerDatos() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Verificar autenticación
            AuthController::verificarAuth();

            $datos = [
                'metricas' => self::obtenerMetricas(),
                'graficas' => self::obtenerDatosGraficas(),
                'stockBajo' => self::obtenerProductosStockBajo()
            ];

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Datos del dashboard obtenidos',
                'data' => $datos
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos del dashboard',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    private static function obtenerMetricas() {
        try {
            $fecha_hoy = date('Y-m-d');
            $mes_actual = date('m');
            $año_actual = date('Y');

            // Ventas del día
            $query_ventas_dia = "SELECT 
                                    COALESCE(SUM(total), 0) as total,
                                    COUNT(*) as cantidad
                                 FROM ventas 
                                 WHERE DATE(fecha_venta) = '" . $fecha_hoy . "' 
                                 AND estado_venta = 'Completada'";
            
            $resultado_ventas = ActiveRecord::$db->query($query_ventas_dia);
            $ventas_dia = $resultado_ventas->fetch_assoc();

            // Reparaciones pendientes
            $query_reparaciones = "SELECT COUNT(*) as total 
                                  FROM reparaciones 
                                  WHERE estado_reparacion IN ('Recibido', 'En Proceso')";
            
            $resultado_reparaciones = ActiveRecord::$db->query($query_reparaciones);
            $reparaciones_pendientes = $resultado_reparaciones->fetch_assoc()['total'];

            // Stock bajo (5 o menos unidades)
            $query_stock_bajo = "SELECT COUNT(*) as total 
                                FROM inventario 
                                WHERE stock <= 5";
            
            $resultado_stock = ActiveRecord::$db->query($query_stock_bajo);
            $stock_bajo = $resultado_stock->fetch_assoc()['total'];

            // Total clientes
            $query_clientes = "SELECT COUNT(*) as total FROM clientes";
            $resultado_clientes = ActiveRecord::$db->query($query_clientes);
            $total_clientes = $resultado_clientes->fetch_assoc()['total'];

            // Ventas del mes
            $query_ventas_mes = "SELECT COALESCE(SUM(total), 0) as total 
                                FROM ventas 
                                WHERE MONTH(fecha_venta) = " . $mes_actual . " 
                                AND YEAR(fecha_venta) = " . $año_actual . "
                                AND estado_venta = 'Completada'";
            
            $resultado_ventas_mes = ActiveRecord::$db->query($query_ventas_mes);
            $ventas_mes = $resultado_ventas_mes->fetch_assoc()['total'];

            // Reparaciones del mes
            $query_reparaciones_mes = "SELECT COALESCE(SUM(costo_reparacion), 0) as total 
                                      FROM reparaciones 
                                      WHERE MONTH(fecha_ingreso) = " . $mes_actual . " 
                                      AND YEAR(fecha_ingreso) = " . $año_actual . "
                                      AND estado_reparacion = 'Entregado'";
            
            $resultado_reparaciones_mes = ActiveRecord::$db->query($query_reparaciones_mes);
            $reparaciones_mes = $resultado_reparaciones_mes->fetch_assoc()['total'];

            return [
                'ventasDelDia' => floatval($ventas_dia['total']),
                'cantidadVentas' => intval($ventas_dia['cantidad']),
                'reparacionesPendientes' => intval($reparaciones_pendientes),
                'stockBajo' => intval($stock_bajo),
                'totalClientes' => intval($total_clientes),
                'ventasMes' => floatval($ventas_mes),
                'reparacionesMes' => floatval($reparaciones_mes)
            ];

        } catch (Exception $e) {
            throw new Exception('Error al obtener métricas: ' . $e->getMessage());
        }
    }

    private static function obtenerDatosGraficas() {
        try {
            return [
                'ventasDiarias' => self::obtenerVentasDiarias(),
                'reparaciones' => self::obtenerEstadosReparaciones(),
                'ingresos' => self::obtenerIngresosMensuales(),
                'topProductos' => self::obtenerTopProductos(),
                'stockMarcas' => self::obtenerStockPorMarcas()
            ];

        } catch (Exception $e) {
            throw new Exception('Error al obtener datos de gráficas: ' . $e->getMessage());
        }
    }

    private static function obtenerVentasDiarias() {
        try {
            $query = "SELECT 
                        DATE(fecha_venta) as fecha,
                        COALESCE(SUM(total), 0) as total
                      FROM ventas 
                      WHERE fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                      AND estado_venta = 'Completada'
                      GROUP BY DATE(fecha_venta)
                      ORDER BY fecha ASC";
            
            $resultado = ActiveRecord::$db->query($query);
            
            $fechas = [];
            $valores = [];
            
            while($row = $resultado->fetch_assoc()) {
                $fechas[] = date('d/m', strtotime($row['fecha']));
                $valores[] = floatval($row['total']);
            }

            return [
                'fechas' => $fechas,
                'valores' => $valores
            ];

        } catch (Exception $e) {
            return ['fechas' => [], 'valores' => []];
        }
    }

    private static function obtenerEstadosReparaciones() {
        try {
            $query = "SELECT 
                        COUNT(CASE WHEN estado_reparacion = 'Recibido' THEN 1 END) as recibidas,
                        COUNT(CASE WHEN estado_reparacion = 'En Proceso' THEN 1 END) as en_proceso,
                        COUNT(CASE WHEN estado_reparacion = 'Terminado' THEN 1 END) as terminadas,
                        COUNT(CASE WHEN estado_reparacion = 'Entregado' THEN 1 END) as entregadas
                      FROM reparaciones 
                      WHERE fecha_ingreso >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            
            $resultado = ActiveRecord::$db->query($query);
            $row = $resultado->fetch_assoc();

            return [
                intval($row['recibidas']),
                intval($row['en_proceso']),
                intval($row['terminadas']),
                intval($row['entregadas'])
            ];

        } catch (Exception $e) {
            return [0, 0, 0, 0];
        }
    }

    private static function obtenerIngresosMensuales() {
        try {
            $query = "SELECT 
                        DATE_FORMAT(fecha_operacion, '%Y-%m') as mes,
                        SUM(CASE WHEN tipo_operacion = 'Venta' THEN monto ELSE 0 END) as ventas,
                        SUM(CASE WHEN tipo_operacion = 'Reparacion' THEN monto ELSE 0 END) as reparaciones
                      FROM historial_ventas 
                      WHERE fecha_operacion >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                      GROUP BY DATE_FORMAT(fecha_operacion, '%Y-%m')
                      ORDER BY mes ASC";
            
            $resultado = ActiveRecord::$db->query($query);
            
            $meses = [];
            $ventas = [];
            $reparaciones = [];
            
            while($row = $resultado->fetch_assoc()) {
                $meses[] = date('M Y', strtotime($row['mes'] . '-01'));
                $ventas[] = floatval($row['ventas']);
                $reparaciones[] = floatval($row['reparaciones']);
            }

            return [
                'meses' => $meses,
                'ventas' => $ventas,
                'reparaciones' => $reparaciones
            ];

        } catch (Exception $e) {
            return ['meses' => [], 'ventas' => [], 'reparaciones' => []];
        }
    }

    private static function obtenerTopProductos() {
        try {
            $query = "SELECT 
                        CONCAT(i.modelo, ' (', m.nombre_marca, ')') as producto,
                        SUM(dv.cantidad) as total_vendido
                      FROM detalle_ventas dv
                      JOIN inventario i ON dv.id_inventario = i.id_inventario
                      JOIN marcas m ON i.id_marca = m.id_marca
                      JOIN ventas v ON dv.id_venta = v.id_venta
                      WHERE v.fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                      AND v.estado_venta = 'Completada'
                      GROUP BY dv.id_inventario, i.modelo, m.nombre_marca
                      ORDER BY total_vendido DESC
                      LIMIT 5";
            
            $resultado = ActiveRecord::$db->query($query);
            
            $nombres = [];
            $cantidades = [];
            
            while($row = $resultado->fetch_assoc()) {
                $nombres[] = $row['producto'];
                $cantidades[] = intval($row['total_vendido']);
            }

            return [
                'nombres' => $nombres,
                'cantidades' => $cantidades
            ];

        } catch (Exception $e) {
            return ['nombres' => [], 'cantidades' => []];
        }
    }

    private static function obtenerStockPorMarcas() {
        try {
            $query = "SELECT 
                        m.nombre_marca,
                        SUM(i.stock) as total_stock
                      FROM marcas m
                      LEFT JOIN inventario i ON m.id_marca = i.id_marca
                      WHERE m.activo = 'S'
                      GROUP BY m.id_marca, m.nombre_marca
                      ORDER BY total_stock DESC";
            
            $resultado = ActiveRecord::$db->query($query);
            
            $datos = [];
            
            while($row = $resultado->fetch_assoc()) {
                $datos[] = intval($row['total_stock']);
            }

            return $datos;

        } catch (Exception $e) {
            return [0, 0, 0, 0, 0, 0]; // 6 marcas por defecto
        }
    }

    private static function obtenerProductosStockBajo() {
        try {
            $query = "SELECT 
                        i.id_inventario,
                        i.modelo,
                        m.nombre_marca as marca,
                        i.stock,
                        i.precio_venta
                      FROM inventario i
                      JOIN marcas m ON i.id_marca = m.id_marca
                      WHERE i.stock <= 5
                      ORDER BY i.stock ASC
                      LIMIT 10";
            
            $resultado = ActiveRecord::$db->query($query);
            
            $productos = [];
            
            while($row = $resultado->fetch_assoc()) {
                $productos[] = [
                    'id_inventario' => intval($row['id_inventario']),
                    'modelo' => $row['modelo'],
                    'marca' => $row['marca'],
                    'stock' => intval($row['stock']),
                    'precio_venta' => floatval($row['precio_venta'])
                ];
            }

            return $productos;

        } catch (Exception $e) {
            return [];
        }
    }

    public static function obtenerResumenVentas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            AuthController::verificarAuth();

            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            
            $query = "SELECT 
                        COUNT(*) as total_ventas,
                        COALESCE(SUM(total), 0) as monto_total,
                        COALESCE(AVG(total), 0) as promedio_venta
                      FROM ventas 
                      WHERE DATE(fecha_venta) = '" . $fecha . "'
                      AND estado_venta = 'Completada'";
            
            $resultado = ActiveRecord::$db->query($query);
            $resumen = $resultado->fetch_assoc();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Resumen obtenido',
                'data' => [
                    'total_ventas' => intval($resumen['total_ventas']),
                    'monto_total' => floatval($resumen['monto_total']),
                    'promedio_venta' => floatval($resumen['promedio_venta'])
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener resumen de ventas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerAlertasStock() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            AuthController::verificarAuth();

            $limite = $_POST['limite'] ?? 5;
            
            $query = "SELECT 
                        COUNT(*) as productos_criticos,
                        COALESCE(SUM(precio_venta * stock), 0) as valor_stock_bajo
                      FROM inventario 
                      WHERE stock <= " . $limite;
            
            $resultado = ActiveRecord::$db->query($query);
            $alertas = $resultado->fetch_assoc();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Alertas de stock obtenidas',
                'data' => [
                    'productos_criticos' => intval($alertas['productos_criticos']),
                    'valor_stock_bajo' => floatval($alertas['valor_stock_bajo'])
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener alertas de stock',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerReparacionesUrgentes() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            AuthController::verificarAuth();

            // Reparaciones vencidas
            $query_vencidas = "SELECT COUNT(*) as total 
                              FROM reparaciones 
                              WHERE fecha_entrega_estimada < CURDATE() 
                              AND estado_reparacion NOT IN ('Entregado', 'Cancelado')";
            
            $resultado_vencidas = ActiveRecord::$db->query($query_vencidas);
            $vencidas = $resultado_vencidas->fetch_assoc()['total'];

            // Reparaciones para hoy
            $query_hoy = "SELECT COUNT(*) as total 
                         FROM reparaciones 
                         WHERE DATE(fecha_entrega_estimada) = CURDATE() 
                         AND estado_reparacion NOT IN ('Entregado', 'Cancelado')";
            
            $resultado_hoy = ActiveRecord::$db->query($query_hoy);
            $para_hoy = $resultado_hoy->fetch_assoc()['total'];

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reparaciones urgentes obtenidas',
                'data' => [
                    'vencidas' => intval($vencidas),
                    'para_hoy' => intval($para_hoy),
                    'total_urgentes' => intval($vencidas + $para_hoy)
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener reparaciones urgentes',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}

?>