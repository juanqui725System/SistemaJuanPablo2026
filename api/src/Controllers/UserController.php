<?php
require_once __DIR__ . "/../Models/Users.php";
class UserController
{
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            http_response_code(400);
            echo json_encode(["estado" => false, "message" => "JSON invalido"]);
            return;
        }

        $credential = trim((string) ($data["username"] ?? $data["email"] ?? ""));
        $password = (string) ($data["password"] ?? "");
        $errores = [];

        if ($credential === "") {
            $errores["username"] = "El usuario o correo es obligatorio";
        }
        if ($password === "") {
            $errores["password"] = "La contrasena es obligatoria";
        }

        if ($errores) {
            http_response_code(422);
            echo json_encode([
                "estado" => false,
                "message" => "Existen errores de validacion",
                "errores" => $errores,
            ]);
            return;
        }

        $user = Users::findByUsernameOrEmail($credential);
        if ($user === null) {
            http_response_code(401);
            echo json_encode(["estado" => false, "message" => "Usuario o contrasena incorrectos"]);
            return;
        }

        if (!(bool) $user["estado"]) {
            http_response_code(401);
            echo json_encode(["estado" => false, "message" => "Usuario o contrasena incorrectos"]);
            return;
        }

        $storedPassword = (string) $user["password_hash"];
        $passwordIsValid = password_verify($password, $storedPassword);

        // Migrar al primer acceso las contrasenas antiguas guardadas en texto plano.
        if (!$passwordIsValid && hash_equals($storedPassword, $password)) {
            $passwordIsValid = true;
            Users::update($user["id"], [
                "password_hash" => password_hash($password, PASSWORD_DEFAULT),
            ]);
        }

        if (!$passwordIsValid) {
            http_response_code(401);
            echo json_encode(["estado" => false, "message" => "Usuario o contrasena incorrectos"]);
            return;
        }

        echo json_encode([
            "estado" => true,
            "message" => "Inicio de sesion correcto",
            "usuario" => [
                "id" => (int) $user["id"],
                "username" => $user["username"],
                "email" => $user["email"],
                "rol" => $user["rol"],
                "estado" => (bool) $user["estado"],
            ],
        ]);
    }

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

        // Guardar una contraseña como hash irreversible, nunca en texto plano.
        $data["password_hash"] = password_hash($data["password_hash"], PASSWORD_DEFAULT);

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

        if (isset($data["rol"])) {
            $rol = strtolower(trim((string) $data["rol"]));
            if (!in_array($rol, ["administrador", "vendedor"], true)) {
                $errores["rol"] = "El rol debe ser administrador o vendedor";
            } else {
                $user["rol"] = $rol;
            }
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
