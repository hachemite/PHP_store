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

// Process delete operation after confirmation
if(isset($_GET["id"]) && !empty($_GET["id"])){
    // Get ID from URL
    $id = $_GET["id"];
    
    // First, get the product details to find image path if exists
    $sql = "SELECT image_url FROM products WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        $param_id = $id;
        
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                $image_url = $product["image_url"];
                
                // Delete the image file if it exists
                if(!empty($image_url) && file_exists("../" . $image_url)){
                    unlink("../" . $image_url);
                }
            } else {
                // Product not found
                $_SESSION["error_message"] = "No product found with this ID.";
                header("location: index.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            header("location: index.php");
            exit();
        }
    }
    unset($stmt);
    
    // Prepare a delete statement
    $sql = "DELETE FROM products WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
        
        // Set parameters
        $param_id = $id;
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Records deleted successfully
            $_SESSION["success_message"] = "Product deleted successfully!";
            header("location: index.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    unset($stmt);
    
    // Close connection
    unset($pdo);
} else{
    // No ID parameter provided
    $_SESSION["error_message"] = "No product ID specified.";
    header("location: index.php");
    exit();
}
?>