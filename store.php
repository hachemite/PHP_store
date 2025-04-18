<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


echo "Welcome to our store, " . $_SESSION["is_admin"] . "!";
// Include database connection
require_once "config.php";

// Fetch products from database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id");
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    die("ERROR: Could not fetch products. " . $e->getMessage());
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store</title>
    <link rel="stylesheet" href="file.css">
    <script src="store.js" defer></script>
</head>
<body>
    <div class="page-wrapper">
    <header class="scandinavian-header">
    <div class="container">
        <div class="header-content">
            <div class="logo-container">
                <div class="garden-logo">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#5E7B65" d="M12,2C8,2 4,6 4,10c0,2.5 1.2,4.8 3,6.2V21h2v-4h2v4h2v-4h2v4h2v-4.8c1.8-1.4 3-3.7 3-6.2C20,6 16,2 12,2M12,4c3.3,0 6,2.7 6,6c0,1.6-0.7,3.1-1.8,4.2L14,13h-4l-2.2,1.2C6.7,13.1 6,11.6 6,10C6,6.7 8.7,4 12,4M10,9l1.5,1.5L10,12L8.5,10.5L10,9m4,0l1.5,1.5L14,12l-1.5-1.5L14,9z"/>
                    </svg>
                    <span>Nordic Garden Co.</span>
                </div>
            </div>
            

            
            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="store.php" class="active">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M12,3L2,12H5V20H19V12H22L12,3M12,7.7L16,11.2V18H14V14H10V18H8V11.2L12,7.7Z"/>
                            </svg>
                            <span>Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="cart.php">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7A2,2 0 0,1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                            </svg>
                            <span>Cart <span id="cart-count" class="cart-badge">0</span></span>
                        </a>
                    </li>
                    <li>
                        <a href="commands.php">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M12,2A10,10 0 1,0 22,12A10,10 0 0,0 12,2M11,17H13V15H11V17M11,13H13V7H11V13Z"/>
                            </svg>
                            <span>Commands</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                            </svg>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<style>

</style>
        <main>
            <div class="container">

            <div class="welcome-message">
                <p>Welcome back, <span class="username"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>!</p>
            </div>
                <h2>Our Products</h2>
                

                
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" data-id="<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-price">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                            <button class="btn add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
        
        <footer>
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> My Online Store. All rights reserved.</p>
            </div>
        </footer>
    </div>
    
    <div id="cart-notification" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="notification-message">Item added to cart!</p>
        </div>
    </div>
</body>
</html>