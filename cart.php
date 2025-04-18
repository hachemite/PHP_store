<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include database connection
require_once "config.php";

// Initialize cart if not set
if(!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}

// Process checkout if form submitted from cart
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["checkout"])) {
    // Prepare cart data for transfer
    $_SESSION["checkout_cart"] = $_SESSION["cart"];
    header("Location: checkout.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="file.css">
    <script src="cart.js" defer></script>
</head>
<body>
    <div class="page-wrapper">
        <header>
            <div class="container">
                <h1>Your Shopping Cart</h1>
                <nav>
                    <ul>
                        <li><a href="store.php">Products</a></li>
                        <li><a href="cart.php" class="active">Cart <span id="cart-count"><?php echo count($_SESSION["cart"]); ?></span></a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <main>
            <div class="container">
                <div id="cart-empty" style="display: <?php echo empty($_SESSION["cart"]) ? 'block' : 'none'; ?>;">
                    <p>Your cart is empty. <a href="store.php">Continue shopping</a>.</p>
                </div>
                
                <div id="cart-content" style="display: <?php echo empty($_SESSION["cart"]) ? 'none' : 'block'; ?>;">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <?php 
                                $total = 0;
                                foreach($_SESSION["cart"] as $product_id => $item): 
                                    $subtotal = $item["price"] * $item["quantity"];
                                    $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item["name"]); ?></td>
                                    <td>$<?php echo number_format($item["price"], 2); ?></td>
                                    <td><?php echo $item["quantity"]; ?></td>
                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-item" data-id="<?php echo $product_id; ?>">Remove</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Subtotal</td>
                                    <td id="cart-subtotal">$<?php echo number_format($total, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <div class="cart-actions">
                            <button type="button" id="clear-cart" class="btn btn-secondary">Clear Cart</button>
                            <button type="submit" name="checkout" id="checkout" class="btn btn-primary">Proceed to Checkout</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        
        <footer>
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> My Online Store. All rights reserved.</p>
            </div>
        </footer>
    </div>
    
    <!-- Confirmation modal for clearing cart -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <h3>Confirm Action</h3>
            <p>Are you sure you want to clear your shopping cart?</p>
            <div class="modal-actions">
                <button id="confirm-yes" class="btn btn-danger">Yes, clear cart</button>
                <button id="confirm-no" class="btn">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    // JavaScript to handle cart interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Remove item button
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    }
                });
            });
        });

        // Clear cart button
        document.getElementById('clear-cart').addEventListener('click', function() {
            document.getElementById('confirm-modal').style.display = 'block';
        });

        // Confirm clear cart
        document.getElementById('confirm-yes').addEventListener('click', function() {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=clear'
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        });

        // Cancel clear cart
        document.getElementById('confirm-no').addEventListener('click', function() {
            document.getElementById('confirm-modal').style.display = 'none';
        });
    });
    </script>
</body>
</html>