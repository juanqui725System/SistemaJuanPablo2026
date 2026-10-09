<?php
include_once __DIR__ . "/../Config/conexionDB.php";
class Users
{
    public static function all()
    {
        return ConexionPDO::query("SELECT * FROM USUARIOS ORDER BY id");
    }

    public static function find($id)
    {
        $users = ConexionPDO::query(
            "SELECT * FROM USUARIOS WHERE id = :id",
            [":id" => $id]
        );
        return $users[0] ?? null;
    }

    public static function add($data)
    {
        $columnas = [];
        $campos = [];
        $valores = [];
        foreach ($data as $columna => $valor) {
            $columnas[] = $columna;
            $campos[] = ":$columna";
            $valores[":$columna"] = $valor;
        }

        $stringColumnas = implode(",", $columnas);
        $stringCampos = implode(",", $campos);

        $sql = "INSERT INTO USUARIOS ($stringColumnas) VALUES ($stringCampos)";
        return ConexionPDO::execute($sql, $valores, true);
    }

    public static function update($id, $data)
    {
        if (isset($data["id"])) {
            unset($data["id"]);
        }

        $campos = [];
        $valores = [];
        foreach ($data as $columna => $valor) {
            $campos[] = "$columna=:$columna";
            $valores[":$columna"] = $valor;
        }

        $stringCampos = implode(",", $campos);

        $sql = "UPDATE USUARIOS SET $stringCampos WHERE id=:id";
        $valores[":id"] = $id;
        return ConexionPDO::execute($sql, $valores, false);
    }

    public static function delete($id)
    {
        $sql = "DELETE FROM USUARIOS WHERE id=:id";
        $valores = [":id" => $id];
        return ConexionPDO::execute($sql, $valores, false);
    }
}