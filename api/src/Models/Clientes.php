<?php
include_once __DIR__ . "/../Config/conexionDB.php";
class Clientes
{
    //Mostrar ciente
    public static function all()
    {
        $sql = "SELECT * FROM CLIENTES ORDER BY id";
        return ConexionPDO::query($sql);
    }
    public static function find($id)
    {
        $clientes = ConexionPDO::query(
            "SELECT * FROM CLIENTES WHERE id = :id",
            [":id" => $id]
        );
        return $clientes[0] ?? null;
    }
    //Actualizar Cliente
    public static function update($id, $data)
    {
        $sql = "UPDATE CLIENTES
                SET ci = :ci, nombre = :nombre, apellidos = :apellidos,
                    direccion = :direccion, telefono = :telefono
                WHERE id = :id";
        return ConexionPDO::execute($sql, [
            ":id" => $id,
            ":ci" => $data["ci"],
            ":nombre" => $data["nombre"],
            ":apellidos" => $data["apellidos"],
            ":direccion" => $data["direccion"],
            ":telefono" => $data["telefono"],
        ], false);
    }
    public static function add($data)
    {
        $sql = "INSERT INTO CLIENTES (ci, nombre, apellidos, direccion, telefono)
                VALUES (:ci, :nombre, :apellidos, :direccion, :telefono)";
        return ConexionPDO::execute($sql, [
            ":ci" => $data["ci"],
            ":nombre" => $data["nombre"],
            ":apellidos" => $data["apellidos"],
            ":direccion" => $data["direccion"],
            ":telefono" => $data["telefono"],
        ], true);
    }
    public static function delete($id)
    {
        return ConexionPDO::execute(
            "DELETE FROM CLIENTES WHERE id = :id",
            [":id" => $id],
            false
        );
    }
}
