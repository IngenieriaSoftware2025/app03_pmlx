<?php 

namespace Controllers;

use Exception;
use Model\Ventas;
use Model\DetalleVentas;
use Model\Inventario;
use Model\Clientes;
use Model\Usuarios;
use Model\HistorialVentas;
use Model\ActiveRecord;
use MVC\Router;

class VentasController {

    public static function mostrarPagina(Router $router) {
        $router->render('ventas/index', []);
    }

    public static function buscarVentas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Consulta con JOIN para obtener información completa
            $query = "SELECT v.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             CONCAT(u.nombre, ' ', u.apellido) as nombre_usuario
                      FROM ventas v 
                      JOIN clientes c ON v.id_cliente = c.id_cliente
                      JOIN usuarios u ON v.id_usuario = u.id_usuario
                      ORDER BY v.fecha_venta DESC";
            
            $ventas = Ventas::consultarSQL($query);
            
            if ($ventas) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Ventas encontradas',
                    'data' => $ventas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron ventas',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar ventas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarVenta() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_cliente']) || empty($_POST['id_usuario']) || empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        try {
            // Iniciar transacción
            ActiveRecord::$db->autocommit(false);

            // Decodificar productos del JSON
            $productos = json_decode($_POST['productos'], true);
            if (!$productos || !is_array($productos)) {
                throw new Exception('Lista de productos inválida');
            }

            // Validar y calcular totales
            $subtotal = 0;
            foreach($productos as $producto) {
                if (empty($producto['id_inventario']) || empty($producto['cantidad']) || empty($producto['precio_unitario'])) {
                    throw new Exception('Datos de producto incompletos');
                }
                
                // Verificar stock disponible
                $inventario = Inventario::find($producto['id_inventario']);
                if (!$inventario) {
                    throw new Exception('Producto no encontrado: ' . $producto['id_inventario']);
                }
                
                if ($inventario->stock < $producto['cantidad']) {
                    throw new Exception('Stock insuficiente para: ' . $inventario->modelo);
                }
                
                $subtotal += $producto['cantidad'] * $producto['precio_unitario'];
            }

            // Calcular impuesto y total
            $porcentaje_impuesto = 12; // IVA Guatemala
            $impuesto = ($subtotal * $porcentaje_impuesto) / 100;
            $total = $subtotal + $impuesto;

            // Crear la venta
            $venta = new Ventas([
                'id_cliente' => $_POST['id_cliente'],
                'id_usuario' => $_POST['id_usuario'],
                'fecha_venta' => $_POST['fecha_venta'] ?? date('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $total,
                'estado_venta' => $_POST['estado_venta'] ?? 'Completada',
                'observaciones' => $_POST['observaciones'] ?? ''
            ]);

            $resultado_venta = $venta->crear();
            if (!$resultado_venta) {
                throw new Exception('Error al crear la venta');
            }

            // Obtener ID de la venta recién creada
            $id_venta = ActiveRecord::$db->insert_id;

            // Crear detalles de venta y actualizar stock
            foreach($productos as $producto) {
                // Crear detalle
                $detalle = new DetalleVentas([
                    'id_venta' => $id_venta,
                    'id_inventario' => $producto['id_inventario'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'subtotal' => $producto['cantidad'] * $producto['precio_unitario']
                ]);

                $resultado_detalle = $detalle->crear();
                if (!$resultado_detalle) {
                    throw new Exception('Error al crear detalle de venta');
                }

                // Actualizar stock
                $inventario = Inventario::find($producto['id_inventario']);
                $inventario->stock = $inventario->stock - $producto['cantidad'];
                $resultado_stock = $inventario->actualizar();
                
                if (!$resultado_stock) {
                    throw new Exception('Error al actualizar stock');
                }
            }

            // Registrar en historial
            $historial = new HistorialVentas([
                'id_venta' => $id_venta,
                'tipo_operacion' => 'Venta',
                'fecha_operacion' => date('Y-m-d H:i:s'),
                'monto' => $total,
                'id_usuario' => $_POST['id_usuario'],
                'descripcion' => 'Venta de celulares'
            ]);
            $historial->crear();

            // Confirmar transacción
            ActiveRecord::$db->commit();
            ActiveRecord::$db->autocommit(true);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta guardada exitosamente',
                'id_venta' => $id_venta,
                'total' => $total
            ]);

        } catch (Exception $e) {
            // Revertir transacción
            ActiveRecord::$db->rollback();
            ActiveRecord::$db->autocommit(true);

            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerDetalleVenta() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_venta'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de venta requerido'
            ]);
            return;
        }

        try {
            // Obtener información de la venta
            $query_venta = "SELECT v.*, 
                                   CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                                   c.telefono, c.email,
                                   CONCAT(u.nombre, ' ', u.apellido) as nombre_usuario
                            FROM ventas v 
                            JOIN clientes c ON v.id_cliente = c.id_cliente
                            JOIN usuarios u ON v.id_usuario = u.id_usuario
                            WHERE v.id_venta = " . $_POST['id_venta'];
            
            $venta = Ventas::consultarSQL($query_venta);
            
            if (empty($venta)) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            // Obtener detalles de la venta
            $query_detalles = "SELECT dv.*, 
                                      i.modelo, i.imei,
                                      m.nombre_marca
                               FROM detalle_ventas dv
                               JOIN inventario i ON dv.id_inventario = i.id_inventario
                               JOIN marcas m ON i.id_marca = m.id_marca
                               WHERE dv.id_venta = " . $_POST['id_venta'];
            
            $detalles = DetalleVentas::consultarSQL($query_detalles);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta obtenido',
                'venta' => $venta[0],
                'detalles' => $detalles
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener detalle de venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function cambiarEstadoVenta() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_venta']) || empty($_POST['estado'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de venta y estado requeridos'
            ]);
            return;
        }

        try {
            $venta = Ventas::find($_POST['id_venta']);
            
            if (!$venta) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            $estados_validos = ['Pendiente', 'Completada', 'Cancelada'];
            if (!in_array($_POST['estado'], $estados_validos)) {
                throw new Exception('Estado no válido');
            }

            $venta->estado_venta = $_POST['estado'];
            $resultado = $venta->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => "Venta {$_POST['estado']} exitosamente"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al cambiar estado de venta'
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

    public static function buscarVentasDelDia() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            
            $query = "SELECT v.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             CONCAT(u.nombre, ' ', u.apellido) as nombre_usuario
                      FROM ventas v 
                      JOIN clientes c ON v.id_cliente = c.id_cliente
                      JOIN usuarios u ON v.id_usuario = u.id_usuario
                      WHERE DATE(v.fecha_venta) = '" . $fecha . "'
                      ORDER BY v.fecha_venta DESC";
            
            $ventas = Ventas::consultarSQL($query);
            
            // Calcular total del día
            $query_total = "SELECT SUM(total) as total_dia 
                           FROM ventas 
                           WHERE DATE(fecha_venta) = '" . $fecha . "' 
                           AND estado_venta = 'Completada'";
            
            $resultado_total = ActiveRecord::$db->query($query_total);
            $total_dia = $resultado_total->fetch_assoc()['total_dia'] ?? 0;

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas del día encontradas',
                'ventas' => $ventas,
                'total_dia' => $total_dia,
                'cantidad_ventas' => count($ventas)
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar ventas del día',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarVentasPorRango() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Fechas de inicio y fin requeridas'
            ]);
            return;
        }

        try {
            $query = "SELECT v.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente,
                             CONCAT(u.nombre, ' ', u.apellido) as nombre_usuario
                      FROM ventas v 
                      JOIN clientes c ON v.id_cliente = c.id_cliente
                      JOIN usuarios u ON v.id_usuario = u.id_usuario
                      WHERE DATE(v.fecha_venta) BETWEEN '" . $_POST['fecha_inicio'] . "' AND '" . $_POST['fecha_fin'] . "'
                      ORDER BY v.fecha_venta DESC";
            
            $ventas = Ventas::consultarSQL($query);
            
            // Calcular totales del rango
            $query_totales = "SELECT 
                                COUNT(*) as total_ventas,
                                SUM(total) as monto_total,
                                AVG(total) as promedio_venta
                              FROM ventas 
                              WHERE DATE(fecha_venta) BETWEEN '" . $_POST['fecha_inicio'] . "' AND '" . $_POST['fecha_fin'] . "'
                              AND estado_venta = 'Completada'";
            
            $resultado_totales = ActiveRecord::$db->query($query_totales);
            $totales = $resultado_totales->fetch_assoc();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas por rango encontradas',
                'ventas' => $ventas,
                'estadisticas' => $totales
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar ventas por rango',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerVentasRecientes() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $limite = $_POST['limite'] ?? 10;
            
            $query = "SELECT v.*, 
                             CONCAT(c.nombre, ' ', c.apellido) as nombre_cliente
                      FROM ventas v 
                      JOIN clientes c ON v.id_cliente = c.id_cliente
                      ORDER BY v.fecha_venta DESC LIMIT " . $limite;
            
            $ventas = Ventas::consultarSQL($query);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas recientes encontradas',
                'data' => $ventas
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener ventas recientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerEstadisticasVentas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $mes = $_POST['mes'] ?? date('m');
            $año = $_POST['año'] ?? date('Y');
            
            $query = "SELECT 
                        COUNT(*) as total_ventas,
                        SUM(total) as monto_total,
                        AVG(total) as promedio_venta,
                        MAX(total) as venta_mayor,
                        MIN(total) as venta_menor
                      FROM ventas 
                      WHERE MONTH(fecha_venta) = " . $mes . " 
                      AND YEAR(fecha_venta) = " . $año . "
                      AND estado_venta = 'Completada'";
            
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

    public static function cancelarVenta() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_venta'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de venta requerido'
            ]);
            return;
        }

        try {
            // Iniciar transacción
            ActiveRecord::$db->autocommit(false);

            $venta = Ventas::find($_POST['id_venta']);
            
            if (!$venta) {
                throw new Exception('Venta no encontrada');
            }

            if ($venta->estado_venta === 'Cancelada') {
                throw new Exception('La venta ya está cancelada');
            }

            // Obtener detalles para restaurar stock
            $query_detalles = "SELECT * FROM detalle_ventas WHERE id_venta = " . $_POST['id_venta'];
            $detalles = DetalleVentas::consultarSQL($query_detalles);

            // Restaurar stock
            foreach($detalles as $detalle) {
                $inventario = Inventario::find($detalle->id_inventario);
                if ($inventario) {
                    $inventario->stock = $inventario->stock + $detalle->cantidad;
                    $inventario->actualizar();
                }
            }

            // Cambiar estado a cancelada
            $venta->estado_venta = 'Cancelada';
            $resultado = $venta->actualizar();

            if (!$resultado) {
                throw new Exception('Error al cancelar la venta');
            }

            // Confirmar transacción
            ActiveRecord::$db->commit();
            ActiveRecord::$db->autocommit(true);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta cancelada exitosamente'
            ]);

        } catch (Exception $e) {
            // Revertir transacción
            ActiveRecord::$db->rollback();
            ActiveRecord::$db->autocommit(true);

            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}

?>