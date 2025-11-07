<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

// Verifica si la sesión está activa
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

session_start();
// y $_SESSION['usuario']['id_usuario'] debe estar definido
$id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
$mensaje = $_POST['mensaje'] ?? '';

if (!$id_usuario || empty($mensaje)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

try {
    $stmt = $conexion->prepare("INSERT INTO notificaciones (id_usuario, mensaje, leida, fecha) VALUES (?, ?, 0, NOW())");
    $stmt->execute([$id_usuario, $mensaje]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


?>
