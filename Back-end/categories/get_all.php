<?php
include_once "../connect.php";

header("Content-Type: application/json");




try {

    $columns = "*";

    getAllCustomData("categories", $columns, "1 LIMIT 1000 OFFSET 0");

} catch (\Throwable $th) {
    printFailure("An error occurred: " . $th->getMessage() . " on line " . $th->getLine());
}

?>