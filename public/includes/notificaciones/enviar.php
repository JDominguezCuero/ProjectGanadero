<?php
require_once '../../config/config.php';
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$id_receptor = $data['receptor_id'] ?? null;
$id_producto = $data['id_producto'] ?? null;
$mensaje = $data['mensaje'] ?? null;
$id_emisor = $_SESSION['usuario']['id_usuario'] ?? null;

if ($id_receptor && $mensaje && $id_emisor) {
    $stmt = $conexion->prepare("INSERT INTO notificaciones (id_usuario_emisor, id_usuario_receptor, mensaje, leido, fecha) VALUES (?, ?, ?, 0, NOW())");
    $stmt->execute([$id_emisor, $id_receptor, $mensaje]);

    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
}
