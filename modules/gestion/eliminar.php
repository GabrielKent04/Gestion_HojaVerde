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
    responder(403, ['success' => false, 'message' => 'No tienes permiso para cambiar el estado de medicamentos.']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(405, ['success' => false, 'message' => 'Método no permitido.']);
}

$idMedicamento = $_POST['id_medicamento'] ?? null;
$estado = $_POST['estado'] ?? null;
if (!is_string($idMedicamento) || !preg_match('/^[1-9]\d*$/', $idMedicamento) || (float) $idMedicamento > 2147483647) {
    responder(422, ['success' => false, 'message' => 'El identificador del medicamento no es válido.']);
}
if (!is_string($estado) || !in_array($estado, ['0', '1'], true)) {
    responder(422, ['success' => false, 'message' => 'El estado solicitado no es válido.']);
}

require_once __DIR__ . '/../../config/conexion.php';

try {
    $stmt = $pdo->prepare(
        'UPDATE medicamentos SET estado = :estado WHERE id_medicamento = :id_medicamento'
    );
    $stmt->execute([
        ':estado' => (int) $estado,
        ':id_medicamento' => (int) $idMedicamento,
    ]);

    $stmt = $pdo->prepare('SELECT * FROM medicamentos WHERE id_medicamento = :id_medicamento');
    $stmt->execute([':id_medicamento' => (int) $idMedicamento]);
    $medicamento = $stmt->fetch();
    if (!$medicamento) {
        responder(404, ['success' => false, 'message' => 'No se encontró el medicamento indicado.']);
    }

    $medicamento['id_medicamento'] = (int) $medicamento['id_medicamento'];
    $medicamento['precio'] = (float) $medicamento['precio'];
    $medicamento['stock'] = (int) $medicamento['stock'];
    $medicamento['estado'] = (int) $medicamento['estado'];

    responder(200, ['success' => true, 'medicamento' => $medicamento]);
} catch (PDOException $error) {
    error_log($error->getMessage());
    responder(500, ['success' => false, 'message' => 'No se pudo cambiar el estado del medicamento. Inténtalo nuevamente.']);
}