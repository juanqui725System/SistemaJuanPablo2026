<?php
include_once __DIR__ . "/../Config/conexionDB.php";
class Productos
{
    //Mostrar Producto
    public static function all()
    {
        $sql = "SELECT * FROM PRODUCTOS";
        return ConexionPDO::query($sql);
    }
    //Actualizar Producto
    public static function update($id, $data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }
        $campos = [];
        $valores = [];
        //construir datos
        foreach ($data as $columna => $valor) {
            $campos[] = "$columna=:$columna";
            $valores[":$columna"] = $valor;
        }

        $stringCampos = implode(",", $campos);

        //preparamos la consulta
        $sql = "UPDATE PRODUCTOS SET $stringCampos WHERE id=:id";
        $valores[':id'] = $id;
        //print_r($valores);
        $result = ConexionPDO::execute($sql, $valores, false);
        return $result;
    }
    public static function add($data)
    {
        $columnas = [];
        $campos = [];
        $valores = [];
        //construir datos
        foreach ($data as $columna => $valor) {
            $columnas[] = $columna;
            $campos[] = ":$columna";
            $valores[":$columna"] = $valor;
        }

        $stringColumnas = implode(",", $columnas);
        $stringCampos = implode(",", $campos);
        //preparamos la consulta
        $sql = "INSERT INTO PRODUCTOS ($stringColumnas) VALUES ($stringCampos)";
        $result = ConexionPDO::execute($sql, $valores, true);
        return $result;

    }
    public static function delete($id)
    {
        $sql = "DELETE FROM PRODUCTOS WHERE id=:id";
        $valores = [
            ":id" => $id
        ];
        $result = ConexionPDO::execute($sql, $valores, false);
        return $result;
    }
}
