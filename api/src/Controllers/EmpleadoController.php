<?php

require_once __DIR__ . "/../Models/Empleados.php";

class EmpleadoController
{
    public function getAll()
    {
        echo json_encode(Empleados::all());
    }

    public function getById($id)
    {
        $empleado = Empleados::find($id);
        if ($empleado === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Empleado no encontrado"]);
            return;
        }

        echo json_encode($empleado);
    }

    public function add()
    {
        $data = $this->getValidatedData();
        if ($data === null) {
            return;
        }

        $id = Empleados::add($data);
        http_response_code(201);
        echo json_encode([
            "estado" => true,
            "message" => "Empleado adicionado correctamente",
            "id" => (int) $id,
        ]);
    }

    public function actualizar($id)
    {
        $data = $this->getValidatedData();
        if ($data === null) {
            return;
        }

        if (Empleados::find($id) === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Empleado no encontrado"]);
            return;
        }

        Empleados::update($id, $data);
        echo json_encode(["estado" => true, "message" => "Empleado actualizado correctamente"]);
    }

    public function eliminar($id)
    {
        if (Empleados::find($id) === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Empleado no encontrado"]);
            return;
        }

        Empleados::delete($id);
        echo json_encode(["estado" => true, "message" => "Empleado eliminado correctamente"]);
    }

    private function getValidatedData()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            http_response_code(400);
            echo json_encode([
                "estado" => false,
                "message" => "JSON invalido",
                "error" => json_last_error_msg(),
            ]);
            return null;
        }

        $limits = ["ci" => 20, "nombre" => 50, "apellidos" => 50];
        $errores = [];
        $empleado = [];
        foreach ($limits as $campo => $limite) {
            $valor = isset($data[$campo]) ? trim((string) $data[$campo]) : "";
            if ($valor === "") {
                $errores[$campo] = "El campo $campo es obligatorio";
            } elseif (mb_strlen($valor) > $limite) {
                $errores[$campo] = "El campo $campo no debe superar $limite caracteres";
            } else {
                $empleado[$campo] = $valor;
            }
        }

        if ($errores) {
            http_response_code(422);
            echo json_encode([
                "estado" => false,
                "message" => "Existen errores de validacion",
                "errores" => $errores,
            ]);
            return null;
        }

        return $empleado;
    }
}
