<?php

session_start();
require 'config/connection.php';

$id = $_GET['id'];

$mysql->query("UPDATE products SET stock = stock + 1 WHERE id = ".$id);

if(array_key_exists($id, $_SESSION['cart'])){
    if((int)$_SESSION['cart'][$id] > 1) {
        $_SESSION['cart'][$id]--;
    } else {
        unset($_SESSION['cart'][$id]);
    }
} else {
    $_SESSION['cart'][$id] = 1;
}

header("location: index.php");