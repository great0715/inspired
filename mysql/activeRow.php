<?php
require_once("config.php");
require_once("functions.php");

global $dbMssql, $DB_NAME, $tblActiveRow;

// sql statement for creation table in database
// $sql_table = "CREATE TABLE IF NOT EXISTS active_row(
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     tbl_name VARCHAR(10),
//     row_id VARCHAR(10),
//     DATE VARCHAR(10)
//   )";
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //checking if final_data table exists in database
  // try {
  //   $db->query($sql_table);
  // } catch (Exception $err) {
  //   echo $err;
  // }
  //insert new data into database

  $tbl = $_POST["table"];
  $row_id = $_POST["row"];
  $date = $_POST["date"];

  $sql_select = "SELECT * FROM active_row WHERE date = '" . $date . "'";
  $sql_insert = "INSERT INTO active_row(tbl_name,row_id,date) VALUES ( ?, ? , ?)";
  $sql_update = "UPDATE active_row SET tbl_name = ? , row_id= ? WHERE date = ?";
  $resultSelect = sqlsrv_query($dbMssql, $sql_select, $params, $options);
  if ($resultSelect && sqlsrv_num_rows($resultSelect) > 0) {
    $stmtUpdate = sqlsrv_prepare($dbMssql, $sql_update, array($tbl, $row_id, $date));
    $resultUpdate =  sqlsrv_execute($stmtUpdate);
    if ($resultUpdate) {
      echo "Update Success";
    } else {
      echo "Update Failed!";
    }
  } else {
    $stmtUpdate = sqlsrv_prepare($dbMssql, $sql_insert, array($tbl, $row_id, $date));
    $resultInsert =  sqlsrv_execute($stmtUpdate);
    if ($resultInsert) {
      echo "Insert Success";
    } else {
      echo "Insert Failed!";
    }
  }
} else {
  $sql_read = "SELECT * from active_row";
  // if ($result = $db->query("SHOW TABLE STATUS FROM " . $DB_NAME . " LIKE 'active_row'")) {
    try {
      $output = array();

      if ($rr = sqlsrv_query($dbMssql, $sql_read, $params, $options)) {
        while ($item = sqlsrv_fetch_array($rr, SQLSRV_FETCH_ASSOC)) {
          $output[] = $item;
        }
        // mysqli_free_result($result);
        echo json_encode($output);
      }
    } catch (Exception $err) {
      echo $err;
    }
  // }
}