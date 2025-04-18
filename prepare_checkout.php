<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION["loggedin"])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['cart'])) {
    $_SESSION['checkout_cart'] = array();
    foreach($data['cart'] as $item) {
        $_SESSION['checkout_cart'][$item['id']] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity']
        ];
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
}
?>