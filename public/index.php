<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;

// Importar todos los controladores
use Controllers\AuthController;
use Controllers\DashboardController;
use Controllers\UsuariosController;
use Controllers\ClientesController;
use Controllers\MarcasController;
use Controllers\InventarioController;
use Controllers\VentasController;
use Controllers\ReparacionesController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']); // Esto lee APP_NAME del .env

// RUTAS PRINCIPALES
$router->get('/', [AppController::class,'index']);

// Autenticación
$router->get('/login', [AuthController::class, 'mostrarLogin']);
$router->post('/iniciarSesion', [AuthController::class, 'iniciarSesion']);
$router->post('/cerrarSesion', [AuthController::class, 'cerrarSesion']);

// RUTAS DE CLIENTES (SIGUIENDO TU PATRÓN EXACTO)
$router->get('/clientes', [ClientesController::class, 'mostrarPagina']);
$router->post('/clientes/guardarAPI', [ClientesController::class, 'guardarCliente']);
$router->get('/clientes/buscarAPI', [ClientesController::class, 'buscarClientes']);
$router->post('/clientes/modificarAPI', [ClientesController::class, 'modificarCliente']);
$router->get('/clientes/eliminar', [ClientesController::class, 'eliminarCliente']);
$router->post('/clientes/buscarPorCedulaAPI', [ClientesController::class, 'buscarClientePorCedula']);
$router->post('/clientes/buscarPorNombreAPI', [ClientesController::class, 'buscarClientesPorNombre']);
$router->get('/clientes/recientes', [ClientesController::class, 'obtenerClientesRecientes']);

// // RUTAS DE USUARIOS
$router->get('/usuarios', [UsuariosController::class, 'mostrarPagina']);
$router->post('/usuarios/guardarAPI', [UsuariosController::class, 'guardarUsuario']);
$router->get('/usuarios/buscarAPI', [UsuariosController::class, 'buscarUsuarios']);
$router->post('/usuarios/modificarAPI', [UsuariosController::class, 'modificarUsuario']);
$router->get('/usuarios/eliminar', [UsuariosController::class, 'eliminarUsuario']);
$router->get('/usuarios/roles', [UsuariosController::class, 'buscarRoles']);


// // RUTAS DE INVENTARIO
// $router->get('/inventario', [InventarioController::class, 'mostrarPagina']);
// $router->post('/inventario/guardarAPI', [InventarioController::class, 'guardarProducto']);
// $router->get('/inventario/buscarAPI', [InventarioController::class, 'buscarInventario']);
// $router->post('/inventario/modificarAPI', [InventarioController::class, 'modificarProducto']);
// $router->get('/inventario/eliminar', [InventarioController::class, 'eliminarProducto']);

// // =============================================
// // RUTAS DE VENTAS
// // =============================================
// $router->get('/ventas', [VentasController::class, 'mostrarPagina']);
// $router->post('/ventas/guardarAPI', [VentasController::class, 'guardarVenta']);
// $router->get('/ventas/buscarAPI', [VentasController::class, 'buscarVentas']);
// $router->post('/ventas/modificarAPI', [VentasController::class, 'modificarVenta']);

// // =============================================
// // RUTAS DE REPARACIONES
// // =============================================
// $router->get('/reparaciones', [ReparacionesController::class, 'mostrarPagina']);
// $router->post('/reparaciones/guardarAPI', [ReparacionesController::class, 'guardarReparacion']);
// $router->get('/reparaciones/buscarAPI', [ReparacionesController::class, 'buscarReparaciones']);
// $router->post('/reparaciones/modificarAPI', [ReparacionesController::class, 'modificarReparacion']);

// // =============================================
// // RUTAS DE MARCAS
// // =============================================
// $router->get('/marcas', [MarcasController::class, 'mostrarPagina']);
// $router->post('/marcas/guardarAPI', [MarcasController::class, 'guardarMarca']);
// $router->get('/marcas/buscarAPI', [MarcasController::class, 'buscarMarcas']);
// $router->post('/marcas/modificarAPI', [MarcasController::class, 'modificarMarca']);

// // Dashboard
// $router->get('/dashboard', [DashboardController::class, 'mostrarPagina']);
// $router->get('/dashboard/datos', [DashboardController::class, 'obtenerDatos']);

$router->comprobarRutas();
