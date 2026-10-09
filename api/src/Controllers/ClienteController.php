<?php
require_once "../src/Models/Clientes.php";
class ClienteController
{
    public function getAll()
    {
        $cliente = Clientes::all();
        echo json_encode($cliente);
    }
    public function getById($id)
    {
        $cliente = Clientes::find($id);
        if ($cliente === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Cliente no encontrado"]);
            return;
        }
        echo json_encode($cliente);
    }

    //Actualizar Cliente
    public function actualizar($id)
    {
        $data = $this->getValidatedData();
        if ($data === null) {
            return;
        }

        if (Clientes::find($id) === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Cliente no encontrado"]);
            return;
        }

        Clientes::update($id, $data);
        echo json_encode(["estado" => true, "message" => "Cliente actualizado correctamente"]);
    }
    //Adicionar Cliente
    public function add()
    {
        $data = $this->getValidatedData();
        if ($data === null) {
            return;
        }

        $id = Clientes::add($data);
        http_response_code(201);
        echo json_encode([
            "estado" => true,
            "message" => "Cliente adicionado correctamente",
            "id" => (int) $id,
        ]);
    }
    //Eliminar Cliente
    public function eliminar($id)
    {
        if (Clientes::find($id) === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Cliente no encontrado"]);
            return;
        }

        Clientes::delete($id);
        echo json_encode(["estado" => true, "message" => "Cliente eliminado correctamente"]);
    }

    private function getValidatedData()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            http_response_code(400);
            echo json_encode(["estado" => false, "message" => "JSON invalido"]);
            return null;
        }

        $required = ["ci" => 20, "nombre" => 50, "apellidos" => 50];
        $optional = ["direccion" => 250, "telefono" => 15];
        $cliente = [];
        $errores = [];

        foreach ($required as $campo => $limite) {
            $valor = isset($data[$campo]) ? trim((string) $data[$campo]) : "";
            if ($valor === "") {
                $errores[$campo] = "El campo $campo es obligatorio";
            } elseif (mb_strlen($valor) > $limite) {
                $errores[$campo] = "El campo $campo no debe superar $limite caracteres";
            } else {
                $cliente[$campo] = $valor;
            }
        }

        foreach ($optional as $campo => $limite) {
            $valor = isset($data[$campo]) ? trim((string) $data[$campo]) : "";
            if (mb_strlen($valor) > $limite) {
                $errores[$campo] = "El campo $campo no debe superar $limite caracteres";
            }
            $cliente[$campo] = $valor === "" ? null : $valor;
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

        return $cliente;
    }
}
