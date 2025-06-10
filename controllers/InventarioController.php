<?php 

namespace Controllers;

use Exception;
use Model\Inventario;
use Model\Marcas;
use Model\ActiveRecord;
use MVC\Router;

class InventarioController {

    public static function mostrarPagina(Router $router) {
        $router->render('inventario/index', []);
    }

    public static function buscarInventario() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            // Consulta con JOIN para obtener nombre de marca
            $query = "SELECT i.*, m.nombre_marca 
                     FROM inventario i 
                     JOIN marcas m ON i.id_marca = m.id_marca 
                     ORDER BY i.fecha_ingreso DESC";
            
            $inventario = Inventario::consultarSQL($query);
            
            if ($inventario) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventario encontrado',
                    'data' => $inventario
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontró inventario',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarProducto() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['modelo']) || empty($_POST['id_marca']) || empty($_POST['estado_dispositivo']) || empty($_POST['precio_venta'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        try {
            // Validar y limpiar datos
            $_POST['modelo'] = trim(htmlspecialchars($_POST['modelo']));
            $_POST['id_marca'] = filter_var($_POST['id_marca'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['imei'] = trim(htmlspecialchars($_POST['imei'] ?? ''));
            $_POST['numero_serie'] = trim(htmlspecialchars($_POST['numero_serie'] ?? ''));
            $_POST['estado_dispositivo'] = trim(htmlspecialchars($_POST['estado_dispositivo']));
            $_POST['precio_compra'] = filter_var($_POST['precio_compra'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $_POST['precio_venta'] = filter_var($_POST['precio_venta'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $_POST['stock'] = filter_var($_POST['stock'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
            $_POST['descripcion'] = trim(htmlspecialchars($_POST['descripcion'] ?? ''));

            // Validaciones
            if (strlen($_POST['modelo']) < 2) {
                throw new Exception('El modelo es inválido');
            }
            if ($_POST['precio_venta'] <= 0) {
                throw new Exception('El precio de venta debe ser mayor a 0');
            }
            if ($_POST['stock'] < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            // Verificar si el IMEI ya existe (si se proporciona)
            if (!empty($_POST['imei'])) {
                $query = "SELECT * FROM inventario WHERE imei = '" . ActiveRecord::$db->escape_string($_POST['imei']) . "'";
                $existe = Inventario::consultarSQL($query);
                
                if (!empty($existe)) {
                    throw new Exception('El IMEI ya está registrado');
                }
            }

            $producto = new Inventario([
                'modelo' => $_POST['modelo'],
                'id_marca' => $_POST['id_marca'],
                'imei' => $_POST['imei'],
                'numero_serie' => $_POST['numero_serie'],
                'estado_dispositivo' => $_POST['estado_dispositivo'],
                'precio_compra' => $_POST['precio_compra'],
                'precio_venta' => $_POST['precio_venta'],
                'stock' => $_POST['stock'],
                'descripcion' => $_POST['descripcion']
            ]);

            $resultado = $producto->crear();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Producto guardado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar el producto'
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

    public static function modificarProducto() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_inventario'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de producto requerido'
            ]);
            return;
        }

        try {
            $producto = Inventario::find($_POST['id_inventario']);
            
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            // Verificar IMEI único (excluyendo el producto actual)
            if (!empty($_POST['imei'])) {
                $query = "SELECT * FROM inventario WHERE imei = '" . ActiveRecord::$db->escape_string($_POST['imei']) . "' AND id_inventario != " . $_POST['id_inventario'];
                $existe = Inventario::consultarSQL($query);
                
                if (!empty($existe)) {
                    throw new Exception('El IMEI ya está registrado');
                }
            }

            // Actualizar datos
            $producto->modelo = trim(htmlspecialchars($_POST['modelo']));
            $producto->id_marca = filter_var($_POST['id_marca'], FILTER_SANITIZE_NUMBER_INT);
            $producto->imei = trim(htmlspecialchars($_POST['imei'] ?? ''));
            $producto->numero_serie = trim(htmlspecialchars($_POST['numero_serie'] ?? ''));
            $producto->estado_dispositivo = trim(htmlspecialchars($_POST['estado_dispositivo']));
            $producto->precio_compra = filter_var($_POST['precio_compra'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $producto->precio_venta = filter_var($_POST['precio_venta'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $producto->stock = filter_var($_POST['stock'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
            $producto->descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));

            $resultado = $producto->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Producto modificado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el producto'
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

    public static function eliminarProducto() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_inventario'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de producto requerido'
            ]);
            return;
        }

        try {
            $producto = Inventario::find($_POST['id_inventario']);
            
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            // Verificar si el producto está en ventas
            $query = "SELECT COUNT(*) as total FROM detalle_ventas WHERE id_inventario = " . $_POST['id_inventario'];
            $resultado = ActiveRecord::$db->query($query);
            $row = $resultado->fetch_assoc();
            
            if ($row['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el producto porque tiene ventas asociadas'
                ]);
                return;
            }

            $resultado = $producto->eliminar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Producto eliminado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar el producto'
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
            $query = "SELECT i.*, m.nombre_marca 
                     FROM inventario i 
                     JOIN marcas m ON i.id_marca = m.id_marca 
                     WHERE i.imei = '" . ActiveRecord::$db->escape_string($_POST['imei']) . "'";
            
            $producto = Inventario::consultarSQL($query);
            
            if (!empty($producto)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Producto encontrado',
                    'data' => $producto[0]
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado',
                    'data' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarProductosDisponibles() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $query = "SELECT i.*, m.nombre_marca 
                     FROM inventario i 
                     JOIN marcas m ON i.id_marca = m.id_marca 
                     WHERE i.stock > 0 
                     ORDER BY i.modelo ASC";
            
            $productos = Inventario::consultarSQL($query);
            
            if (!empty($productos)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Productos disponibles encontrados',
                    'data' => $productos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay productos disponibles',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar productos disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarStockBajo() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $limite = $_POST['limite'] ?? 5;
            $query = "SELECT i.*, m.nombre_marca 
                     FROM inventario i 
                     JOIN marcas m ON i.id_marca = m.id_marca 
                     WHERE i.stock <= " . $limite . " 
                     ORDER BY i.stock ASC";
            
            $productos = Inventario::consultarSQL($query);
            
            if (!empty($productos)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Productos con stock bajo encontrados',
                    'data' => $productos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay productos con stock bajo',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar productos con stock bajo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function actualizarStock() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_inventario']) || !isset($_POST['cantidad'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de producto y cantidad requeridos'
            ]);
            return;
        }

        try {
            $producto = Inventario::find($_POST['id_inventario']);
            
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            $cantidad = filter_var($_POST['cantidad'], FILTER_SANITIZE_NUMBER_INT);
            $nuevo_stock = $producto->stock + $cantidad;

            if ($nuevo_stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            $producto->stock = $nuevo_stock;
            $resultado = $producto->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Stock actualizado exitosamente',
                    'nuevo_stock' => $nuevo_stock
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al actualizar el stock'
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

    public static function buscarPorMarca() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['id_marca'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de marca requerido'
            ]);
            return;
        }

        try {
            $query = "SELECT i.*, m.nombre_marca 
                     FROM inventario i 
                     JOIN marcas m ON i.id_marca = m.id_marca 
                     WHERE i.id_marca = " . $_POST['id_marca'] . " 
                     ORDER BY i.modelo ASC";
            
            $productos = Inventario::consultarSQL($query);
            
            if (!empty($productos)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Productos encontrados',
                    'data' => $productos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron productos de esta marca',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar productos por marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerEstadisticasInventario() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $query = "SELECT 
                        COUNT(*) as total_productos,
                        SUM(stock) as total_stock,
                        SUM(precio_venta * stock) as valor_inventario,
                        COUNT(CASE WHEN stock <= 5 THEN 1 END) as productos_stock_bajo,
                        COUNT(CASE WHEN stock = 0 THEN 1 END) as productos_sin_stock
                      FROM inventario";
            
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
}

?>