<?php
include_once "../connect.php";

header("Content-Type: application/json");

try {

    // Columns optimized for listing clients
    $columns = "id, slug, nameEn, nameAr, descriptionEn, descriptionAr, isActive, logoUrl, websiteUrl, is_featured";
    // Only active clients and order featured first
    $whereClause = "isActive = 1 ORDER BY is_featured DESC, id DESC";

    getAllCustomData("clients", $columns, $whereClause, null, true);

} catch (\Throwable $th) {
    printFailure("An error occurred: " . $th->getMessage() . " on line " . $th->getLine());
}













































?>
