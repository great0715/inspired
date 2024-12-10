<?php
require_once("config.php");
require_once("functions.php");

global $dbMssql;
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// Execute SQL query to fetch data from the table
/** Get the latest value in the live table */
$sql = "SELECT TOP 1 value FROM live ORDER BY id DESC;";


$result = sqlsrv_query($dbMssql, $sql);
if ($result) {
  $data = sqlsrv_fetch_array($result, SQLSRV_FETCH_NUMERIC);
  echo json_encode($data);
}


// Check if any rows were returned
