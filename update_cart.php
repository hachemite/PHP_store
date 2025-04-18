<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION["loggedin"])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if(!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}

$response = ['success' => false];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? '';
    
    switch($action) {
        case 'remove':
            $product_id = $_POST["product_id"] ?? '';
            if($product_id && isset($_SESSION["cart"][$product_id])) {
                unset($_SESSION["cart"][$product_id]);
                $response = ['success' => true];
            }
            break;
            
        case 'clear':
            $_SESSION["cart"] = array();
            $response = ['success' => true];
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Invalid action'];
    }
}

echo json_encode($response);
?>