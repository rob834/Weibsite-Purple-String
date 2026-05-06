<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
    http_response_code(403); echo json_encode(['error'=>'forbidden']); exit();
}
require_once __DIR__ . '/admin_clients_storage.php';

$id = $_POST['client_id'] ?? null;
$status = $_POST['status'] ?? null;
if (!$id || !$status) { http_response_code(400); echo json_encode(['error'=>'missing']); exit(); }

$clients = read_clients();
$idx = find_client($clients, $id);
if ($idx === -1) { http_response_code(404); echo json_encode(['error'=>'not found']); exit(); }

$clients[$idx]['status'] = $status;
write_clients($clients);
echo json_encode(['ok'=>true, 'client'=>$clients[$idx]]);

?>
