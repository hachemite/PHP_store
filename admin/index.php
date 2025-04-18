<?php
// Initialize the session
session_start();
 
// Check if the user is logged in and is admin, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != true){
    header("location: ../login.php");
    exit;
}

// Include database connection
require_once "../config.php";
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../file.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="page-wrapper">
        <header class="admin-header">
            <div class="container">
                <h1>Admin Dashboard</h1>
                <nav>
                    <ul>
                        <li><a href="index.php" class="active">Products</a></li>
                        <li><a href="orders.php">Orders</a></li>
                        <li><a href="../store.php">View Store</a></li>
                        <li><a href="../logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <main>
            <div class="container">
                <div class="admin-header-actions">
                    <h2>Product Management</h2>
                    <a href="add_product.php" class="btn btn-primary">Add New Product</a>
                </div>
                
                <?php
                // Check for success message
                if(isset($_SESSION["success_message"])) {
                    echo '<div class="alert alert-success">' . $_SESSION["success_message"] . '</div>';
                    unset($_SESSION["success_message"]);
                }
                
                // Fetch all products
                try {
                    $stmt = $pdo->query("SELECT * FROM products ORDER BY id");
                    $products = $stmt->fetchAll();
                } catch(PDOException $e) {
                    echo '<div class="alert alert-danger">Error fetching products: ' . $e->getMessage() . '</div>';
                    $products = [];
                }
                ?>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($products)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No products found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <?php if(!empty($product['image_url'])): ?>
                                                <img src="<?php echo '../' . htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumbnail">
                                            <?php else: ?>
                                                <span class="no-image">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td class="description-cell"><?php echo htmlspecialchars($product['description']); ?></td>
                                        <td class="actions-cell">
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        
        <footer>
            <div class="container">
                <p>&copy; <?php echo date("Y"); ?> Admin Dashboard. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>