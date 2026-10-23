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
require_once "../src/Controllers/EmpleadoController.php";

use App\Router;

$route=new Router();
//direccion para usuarios
$route->add('GET','/','UserController@getAll');
$route->add('GET','/users','UserController@getAll'); 
$route->add('GET','/users/{id}','UserController@getById'); 
$route->add('POST','/users','UserController@add'); 
$route->add('POST','/login','UserController@login'); 
$route->add('PUT','/users/{id}','UserController@actualizar'); 
$route->add('DELETE','/users/{id}','UserController@eliminar'); 
//direccion de productos
$route->add('GET','/productos','ProductoController@getAll'); 
$route->add('GET','/productos/{id}','ProductoController@getById');
$route->add('PUT','/productos/{id}','ProductoController@actualizar'); 
$route->add('POST','/productos','ProductoController@add'); 
$route->add('DELETE','/productos/{id}','ProductoController@eliminar'); 
//direccion de clientes
$route->add('GET','/clientes','ClienteController@getAll');
$route->add('GET','/clientes/{id}','ClienteController@getById');
$route->add('POST','/clientes','ClienteController@add');
$route->add('PUT','/clientes/{id}','ClienteController@actualizar');
$route->add('DELETE','/clientes/{id}','ClienteController@eliminar');

//direccion de empleados
$route->add('GET','/empleados','EmpleadoController@getAll');
$route->add('GET','/empleados/{id}','EmpleadoController@getById');
$route->add('POST','/empleados','EmpleadoController@add');
$route->add('PUT','/empleados/{id}','EmpleadoController@actualizar');
$route->add('DELETE','/empleados/{id}','EmpleadoController@eliminar');


$route->run();
