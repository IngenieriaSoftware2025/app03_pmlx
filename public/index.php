<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
//importa la clase clientes y controlles 
use Controllers\ClienteController;


$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);


$router->get('/clientes',[ClienteController::class,'mostrarPagina']);
$router->post('/guardarClientes',[ClienteController::class,'guardarCliente']);
$router->post('/modificarClientes',[ClienteController::class,'ModificarCliente']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
