<?php

session_start();
require 'config/connection.php';

$id = $_GET['id'];

if(empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$checkStock = $mysql->query("SELECT stock FROM products WHERE id = ".$id);

while($row = $checkStock->fetch_assoc()) {
    if($row['stock'] <= 0) {
        $_SESSION['message'] = "Stock is empty";
        header("location: index.php");
        exit();
    }
}

if(array_key_exists($id, $_SESSION['cart'])){
    $_SESSION['cart'][$id]++;
} else {
    $_SESSION['cart'][$id] = 1;
}

$mysql->query("UPDATE products SET stock = stock - 1 WHERE id = ".$id);

header("location: index.php");