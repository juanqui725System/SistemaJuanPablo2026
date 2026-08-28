<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if($_SERVER['REQUEST_METHOD']=='OPTIONS')
    {
        exit;
    }
require_once "../src/Router.php";
require_once "../src/Controllers/UserController.php";
require_once "../src/Controllers/ProductoController.php";
require_once "../src/Controllers/ClienteController.php";

use App\Router;

$route=new Router();
//direccion para usuarios
$route->add('GET','/','UserController@getAll');
$route->add('GET','/users','UserController@getAll'); 
//direccion de productos
$route->add('GET','/productos','ProductoController@getAll'); 
$route->add('PUT','/productos/{id}','ProductoController@actualizar'); 
$route->add('POST','/productos','ProductoController@add'); 
$route->add('DELETE','/productos/{id}','ProductoController@eliminar'); 
//direccion de clientes
$route->add('GET','/clientes','ClienteController@getAll');


$route->run();
