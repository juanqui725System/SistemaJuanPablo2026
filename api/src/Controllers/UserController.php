<?php
require_once __DIR__ . "/../Models/Users.php";
class UserController
{
    public function getAll()
    {
        echo json_encode(Users::all());
    }

    public function getById($id)
    {
        $user = Users::find($id);
        if ($user === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Usuario no encontrado"]);
            return;
        }
        echo json_encode($user);
    }

    //Actualizar Usuario
    public function actualizar($id)
    {
        $data = $this->getValidatedData();
        if ($data === null) {
            return;
        }

        if (Users::find($id) === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Usuario no encontrado"]);
            return;
        }

        Users::update($id, $data);
        echo json_encode(["estado" => true, "message" => "Usuario actualizado correctamente"]);
    }

    //Adicionar Usuario
    public function add()
    {
        $data = $this->getValidatedData();
        if ($data === null) {
            return;
        }

        $id = Users::add($data);
        http_response_code(201);
        echo json_encode([
            "estado" => true,
            "message" => "Usuario adicionado correctamente",
            "id" => (int) $id,
        ]);
    }

    //Eliminar Usuario
    public function eliminar($id)
    {
        if (Users::find($id) === null) {
            http_response_code(404);
            echo json_encode(["estado" => false, "message" => "Usuario no encontrado"]);
            return;
        }

        Users::delete($id);
        echo json_encode(["estado" => true, "message" => "Usuario eliminado correctamente"]);
    }

    private function getValidatedData()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            http_response_code(400);
            echo json_encode(["estado" => false, "message" => "JSON invalido"]);
            return null;
        }

        $required = ["username" => 50, "email" => 50, "password_hash" => 255, "cod_empleado" => null];
        $user = [];
        $errores = [];

        foreach ($required as $campo => $limite) {
            $valor = isset($data[$campo]) ? trim((string) $data[$campo]) : "";
            if ($valor === "") {
                $errores[$campo] = "El campo $campo es obligatorio";
            } elseif ($limite !== null && mb_strlen($valor) > $limite) {
                $errores[$campo] = "El campo $campo no debe superar $limite caracteres";
            } else {
                $user[$campo] = $valor;
            }
        }

        if (isset($user["cod_empleado"]) && !ctype_digit((string) $user["cod_empleado"])) {
            $errores["cod_empleado"] = "El campo cod_empleado debe ser numerico";
        }

        if (isset($data["estado"]) && $data["estado"] !== "") {
            $user["estado"] = filter_var($data["estado"], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
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

        return $user;
    }
}