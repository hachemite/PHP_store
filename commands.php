<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database connection
require_once "config.php";

// Fetch user's previous commands (orders)
$user_id = $_SESSION['id'];
$orders = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND status != 'completed'");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}

// Handle cancel order request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_order_id"])) {
    $order_id = $_POST["cancel_order_id"];
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ? AND user_id = ? AND status != 'completed'");
        $stmt->execute([$order_id, $user_id]);
        $_SESSION['success_message'] = "Order cancelled successfully.";
        header("Location: commands.php");
        exit;
    } catch (PDOException $e) {
        $error = "Error cancelling order: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Commands</title>
    <link rel="stylesheet" href="file.css">
</head>
<body>
    <div class="page-wrapper">
        <header>
            <div class="container">
                <h1>Your Commands</h1>
                <nav>
                    <ul>
                        <li><a href="store.php">Products</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>

                <h2>Your Previous Commands</h2>
                <?php if (empty($orders)): ?>
                    <p>You have no pending or incomplete orders.</p>
                <?php else: ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="cancel_order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> My Online Store. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>