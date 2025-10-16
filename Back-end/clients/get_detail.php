<?php
include_once "../connect.php";

header("Content-Type: application/json");

try {

    $slug = $_GET['slug'];

    getAllCustomData("clients", "*", "slug = '$slug' ");

} catch (\Throwable $th) {
    printFailure("An error occurred: " . $th->getMessage() . " on line " . $th->getLine());
}

?>
