<?php 

require "config/connection.php";
$rest = [];
$data;
$search = $_GET["search"];
if(empty($search) || is_null($search)) {
    // $data = $mysql->query("SELECT * FROM products");
    echo (json_encode($rest));
} else {
    $temp = "SELECT * FROM products WHERE title LIKE '%".$search."%'";
    $data = $mysql->query($temp);
    while($row = $data->fetch_assoc()) {
        $rest[] = $row;
    }
    
    echo (json_encode($rest));
}

?>