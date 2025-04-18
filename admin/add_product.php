<?php
// Initialize the session
// Initialize the session
session_start();
 
// Check if the user is logged in and is admin, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true || !isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != true){
    header("location: ../login.php");
    exit;
}
// Include database connection
require_once "../config.php";

// Define variables and initialize with empty values
$name = $description = $price = $image_url = "";
$name_err = $description_err = $price_err = $image_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter product name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter product description.";
    } else {
        $description = trim($_POST["description"]);
    }
    
    // Validate price
    if(empty(trim($_POST["price"]))){
        $price_err = "Please enter product price.";
    } elseif(!is_numeric(trim($_POST["price"])) || floatval(trim($_POST["price"])) <= 0){
        $price_err = "Please enter a valid positive number for price.";
    } else {
        $price = floatval(trim($_POST["price"]));
    }
    
    // Handle image upload
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "webp" => "image/webp");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        
        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            $image_err = "Please select a valid image format (JPG, JPEG, PNG, GIF, WEBP).";
        }
        
        // Validate file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            $image_err = "Image size is larger than the allowed limit (5MB).";
        }
        
        // Check if file already exists
        $upload_dir = "../uploads/";
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $new_filename = uniqid() . "." . $ext;
        $upload_path = $upload_dir . $new_filename;
        
        // If no errors, proceed with upload
        if(empty($image_err)){
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)) {
                $image_url = "uploads/" . $new_filename;
            } else {
                $image_err = "There was an error uploading your file.";
            }
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($description_err) && empty($price_err) && empty($image_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO products (name, description, price, image_url) VALUES (:name, :description, :price, :image_url)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":description", $param_description, PDO::PARAM_STR);
            $stmt->bindParam(":price", $param_price);
            $stmt->bindParam(":image_url", $param_image_url, PDO::PARAM_STR);
            
            // Set parameters
            $param_name = $name;
            $param_description = $description;
            $param_price = $price;
            $param_image_url = $image_url;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Set success message and redirect to products page
                $_SESSION["success_message"] = "Product added successfully!";
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
                        <li><a href="users.php">Users</a></li>
                        <li><a href="../store.php">View Store</a></li>
                        <li><a href="../logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        
        <main>
            <div class="container">
                <div class="admin-header-actions">
                    <h2>Add New Product</h2>
                    <a href="index.php" class="btn btn-secondary">Back to Products</a>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="admin-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>    
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                        <span class="invalid-feedback"><?php echo $description_err; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" name="price" step="0.01" min="0" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                        <span class="invalid-feedback"><?php echo $price_err; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $image_err; ?></span>
                        <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF , WEBP. Max size: 5MB.</small>
                    </div>
                    
                    <div class="form-actions">
                        <input type="submit" class="btn btn-primary" value="Add Product">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
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