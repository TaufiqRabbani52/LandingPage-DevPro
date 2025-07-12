<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
  $stmt = $pdo->query("
    SELECT 
      id,
      name,
      client,
      type,
      budget,
      status,
      start_date AS startDate,
      end_date AS endDate,
      description,
      created_at AS createdAt
    FROM projects
    ORDER BY created_at DESC
  ");

  $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['success' => true, 'data' => $projects]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Gagal mengambil data proyek.']);
}
?>
