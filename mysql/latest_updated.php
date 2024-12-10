<?php

require_once("config.php");
require_once("functions.php");

global $dbMssql, $DB_NAME_MSSQL, $db, $DB_NAME;

// Execute SQL query to fetch data from the table
// $sql = "SHOW TABLE STATUS FROM " . $DB_NAME . " LIKE 'excel_pick_import'";
// $result = $db->query($sql);
// // Check if any rows were returned
// $latest_updated = mysqli_fetch_array($result);
// echo "<pre>";
// var_dump($latest_updated);
// exit;
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sql = "SELECT last_user_update as 'Update_time'
        FROM   sys.dm_db_index_usage_stats
        WHERE  database_id = db_id()
        AND object_id = object_id('excel_pick_import')";
$resultSelect = sqlsrv_query($dbMssql, $sql, $params, $options);
$dataLatestUpdated = sqlsrv_fetch_array($resultSelect, SQLSRV_FETCH_ASSOC);
$timeUpdate = "";
if($dataLatestUpdated && isset($dataLatestUpdated['Update_time'])){
    $result = $dataLatestUpdated['Update_time']->format('Y-m-d H:i:s');
}
$latest_updated["Update_time"] = $result;
// $result = sqlsrv_query($db,$sql);
// $latest_updated = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
    // $latest_updated = ["Update_time" => "2024-02-19 22:24:55"];
// get latest updated value from live page
// $sql_live = "SELECT value FROM live WHERE timestamp = (SELECT MAX(timestamp) FROM live)";
// $result_live = $db->query($sql_live);
// $result_live = sqlsrv_query($dbMssql,$sql_live);
