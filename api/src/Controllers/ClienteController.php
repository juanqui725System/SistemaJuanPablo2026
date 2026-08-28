<?php
require_once "../src/Models/Clientes.php";
class ClienteController
{
    public function getAll()
    {
        $cliente = Clientes::all();
        echo json_encode($cliente);
    }
    //Actualizar Producto
    public function actualizar($id)
    {

    }
    //Adicionar Producto
    public function add()
    {
       
    }
    //Eliminar Producto
    public function eliminar($id)
    {
        
    }
}
