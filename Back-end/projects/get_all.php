<?php
include_once "../connect.php";

header("Content-Type: application/json");

try {

    // Columns optimized for listing projects
    $columns = "id, slug, titleEn, titleAr, client_id, client_name, project_date, category, featured, imageUrl, demoUrl, codeUrl";
    // Only completed projects, featured first, then newest
    $whereClause = "status = 'COMPLETED' ORDER BY featured DESC, project_date DESC";

    getAllCustomData("projects", $columns, $whereClause, null, true);

} catch (\Throwable $th) {
    printFailure("An error occurred: " . $th->getMessage() . " on line " . $th->getLine());
}

?>
