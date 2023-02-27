<?php

session_start();

require 'config/connection.php';
if($_SERVER['REQUEST_METHOD'] === "POST") {

    $total = $_POST['totalPrice'];
    $payment = $_POST['payment'];
    $comment = $_POST['comment'];
    $products = explode(',',$_POST['productIds']);
    $qty = explode(',',$_POST['productQty']);

    $mysql->query("INSERT INTO transactions (total,comment,paymentMethod) VALUES ('".$total."','".$comment."','".$payment."')") || die(mysqli_error($mysql));
    $lastId = $mysql->insert_id;
    var_dump($products);
    var_dump((int)$products[0]);
    var_dump($mysql->insert_id);
    for ($val = 0; $val < count($products); $val++) {
        $rest = $mysql->query("INSERT INTO transactionitems  (transactionId,productId,quantity) VALUES (".$lastId.",'".(int)$products[$val]."','".(int)$qty[$val]."')") || die(mysqli_error($mysql));
    }

    
}
unset($_SESSION['cart']);
$_SESSION['transactionSuccess'] = $lastId;

header("Location:index.php ");
