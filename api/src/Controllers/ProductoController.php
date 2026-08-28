<?php
require_once "../src/Models/Productos.php";
class ProductoController
{
    public function getAll()
    {
        $producto = Productos::all();
        echo json_encode($producto);
    }
    //Actualizar Producto
    public function actualizar($id)
    {

        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            echo json_encode(
                [
                    "status" => "Error codificacion",
                    "message" => json_last_error_msg(),
                ]
            );
            return;
        }

        $errores = [];
        $camposObligatorios = [
            "codBarras" => "El campo codBarras es obligatorio",
            "descripcion" => "El campo descripcion es obligatorio",
            "stock" => "El campo stock es obligatorio",
            "precio_unitario" => "El campo precio_unitario es obligatorio",
            "creado_por" => "El campo creado_por es obligatorio",
            "fecha_registro" => "El campo fecha_registro es obligatorio",
        ];

        foreach ($camposObligatorios as $campo => $mensaje) {
            if (!isset($data[$campo]) || trim($data[$campo]) == "") {
                $errores[$campo] = $mensaje;
            }
        }

        if (isset($data['stock']) && trim($data['stock']) != "" && !is_numeric($data['stock'])) {
            $errores['stock'] = "El campo stock debe ser numerico";
        }

        if (isset($data['precio_unitario']) && trim($data['precio_unitario']) != "" && !is_numeric($data['precio_unitario'])) {
            $errores['precio_unitario'] = "El campo precio_unitario debe ser numerico";
        }

        if (isset($data['creado_por']) && trim($data['creado_por']) != "" && !is_numeric($data['creado_por'])) {
            $errores['creado_por'] = "El campo creado_por debe ser numerico";
        }

        if (!empty($errores)) {
            echo json_encode([
                "estado" => false,
                "message" => "Existen errores de validacion",
                "errores" => $errores,
            ]);
            return;
        }

        $producto = Productos::update($id, $data);
        if ($producto) {
            echo json_encode([
                "estado" => true,
                "message" => "Producto actualizado correctamente",
            ]);
            return;
        }
        echo json_encode($producto);
    }
    //Adicionar Producto
    public function add()
    {
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            echo json_encode(
                [
                    "status" => "Error codificacion",
                    "message" => json_last_error_msg(),
                ]
            );
            return;
        }

        $errores = [];
        $camposObligatorios = [
            "codBarras" => "El campo codBarras es obligatorio",
            "descripcion" => "El campo descripcion es obligatorio",
            "stock" => "El campo stock es obligatorio",
            "precio_unitario" => "El campo precio_unitario es obligatorio",
            "creado_por" => "El campo creado_por es obligatorio",
            "fecha_registro" => "El campo fecha_registro es obligatorio",
        ];

        foreach ($camposObligatorios as $campo => $mensaje) {
            if (!isset($data[$campo]) || trim($data[$campo]) == "") {
                $errores[$campo] = $mensaje;
            }
        }

        if (isset($data['stock']) && trim($data['stock']) != "" && !is_numeric($data['stock'])) {
            $errores['stock'] = "El campo stock debe ser numerico";
        }

        if (isset($data['precio_unitario']) && trim($data['precio_unitario']) != "" && !is_numeric($data['precio_unitario'])) {
            $errores['precio_unitario'] = "El campo precio_unitario debe ser numerico";
        }

        if (isset($data['creado_por']) && trim($data['creado_por']) != "" && !is_numeric($data['creado_por'])) {
            $errores['creado_por'] = "El campo creado_por debe ser numerico";
        }

        if (!empty($errores)) {
            echo json_encode([
                "estado" => false,
                "message" => "Existen errores de validacion",
                "errores" => $errores,
            ]);
            return;
        }

        $producto = Productos::add($data);
        if ($producto) {
            echo json_encode([
                "estado" => true,
                "message" => "Producto adicionado correctamente",
            ]);
            return;
        }
        echo json_encode($producto);
    }
    //Eliminar Producto
    public function eliminar($id)
    {
        $producto = Productos::delete($id);
        if ($producto) {
            echo json_encode([
                "estado" => true,
                "message" => "Producto eliminado correctamente",
            ]);
            return;
        }
        echo json_encode($producto);
    }
}
