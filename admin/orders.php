<?php
session_start();

// Check admin status


require_once "../config.php";

// Fetch all orders with user info
$orders = [];
try {
    $stmt = $pdo->query("
        SELECT o.*, u.username, u.email 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC
    ");
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="../file.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .order-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .order-details {
            display: none;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
            margin-top: 5px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #FFC107; color: #000; }
        .status-processing { background: #17A2B8; color: #fff; }
        .status-shipped { background: #007BFF; color: #fff; }
        .status-completed { background: #28A745; color: #fff; }
        .status-cancelled { background: #DC3545; color: #fff; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header class="admin-header">
            <div class="container">
                <h1>Order Management</h1>
                <nav>
                    <ul>
                        <li><a href="index.php">Products</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li><a href="orders.php" class="active">Orders</a></li>
                        <li><a href="../store.php">View Store</a></li>
                        <li><a href="../logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <div class="container">
                <h2>All Orders</h2>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="orders-list">
                    <?php if(empty($orders)): ?>
                        <p>No orders found.</p>
                    <?php else: ?>
                        <?php foreach($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <strong>Order #<?php echo $order['id']; ?></strong>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?>
                                    </div>
                                    <div>
                                        <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                    </div>
                                    <button class="btn btn-sm btn-secondary toggle-details" 
                                            data-order="<?php echo $order['id']; ?>">
                                        View Details
                                    </button>
                                    <a href="orderstatus.php?order_id=<?php echo $order['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        Manage Status
                                    </a>
                                </div>

                                <div class="order-details" id="details-<?php echo $order['id']; ?>">
                                    <div class="customer-info">
                                        <h4>Customer Information</h4>
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                        <p><strong>Shipping Address:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                                    </div>

                                    <div class="order-items">
                                        <h4>Order Items</h4>
                                        <?php
                                        // Fetch order items with product images
                                        $stmt = $pdo->prepare("
                                            SELECT oi.*, p.name, p.image_url 
                                            FROM order_items oi
                                            JOIN products p ON oi.product_id = p.id
                                            WHERE oi.order_id = ?
                                        ");
                                        $stmt->execute([$order['id']]);
                                        $items = $stmt->fetchAll();

                                        foreach($items as $item): ?>
                                            <div class="order-item">
                                                <?php if(!empty($item['image_url'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         class="order-item-image">
                                                <?php else: ?>
                                                    <div class="order-item-image" style="background:#eee;display:flex;align-items:center;justify-content:center;">
                                                        No Image
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                                    <p>Price: $<?php echo number_format($item['price_at_purchase'], 2); ?></p>
                                                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                                                    <p>Subtotal: $<?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Toggle order details
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order');
            const detailsDiv = document.getElementById('details-' + orderId);
            detailsDiv.style.display = detailsDiv.style.display === 'block' ? 'none' : 'block';
            this.textContent = detailsDiv.style.display === 'block' ? 'Hide Details' : 'View Details';
        });
    });
    </script>
</body>
</html>