<?php

$mysql = new mysqli("localhost", "root", "", "product");

if($mysql->connect_error) die("Connection Error");