<?php
// export_subscribers.php - Export data subscriber ke CSV
require_once 'config.php';

// Set header untuk download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');

// Buat output stream
$output = fopen('php://output', 'w');

// Tulis header CSV
fputcsv($output, ['ID', 'Email', 'Status', 'Tanggal Daftar', 'IP Address']);

// Ambil semua data subscriber
$stmt = $pdo->prepare("SELECT * FROM newsletter_emails ORDER BY subscribed_at DESC");
$stmt->execute();

// Tulis data ke CSV
while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['id'],
        $row['email'],
        $row['status'],
        $row['subscribed_at'],
        $row['ip_address']
    ]);
}

// Tutup output stream
fclose($output);
exit;
?>