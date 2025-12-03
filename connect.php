<?php
    $host = "localhost";
    $user = "root";
    $password = "abha11228899";
    $dbName = "fitmanager";

    $connect = mysqli_connect($host,$user,$password,$dbName);

    if (!$connect) {
        die("Connection Failed".mysqli_connect_error());
    }
?>