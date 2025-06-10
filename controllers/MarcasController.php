<?php 

namespace Controllers;

use Exception;
use Model\Marcas;
use Model\ActiveRecord;
use MVC\Router;

class MarcasController {

    public static function mostrarPagina(Router $router) {
        $router->render('marcas/index', []);
    }

    public static function buscarMarcas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $marcas = Marcas::all();
            
            if ($marcas) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marcas encontradas',
                    'data' => $marcas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron marcas',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarMarca() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['nombre_marca']) || empty($_POST['descripcion'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Faltan campos obligatorios'
            ]);
            return;
        }

        try {
            // Validar y limpiar datos
            $_POST['nombre_marca'] = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre_marca']))));
            $_POST['descripcion'] = trim(htmlspecialchars($_POST['descripcion']));
            $_POST['activo'] = $_POST['activo'] ?? 'S';

            // Validaciones
            if (strlen($_POST['nombre_marca']) < 2) {
                throw new Exception('El nombre de la marca es inválido');
            }
            if (strlen($_POST['descripcion']) < 5) {
                throw new Exception('La descripción debe tener al menos 5 caracteres');
            }

            // Verificar si la marca ya existe
            $query = "SELECT * FROM marcas WHERE nombre_marca = '" . ActiveRecord::$db->escape_string($_POST['nombre_marca']) . "'";
            $existe = Marcas::consultarSQL($query);
            
            if (!empty($existe)) {
                throw new Exception('La marca ya está registrada');
            }

            $marca = new Marcas([
                'nombre_marca' => $_POST['nombre_marca'],
                'descripcion' => $_POST['descripcion'],
                'activo' => $_POST['activo']
            ]);

            $resultado = $marca->crear();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marca guardada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar la marca'
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

    public static function modificarMarca() {
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
            $marca = Marcas::find($_POST['id_marca']);
            
            if (!$marca) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Marca no encontrada'
                ]);
                return;
            }

            // Verificar marca única (excluyendo la marca actual)
            if (!empty($_POST['nombre_marca'])) {
                $query = "SELECT * FROM marcas WHERE nombre_marca = '" . ActiveRecord::$db->escape_string($_POST['nombre_marca']) . "' AND id_marca != " . $_POST['id_marca'];
                $existe = Marcas::consultarSQL($query);
                
                if (!empty($existe)) {
                    throw new Exception('La marca ya está registrada');
                }
            }

            // Actualizar datos
            $marca->nombre_marca = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre_marca']))));
            $marca->descripcion = trim(htmlspecialchars($_POST['descripcion']));
            $marca->activo = $_POST['activo'] ?? 'S';

            $resultado = $marca->actualizar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marca modificada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar la marca'
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

    public static function eliminarMarca() {
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
            $marca = Marcas::find($_POST['id_marca']);
            
            if (!$marca) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Marca no encontrada'
                ]);
                return;
            }

            // Verificar si la marca está siendo usada en el inventario
            $query = "SELECT COUNT(*) as total FROM inventario WHERE id_marca = " . $_POST['id_marca'];
            $resultado = ActiveRecord::$db->query($query);
            $row = $resultado->fetch_assoc();
            
            if ($row['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar la marca porque tiene productos asociados'
                ]);
                return;
            }

            $resultado = $marca->eliminar();
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marca eliminada exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar la marca'
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

    public static function cambiarEstadoMarca() {
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
            $marca = Marcas::find($_POST['id_marca']);
            
            if (!$marca) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Marca no encontrada'
                ]);
                return;
            }

            // Cambiar estado
            $marca->activo = ($marca->activo === 'S') ? 'N' : 'S';
            $resultado = $marca->actualizar();
            
            if ($resultado) {
                $estado = ($marca->activo === 'S') ? 'activada' : 'desactivada';
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => "Marca {$estado} exitosamente",
                    'nuevo_estado' => $marca->activo
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al cambiar el estado de la marca'
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

    public static function buscarMarcasActivas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $query = "SELECT * FROM marcas WHERE activo = 'S' ORDER BY nombre_marca ASC";
            $marcas = Marcas::consultarSQL($query);
            
            if (!empty($marcas)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marcas activas encontradas',
                    'data' => $marcas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron marcas activas',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar marcas activas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarMarcaPorNombre() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if (empty($_POST['nombre_marca'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre de marca requerido'
            ]);
            return;
        }

        try {
            $nombre = ActiveRecord::$db->escape_string($_POST['nombre_marca']);
            $query = "SELECT * FROM marcas WHERE nombre_marca LIKE '%{$nombre}%' ORDER BY nombre_marca ASC LIMIT 10";
            
            $marcas = Marcas::consultarSQL($query);
            
            if (!empty($marcas)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marcas encontradas',
                    'data' => $marcas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se encontraron marcas',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerEstadisticasMarcas() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        try {
            $query = "SELECT m.nombre_marca, 
                             COUNT(i.id_inventario) as total_productos,
                             SUM(i.stock) as total_stock
                      FROM marcas m 
                      LEFT JOIN inventario i ON m.id_marca = i.id_marca 
                      WHERE m.activo = 'S'
                      GROUP BY m.id_marca, m.nombre_marca 
                      ORDER BY total_productos DESC";
            
            $resultado = ActiveRecord::$db->query($query);
            $estadisticas = [];
            
            while($row = $resultado->fetch_assoc()) {
                $estadisticas[] = $row;
            }
            
            if (!empty($estadisticas)) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Estadísticas obtenidas',
                    'data' => $estadisticas
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay estadísticas disponibles',
                    'data' => []
                ]);
            }
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