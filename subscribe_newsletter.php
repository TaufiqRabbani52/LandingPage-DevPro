<?php
header('Content-Type: application/json');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan.'
    ]);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email tidak boleh kosong.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Format email tidak valid.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM newsletter_emails WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email sudah terdaftar.'
        ]);
        exit;
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO newsletter_emails (email, subscribed_at, status, ip_address) VALUES (?, NOW(), 'active', ?)");
    $stmt->execute([$email, $ip]);

    echo json_encode([
        'success' => true,
        'message' => 'Terima kasih! Email Anda berhasil didaftarkan.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan data.'
    ]);
}
?>
