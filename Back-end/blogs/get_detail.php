<?php
include_once "../connect.php";

header("Content-Type: application/json");



  

try {
    $slug = $_GET['slug'];
    $columns = "*";
    getAllCustomData("blogs", $columns, "slug_ar = '$slug' OR slug_en = '$slug'");

} catch (\Throwable $th) {
    printFailure("An error occurred: " . $th->getMessage() . " on line " . $th->getLine());
}

?>