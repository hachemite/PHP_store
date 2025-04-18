<?php
// Initialize the session
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

// Check if cart has items
if(empty($_SESSION["checkout_cart"])) {
    header("location: cart.php");
    exit;
}

// Include database connection
require_once "config.php";

// Initialize variables
$error = '';
$success = '';
$user_id = $_SESSION["id"];

// Process checkout when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Calculate total
        $total = 0;
        foreach($_SESSION["checkout_cart"] as $item) {
            $total += $item["price"] * $item["quantity"];
        }
        
        // 1. Create the order record
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, status) 
                VALUES (:user_id, :total_amount, :shipping_address, 'pending')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":total_amount", $total, PDO::PARAM_STR);
        $stmt->bindParam(":shipping_address", $_POST["shipping_address"], PDO::PARAM_STR);
        $stmt->execute();
        
        $order_id = $pdo->lastInsertId();
        
        // 2. Add order items
        foreach($_SESSION["checkout_cart"] as $product_id => $item) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) 
                    VALUES (:order_id, :product_id, :quantity, :price)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
            $stmt->bindParam(":product_id", $product_id, PDO::PARAM_INT);
            $stmt->bindParam(":quantity", $item["quantity"], PDO::PARAM_INT);
            $stmt->bindParam(":price", $item["price"], PDO::PARAM_STR);
            $stmt->execute();
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Clear the cart
        unset($_SESSION["cart"]);
        unset($_SESSION["checkout_cart"]);
        
        $success = "Order placed successfully! Your order ID is: " . $order_id;
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = "Error processing your order: " . $e->getMessage();
    }
}

// Get user's default address
$default_address = '';
$sql = "SELECT address FROM users WHERE id = ?";
if($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    if($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $default_address = $row['address'] ?? '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="file.css">
</head>
<body>
    <div class="page-wrapper">
        <header>
            <div class="container">
                <h1>Checkout</h1>
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
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <p><a href="store.php" class="btn btn-primary">Continue Shopping</a></p>
                <?php else: ?>
                
                <div class="checkout-container">
                    <div class="checkout-summary">
                        <h3>Order Summary</h3>
                        <table class="checkout-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach($_SESSION["checkout_cart"] as $product_id => $item): 
                                    $subtotal = $item["price"] * $item["quantity"];
                                    $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item["name"]); ?></td>
                                    <td>$<?php echo number_format($item["price"], 2); ?></td>
                                    <td><?php echo $item["quantity"]; ?></td>
                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td>$<?php echo number_format($total, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="checkout-form">
                        <h3>Shipping Information</h3>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label>Shipping Address</label>
                                <textarea name="shipping_address" class="form-control" required><?php echo htmlspecialchars($default_address); ?></textarea>
                            </div>
                            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                    
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Place Order</button>
                            </div>
                        </form>
                    </div>
                </div>
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