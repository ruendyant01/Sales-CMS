<!DOCTYPE html>
<html lang="en">
    <?php
     require "./config/connection.php";
     session_set_cookie_params(0);
     session_start();
     header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
     header("Cache-Control: post-check=0, pre-check=0", false);
     header("Pragma: no-cache");
    //  session_destroy();
     $allDataRaw = $mysql->query("SELECT * FROM products");
     $allData = [];
     while($row = $allDataRaw->fetch_assoc()) {
        $allData[] = $row;
     }
    ?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v<?php echo time(); ?>"/>
    <script src="https://kit.fontawesome.com/d561733e28.js" crossorigin="anonymous"></script>
    <title>Sales</title>
</head>
<body>
    <main>
        <?php 
            $checkTransaction = false;
            if(isset($_SESSION['transactionSuccess'])) {
                $checkTransaction = true;
            }
        ?>
        <div class="transaction__result" id="modal-result" style="display: <?= ($checkTransaction ? 'block' : 'none') ?>">
            <button id="modal-button"><i class='fa-sharp fa-solid fa-xmark cross'></i></button>
            <h1>Success</h1>
            <p class="transaction__result--list">Product list</p>
            <table class="transaction__result--products">
                <thead>
                    <tr>
                        <th>Qty</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                    $totalPriceTrans = 0;
                    if(isset($_SESSION['transactionSuccess'])) {
                        $id = +$_SESSION['transactionSuccess'];
                        $query = "SELECT `transactions`.*, `products`.`title`,`products`.`price`,`products`.`discount`, `transactionitems`.`quantity`, `transactions`.`total` FROM `transactions` JOIN `transactionitems` ON `transactions`.`id` = `transactionitems`.`transactionId` JOIN `products` ON `products`.`id` = `transactionitems`.`productId` WHERE `transactionitems`.`transactionId` = ".$id;
                        $transacData = $mysql->query($query);
                        while($row = $transacData->fetch_assoc()) {
                            $totalPrice = $row['price'] * $row['quantity'];
                            $totalPriceDiscount = number_format($totalPrice - (($totalPrice * $row['discount']) /100),2,".",'');
                            $totalPriceTrans = $row['total'];
                            echo "
                                <tr>
                                    <td>{$row['quantity']}</td>
                                    <td>{$row['title']}</td>
                                    <td>\${$row['price']}</td>
                                    <td>{$row['discount']}%</td>
                                    <td>\${$totalPriceDiscount}</td>
                                </tr>
                            ";
                        }
                        unset($_SESSION['transactionSuccess']);
                    }
                ?> 
                </tbody>
            </table>
            <div class="sub-total">
                <p>Sub Total: </p>
                <p>$<?= $totalPriceTrans ?></p>
            </div>
        </div>
            <?php 
                if(isset($_SESSION['message'])) {
                    echo "<p class='error'>{$_SESSION['message']}</p>";
                    unset($_SESSION['message']);
                }
            ?> 
        <form class="search-bar" method="GET" onsubmit="searchBar(event)">
            <div class="icon-container pens">
                <i class="fa-regular fa-pen-to-square pens-icon"></i>
            </div>
            <input type="search" name="search" id="search" placeholder="Enter item name or scan barcode">
            <button class="icon-container cart" type="submit">
                <i class="fa-solid fa-cart-shopping cart-icon"></i>
                <p class="sale-text">Sale</p>
            </button>
            <div class="search__result" id="search-result">
            </div>
        </form>
        <section class="product-list">
            <div class="fixed_table">
                <div>
                    <table>
                    <thead>
                        <tr>
                        <th>-</th>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Disc</th>
                        <th>Total</th>
                        </tr>
                    </thead>
                    </table>
                <div class="body">
                    <table class="full_table">
                    <tbody>
                        <?php 
                            // var_dump($_SESSION['total']);
                        if(isset($_SESSION['cart'])) {
                            if(count($_SESSION['cart']) === 0 ) {
                                echo "<h1>There no items in the cart [Sales]</h1>";
                            } else {
                                foreach($allData as $row) {
                                    if(array_key_exists($row['id'], $_SESSION['cart'])) {
                                        $qty = $_SESSION['cart'][$row['id']];
                                        $totalPrice = $qty * $row['price'];
                                        $total = number_format($totalPrice - (($totalPrice * $row['discount']) /100),2,".","");
                                        echo "<tr>
                                            <td><a id='removeCartItem' href='removeCart.php?id={$row['id']}'><i class='fa-sharp fa-solid fa-xmark cross'></i></a></td>
                                            <td class='item__detail'>
                                                <div class='item__title'>
                                                    <p>{$row['title']}<p>
                                                </div>
                                                <div class='detail'>
                                                    <p>Supplier : {$row['supplier']}<p>
                                                    <p>Description : {$row['description']}<p>
                                                    <p>Category : {$row['category']}<p>
                                                    <p>UPC/EAN/ISBN : {$row['productCode']}<p>
                                                    <p>Stock : {$row['stock']}<p>
                                                    <p>Tax : Edit Taxes<p>
                                                </div>
                                                
                                            </td>
                                            <td>\${$row['price']}</td>
                                            <td>{$qty}</td>
                                            <td>{$row['discount']}%</td>
                                            <td>\${$total}</td>
                                        </tr>";
                                    }
                                }
                            }
                        }
                        ?>
                    </tbody>
                    </table>
                </div>
                </div>
        </section>
    </main>
    <form class="total" method="POST" action="checkout.php">
        <?php 
        $allTotal = 0;
            if(isset($_SESSION['cart'])) {
                if(count($_SESSION['cart']) > 0) {
                    foreach($allData as $val) {
                        if(array_key_exists($val['id'], $_SESSION['cart'])) {
                            $qty = $_SESSION['cart'][$val['id']];
                            $totalPrice = $qty * $val['price'];
                            $total = $totalPrice - (($totalPrice * $val['discount']) / 100);
                            $allTotal += number_format($total,2,".",'');
                        }
                    }
                }
            }
        ?>
        <div class="total__heading">
            <p>Sub Total: [<span>Edit Taxes</span>]</p>
            <span>$<?= $allTotal ?></span>
        </div>
        <div class="total__price">
            <div>
                <h4 class="title">Total</h4>
                <h4 class="total__price--total price">$<?= $allTotal ?></h4>
            </div>
            <div>
                <h4 class="title">Amount Due</h4>
                <h4 class="total__price--amount price">$<?= $allTotal ?></h4>
            </div>
        </div>
        <div class="total__payment">
            <p>Add Payment</p>
            <div>
                <div>
                    <input type="radio" id="cash" name="payment" value="Cash">
                    <label for="cash">Cash</label>
                </div>
                <div>
                    <input type="radio" id="check" name="payment" value="Check">
                    <label for="check">Check</label>
                </div>
                <div>

                    <input type="radio" id="wic" name="payment" value="WIC">
                    <label for="wic">WIC</label>
                </div>
                <div>

                    <input type="radio" id="gift" name="payment" value="Gift Card">
                    <label for="gift">Gift Card</label>
                </div>
                <div>

                    <input type="radio" id="debit" name="payment" value="Debit Card">
                    <label for="debit">Debit Card</label>
                </div>
                <div>

                    <input type="radio" id="credit" name="payment" value="Credit Card">
                    <label for="credit">Credit Card</label>
                </div>
            </div>
        </div>
        <input type="hidden" name="productIds" value="<?= (isset($_SESSION['cart']) ? implode(',',array_keys($_SESSION['cart'])) : [] ); ?>">
        <input type="hidden" name="productQty" value="<?= (isset($_SESSION['cart']) ? implode(',',($_SESSION['cart'])) : [] ); ?>">
        <div class="total__checkout">
            <input type="number" name="totalPrice" id="totalPrice" value="<?= $allTotal ?>" readonly>
            <button type="submit">Complete Sale</button>
        </div>
        <textarea name="comment" id="comment" cols="30" rows="10" placeholder="Comments"></textarea>
    </form>
    <script src="./index.js"></script>
</body>
</html>