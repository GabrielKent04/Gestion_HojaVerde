<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function responder(int $codigoHttp, array $respuesta): void
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

if (!isset($_SESSION['id_usuario'])) {
    responder(401, ['success' => false, 'message' => 'La sesión expiró. Inicia sesión nuevamente.']);
}

if (($_SESSION['rol'] ?? '') !== 'Gerente') {
    responder(403, ['success' => false, 'message' => 'No tienes permiso para editar medicamentos.']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(405, ['success' => false, 'message' => 'Método no permitido.']);
}

$idMedicamento = $_POST['id_medicamento'] ?? null;
if (!is_string($idMedicamento) || !preg_match('/^[1-9]\d*$/', $idMedicamento) || (float) $idMedicamento > 2147483647) {
    responder(422, ['success' => false, 'message' => 'El identificador del medicamento no es válido.']);
}

$camposTexto = [
    'codigo_ref' => 50,
    'codigo_ubicacion' => 50,
    'nombre' => 100,
    'laboratorio' => 100,
];
$datos = [];
foreach ($camposTexto as $campo => $longitudMaxima) {
    $valor = $_POST[$campo] ?? null;
    if (!is_string($valor) || trim($valor) === '' || mb_strlen(trim($valor), 'UTF-8') > $longitudMaxima) {
        responder(422, ['success' => false, 'message' => 'Verifica los campos obligatorios y sus longitudes.']);
    }
    $datos[$campo] = trim($valor);
}

$descripcion = $_POST['descripcion'] ?? '';
if (!is_string($descripcion) || mb_strlen(trim($descripcion), 'UTF-8') > 255) {
    responder(422, ['success' => false, 'message' => 'La descripción no es válida o supera los 255 caracteres.']);
}
$datos['descripcion'] = trim($descripcion);

$precio = $_POST['precio'] ?? null;
$stock = $_POST['stock'] ?? null;
if (!is_string($precio) || !is_numeric($precio) || !is_finite((float) $precio) || (float) $precio < 0 || (float) $precio > 99999999.99) {
    responder(422, ['success' => false, 'message' => 'El precio debe ser un número válido mayor o igual a cero.']);
}
if (!is_string($stock) || !preg_match('/^\d+$/', $stock) || (float) $stock > 2147483647) {
    responder(422, ['success' => false, 'message' => 'El stock debe ser un entero mayor o igual a cero.']);
}

require_once __DIR__ . '/../../config/conexion.php';

try {
    $sql = 'UPDATE medicamentos
        SET codigo_ref = :codigo_ref,
            codigo_ubicacion = :codigo_ubicacion,
            nombre = :nombre,
            laboratorio = :laboratorio,
            descripcion = :descripcion,
            precio = :precio,
            stock = :stock
        WHERE id_medicamento = :id_medicamento';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':codigo_ref' => $datos['codigo_ref'],
        ':codigo_ubicacion' => $datos['codigo_ubicacion'],
        ':nombre' => $datos['nombre'],
        ':laboratorio' => $datos['laboratorio'],
        ':descripcion' => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
        ':precio' => $precio,
        ':stock' => $stock,
        ':id_medicamento' => (int) $idMedicamento,
    ]);

    $stmt = $pdo->prepare('SELECT * FROM medicamentos WHERE id_medicamento = :id_medicamento');
    $stmt->execute([':id_medicamento' => (int) $idMedicamento]);
    $medicamento = $stmt->fetch();
    if (!$medicamento) {
        responder(404, ['success' => false, 'message' => 'No se encontró el medicamento que deseas actualizar.']);
    }

    $medicamento['id_medicamento'] = (int) $medicamento['id_medicamento'];
    $medicamento['precio'] = (float) $medicamento['precio'];
    $medicamento['stock'] = (int) $medicamento['stock'];
    $medicamento['estado'] = (int) $medicamento['estado'];

    responder(200, ['success' => true, 'medicamento' => $medicamento]);
} catch (PDOException $error) {
    error_log($error->getMessage());
    if ($error->getCode() === '23000') {
        responder(409, ['success' => false, 'message' => 'El código de referencia ya está registrado o los datos no cumplen las relaciones de la base de datos.']);
    }
    responder(500, ['success' => false, 'message' => 'No se pudo actualizar el medicamento. Inténtalo nuevamente.']);
}