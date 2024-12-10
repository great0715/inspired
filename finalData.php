<?php
require_once("config.php");
require_once("functions.php");

// global $db, $DB_NAME, $tblFinalData;
global $DB_NAME, $tblFinalData, $dbMssql, $DB_NAME_MSSQL;

// sql statement for creation table in database
// $sql_table = "CREATE TABLE IF NOT EXISTS " . 
// 	$tblFinalData . 
// 	"(
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     container VARCHAR(10),
//     module VARCHAR(10),
//     userNm VARCHAR(10),
//     firstID VARCHAR(20),
//     secondID VARCHAR(20),
//     count VARCHAR(20),
//     liveTime VARCHAR(100),
//     liveBuild INT(10) UNSIGNED,
//     startNm VARCHAR(10),
//     startTime VARCHAR(15),
//     finishNm VARCHAR(10),
//     finishTime VARCHAR(15),
//     complete Boolean
//   )";
$sql_table = "CREATE TABLE  " .
  $tblFinalData .
  "(
    id identity (3153, 1)
    constraint PK_final_data_id
        primary key,
    container nvarchar(10)  default NULL,
    module nvarchar(10)  default NULL,
    userNm nvarchar(10)  default NULL,
    firstID nvarchar(20)  default NULL,
    secondID nvarchar(20)  default NULL,
    count nvarchar(20)  default NULL,
    liveTime nvarchar(100) default NULL,
    liveBuild bigint    default NULL,
    startNm nvarchar(10)  default NULL,
    startTime nvarchar(15)  default NULL,
    finishNm nvarchar(10)  default NULL,
    finishTime nvarchar(15)  default NULL,
    complete smallint      default NULL
  )";

function create_final_data($dbMssql, $sql_table, $data)
{
  //checking if final_data table exists in database
  $params = array();
  $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
  try {
    sqlsrv_query($dbMssql, $sql_table, $params, $options);
  } catch (Exception $err) {
    echo $err;
  }

  //insert new data into database
  $cont = $data["cont"];
  $mod = $data["mod"];
  $currentUser = $data["currentUser"];
  $firstID = $data["firstID"];
  $secondID = $data["secondID"];
  $count = (int) $data["count"];
  $liveTime = $data["liveTime"];
  $liveBuild = (int) $data["liveBuild"];
  $startID = $data["startInfo"]["userName"];
  $startTime = $data["startInfo"]["regTime"];
  $finishID = $data["finishInfo"]["userName"];
  $finishTime = $data["finishInfo"]["regTime"];
  $complete = (int) $data["complete"];

  $sql_select = "SELECT * FROM final_data WHERE firstID = '" . $firstID . "'";
  $sql_insert = "INSERT INTO final_data(
		container, 
		module, 
		userNm, 
		firstID, 
		secondID, 
		count, 
		liveTime, 
		liveBuild, 
		startNm, 
		startTime, 
		finishNm, 
		finishTime,
		complete) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $sql_update = "UPDATE final_data 
                 SET container = ?, module= ?, userNm= ?, count= ?, 
                    liveTime = ?, liveBuild = ?, startNm = ?, startTime = ?,finishNm = ?,
                    finishTime = ?, complete = ?, secondID = ? 
                  WHERE firstID = ?";

  $message = "Failed";
  $dataResult =sqlsrv_query($dbMssql, $sql_select, $params, $options);
  if ($dataResult && sqlsrv_num_rows($dataResult) > 0) {
    $stmtUpdate = sqlsrv_prepare($dbMssql, $sql_update, array($cont, $mod, $currentUser, $count, $liveTime, $liveBuild, $startID, $startTime, $finishID, $finishTime, $complete, $secondID, $firstID));
    $message = ($result = sqlsrv_execute($stmtUpdate)) ? "Update Succeeded!" : "Update Failed";
  } else {
    $stmtInsert = sqlsrv_prepare($dbMssql, $sql_insert, array($cont, $mod, $currentUser, $firstID, $secondID, $count, $liveTime, $liveBuild, $startID, $startTime, $finishID, $finishTime, $complete));
    $message = ($result = sqlsrv_execute($stmtInsert)) ? "Insert Succeeded!" : "Insert Failed";
  }

  echo $message;
}

function read_final_data($dbMssql, $db_name)
{
  $sql_read = "SELECT * from final_data";
  // if ($result = $db->query("SHOW TABLE STATUS FROM " . $db_name . " LIKE 'final_data'")) {
  try {
    $output = array();
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    if ($rr = sqlsrv_query($dbMssql, $sql_read, $params, $options)) {
      while ($item = sqlsrv_fetch_array($rr, SQLSRV_FETCH_ASSOC)) {
        $output[] = $item;
      }

      // mysqli_free_result($result);
      return json_encode($output);
    }
  } catch (Exception $err) {
    return $err;
  }
  // }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  create_final_data($dbMssql, $sql_table, $_POST['data']);
} else {
  echo read_final_data($dbMssql, $DB_NAME_MSSQL);
}