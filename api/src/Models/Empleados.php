<?php

include_once __DIR__ . "/../Config/conexionDB.php";

class Empleados
{
    public static function all()
    {
        return ConexionPDO::query("SELECT * FROM EMPLEADOS ORDER BY id");
    }

    public static function find($id)
    {
        $empleados = ConexionPDO::query(
            "SELECT * FROM EMPLEADOS WHERE id = :id",
            [":id" => $id]
        );

        return $empleados[0] ?? null;
    }

    public static function add($data)
    {
        $sql = "INSERT INTO EMPLEADOS (ci, nombre, apellidos)
                VALUES (:ci, :nombre, :apellidos)";

        return ConexionPDO::execute($sql, [
            ":ci" => $data["ci"],
            ":nombre" => $data["nombre"],
            ":apellidos" => $data["apellidos"],
        ], true);
    }

    public static function update($id, $data)
    {
        $sql = "UPDATE EMPLEADOS
                SET ci = :ci, nombre = :nombre, apellidos = :apellidos
                WHERE id = :id";

        return ConexionPDO::execute($sql, [
            ":id" => $id,
            ":ci" => $data["ci"],
            ":nombre" => $data["nombre"],
            ":apellidos" => $data["apellidos"],
        ], false);
    }

    public static function delete($id)
    {
        return ConexionPDO::execute(
            "DELETE FROM EMPLEADOS WHERE id = :id",
            [":id" => $id],
            false
        );
    }
}
