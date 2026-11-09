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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(405, ['success' => false, 'message' => 'Método no permitido.']);
}

$camposTexto = ['codigo_ref', 'codigo_ubicacion', 'nombre', 'laboratorio'];
$datos = [];
foreach ($camposTexto as $campo) {
    $valor = $_POST[$campo] ?? null;
    if (!is_string($valor) || trim($valor) === '') {
        responder(422, ['success' => false, 'message' => 'Completa todos los campos obligatorios.']);
    }
    $datos[$campo] = trim($valor);
}

$descripcion = $_POST['descripcion'] ?? '';
if (!is_string($descripcion)) {
    responder(422, ['success' => false, 'message' => 'La descripción no es válida.']);
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
    $sql = 'INSERT INTO medicamentos
        (id_usuario_responsable, codigo_ref, codigo_ubicacion, nombre, laboratorio, descripcion, precio, stock, estado)
        VALUES (:id_usuario_responsable, :codigo_ref, :codigo_ubicacion, :nombre, :laboratorio, :descripcion, :precio, :stock, 1)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_usuario_responsable' => $_SESSION['id_usuario'],
        ':codigo_ref' => $datos['codigo_ref'],
        ':codigo_ubicacion' => $datos['codigo_ubicacion'],
        ':nombre' => $datos['nombre'],
        ':laboratorio' => $datos['laboratorio'],
        ':descripcion' => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
        ':precio' => $precio,
        ':stock' => $stock,
    ]);

    $idMedicamento = (int) $pdo->lastInsertId();
    $stmt = $pdo->prepare('SELECT * FROM medicamentos WHERE id_medicamento = :id_medicamento');
    $stmt->execute([':id_medicamento' => $idMedicamento]);
    $medicamento = $stmt->fetch();
    $medicamento['id_medicamento'] = (int) $medicamento['id_medicamento'];
    $medicamento['precio'] = (float) $medicamento['precio'];
    $medicamento['stock'] = (int) $medicamento['stock'];
    $medicamento['estado'] = (int) $medicamento['estado'];

    responder(201, ['success' => true, 'medicamento' => $medicamento]);
} catch (PDOException $error) {
    error_log($error->getMessage());
    if ($error->getCode() === '23000') {
        responder(409, ['success' => false, 'message' => 'El código de referencia ya está registrado o los datos no cumplen las relaciones de la base de datos.']);
    }
    responder(500, ['success' => false, 'message' => 'No se pudo guardar el medicamento. Inténtalo nuevamente.']);
}