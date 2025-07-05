<?php
// view_subscribers.php - Halaman admin untuk melihat daftar subscriber
require_once 'config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Ambil total data untuk pagination
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM newsletter_emails");
$total_stmt->execute();
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Ambil data subscriber
$stmt = $pdo->prepare("SELECT * FROM newsletter_emails ORDER BY subscribed_at DESC LIMIT ? OFFSET ?");
$stmt->bindParam(1, $limit, PDO::PARAM_INT);
$stmt->bindParam(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$subscribers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscribers - DevPro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .stat-box {
            background: linear-gradient(135deg, #00F5D0, #000000);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
            margin: 10px;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 2rem;
        }
        .stat-box p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        .status {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status.active {
            background-color: #d4edda;
            color: #155724;
        }
        .status.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #333;
        }
        .pagination a:hover {
            background-color: #00F5D0;
            color: white;
        }
        .pagination .current {
            background-color: #00F5D0;
            color: white;
        }
        .export-btn {
            background-color: #00F5D0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .export-btn:hover {
            background-color: #00d4b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Newsletter Subscribers</h1>
        
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo $total_records; ?></h3>
                <p>Total Subscribers</p>
            </div>
            <div class="stat-box">
                <h3><?php 
                    $active_stmt = $pdo->prepare("SELECT COUNT(*) FROM newsletter_emails WHERE status = 'active'");
                    $active_stmt->execute();
                    echo $active_stmt->fetchColumn();
                ?></h3>
                <p>Active Subscribers</p>
            </div>
            <div class="stat-box">
                <h3><?php 
                    $today_stmt = $pdo->prepare("SELECT COUNT(*) FROM newsletter_emails WHERE DATE(subscribed_at) = CURDATE()");
                    $today_stmt->execute();
                    echo $today_stmt->fetchColumn();
                ?></h3>
                <p>Today's Subscribers</p>
            </div>
        </div>

        <a href="export_subscribers.php" class="export-btn">Export to CSV</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $subscriber): ?>
                <tr>
                    <td><?php echo $subscriber['id']; ?></td>
                    <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                    <td><span class="status <?php echo $subscriber['status']; ?>"><?php echo ucfirst($subscriber['status']); ?></span></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($subscriber['subscribed_at'])); ?></td>
                    <td><?php echo htmlspecialchars($subscriber['ip_address']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'current' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>