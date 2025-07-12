<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan.']);
  exit;
}

// Ambil data dari request
$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$client = trim($data['client'] ?? '');
$type = trim($data['type'] ?? '');
$budget = intval($data['budget'] ?? 0);
$status = trim($data['status'] ?? '');
$startDate = trim($data['startDate'] ?? '');
$endDate = trim($data['endDate'] ?? '');
$description = trim($data['description'] ?? '');

if (!$name || !$client || !$type || !$budget || !$status || !$startDate || !$endDate) {
  echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
  exit;
}

try {
  $stmt = $pdo->prepare("INSERT INTO projects (name, client, type, budget, status, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$name, $client, $type, $budget, $status, $startDate, $endDate, $description]);

  echo json_encode(['success' => true, 'message' => 'Proyek berhasil disimpan.']);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Gagal menyimpan proyek.']);
}
?>
