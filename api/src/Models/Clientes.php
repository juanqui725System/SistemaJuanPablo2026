<?php
include_once __DIR__ . "/../Config/conexionDB.php";
class Clientes
{
    //Mostrar ciente
    public static function all()
    {
        $sql = "SELECT * FROM CLIENTES";
        return ConexionPDO::query($sql);
    }
    //Actualizar Cliente
    public static function update($id, $data)
    {
       
    }
    public static function add($data)
    {

    }
    public static function delete($id)
    {
       
    }
}
