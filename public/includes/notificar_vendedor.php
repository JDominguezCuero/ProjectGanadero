<?php
require_once '.../../config/config.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_vendedor = $_POST['id_vendedor'] ?? null;
    $id_producto = $_POST['id_producto'] ?? null;
    $nombre_comprador = $_SESSION['usuario']['nombre'] ?? 'Un comprador';
    $id_emisor = $_SESSION['usuario']['id_usuario'] ?? null;

    if ($id_vendedor && $id_producto && $id_emisor) {
        $mensaje = "$nombre_comprador está interesado en tu producto ID $id_producto.";
        $sql = "INSERT INTO notificaciones (id_usuario_emisor, id_usuario_receptor, mensaje, leido, fecha) VALUES (?, ?, ?, 0, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_emisor, $id_vendedor, $mensaje]);

        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    }
}
?>
