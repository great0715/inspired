<?php
require_once("config.php");
require_once("functions.php");

global $dbMssql, $DB_NAME, $tblBuildAmount;

// sql statement for creation table in database
// $sql_table = "CREATE TABLE IF NOT EXISTS build_amount(
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     tbl_name VARCHAR(10),
//     amount VARCHAR(10),
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
  $amount = $_POST["amount"];
  $date = $_POST["date"];

  $sql_select = "SELECT * FROM build_amount WHERE date = '" . $date . "' AND tbl_name = '" . $tbl . "'";
  $sql_insert = "INSERT INTO build_amount(tbl_name,amount,date) VALUES (?, ?, ?)";
  $sql_update = "UPDATE build_amount SET amount= ? WHERE date = ? AND tbl_name = ?";

  $dataSelect = sqlsrv_query($dbMssql, $sql_select, $params, $options);
  if ($dataSelect && sqlsrv_num_rows($dataSelect) > 0) {
    $stmtUpdate = sqlsrv_prepare($dbMssql, $sql_update, array($amount, $date, $tbl));
    $resultUpdate = sqlsrv_execute($stmtUpdate);
    if ($resultUpdate) {
      echo "Success";
    } else {
      echo "Failed!";
    }
  } else {
    $stmtInsert = sqlsrv_prepare($dbMssql, $sql_insert, array($tbl, $amount, $date));
    $resultInsert = sqlsrv_execute($stmtInsert);
    if ($resultInsert) {
      echo "Success";
    } else {
      echo "Failed!";
    }
  }
} else {
  $sql_read = "SELECT * from build_amount";
  // if ($result = $db->query("SHOW TABLE STATUS FROM " . $DB_NAME . " LIKE 'active_row'")) {

  try {
    $output = array();
    $rr = sqlsrv_query($dbMssql, $sql_read, $params, $options);

    if ($rr) {
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