<?php
session_start();

// Check admin status


require_once "../config.php";

$order_id = $_GET['order_id'] ?? 0;

// Fetch order details
$order = null;
try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.username 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
} catch(PDOException $e) {
    $error = "Error fetching order: " . $e->getMessage();
}

// Process status update
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $new_status = '';
    
    if($action === 'update') {
        $new_status = $_POST['status'] ?? '';
    } elseif($action === 'cancel') {
        try {
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);

            $_SESSION['success_message'] = "Order cancelled and deleted successfully!";
            header("Location: orders.php");
            exit;
        } catch(PDOException $e) {
            $error = "Error deleting order: " . $e->getMessage();
        }
    }
    
    if(in_array($new_status, ['pending', 'processing', 'shipped', 'completed', 'cancelled'])) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            
            $_SESSION['success_message'] = "Order status updated successfully!";
            header("Location: orders.php");
            exit;
        } catch(PDOException $e) {
            $error = "Error updating order status: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Order Status</title>
    <link rel="stylesheet" href="../file.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .status-form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .status-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .order-summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header class="admin-header">
            <div class="container">
                <h1>Order Status Management</h1>
                <nav>
                    <ul>
                        <li><a href="index.php">Products</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li><a href="orders.php">Orders</a></li>
                        <li><a href="../store.php">View Store</a></li>
                        <li><a href="../logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <div class="container">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if($order): ?>
                    <div class="order-summary">
                        <h3>Order #<?php echo $order['id']; ?></h3>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                        <p><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                        <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                        <p><strong>Current Status:</strong> 
                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </p>
                    </div>

                    <div class="status-form">
                        <h3>Update Order Status</h3>
                        <form method="post">
                            <input type="hidden" name="action" value="update">
                            
                            <div class="form-group">
                                <label for="status">New Status:</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="status-actions">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                                <button type="submit" name="action" value="cancel" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to cancel this order?')">
                                    Cancel Order
                                </button>
                                <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Order not found.</div>
                    <a href="orders.php" class="btn btn-primary">Back to Orders</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>