<?php
session_start();
$APP_TITLE = 'Process Visualisation';
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "";
$DB_NAME = "pv";

$DB_HOST_MSSQL = "localhost";
$DB_USER_MSSQL = "sa";
$DB_PASSWORD_MSSQL = "sa";
$DB_NAME_MSSQL = "pv";

//Table Name
$tblSettings = 'settings';
$tblUsers = 'users';
$tblActiveRow = 'active_row';
$tblBuildAmount = 'build_amount';

$tblTag = 'tag';
$tblCycleSetting = 'cycle_setting';
$tblLive = 'live';
$tblOPRSetting = 'opr_setting';
$tblDriverSetting = 'driver_setting';
$tblFinalData = 'final_data';

$tblContainerDevan = 'container_devan';
$tblStocking = 'stocking';
$tblHistory = 'histories';
$tblScanLog = 'scan_log';
$tblParts = 'parts';
$tblHelpAlarm = 'help_alarm';
$tblConveyancePicks = 'conveyance_picks';
$tblDolly = 'dolly';
$tblReason = 'reason';
$tblPart2Kanban = 'part_to_kanban';
$tblOverstock = 'overstock';
$tblExcelPick = 'excel_pick_import';
$tblCompletedDate = "complete_list";

/**
 * MSSQL CONNECT 
 * */

$serverName = $DB_HOST_MSSQL;
$connectionOptions = array(
    "database" => $DB_NAME_MSSQL,
    "uid" => $DB_USER_MSSQL,
    "pwd" => $DB_PASSWORD_MSSQL
);

// function exception_handler($exception)
// {
//     echo "<h1>Failure</h1>";
//     echo "Uncaught exception: ", $exception->getMessage();
//     echo "<h1>PHP Info for troubleshooting</h1>";
//     phpinfo();
// }

// set_exception_handler('exception_handler');
$dbMssql = sqlsrv_connect($serverName, $connectionOptions);
if ($dbMssql === false) {
    die(formatErrors(sqlsrv_errors()));
}
/**
 * END MSSQL CONNECT 
 * */
$g_shifts = array('shift1', 'shift2', 'shift3');

$current = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime("+1 days"));
$yesterday = date('Y-m-d', strtotime("-1 days"));
$weekToday = date('N');

$STOCKING_AREAS = array(
    'System Fill',
    'Part Stocking',
    'Free Location'
);

function getLastInsertedId($con){
    $sql = "SELECT SCOPE_IDENTITY() AS LastInsertedId";
    $stmt = sqlsrv_query($con, $sql);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return $row['LastInsertedId'];
}


$websocketKey = "eYiAbBPGoDi2mtsi3OQme5M7GIRAFDUz";

function mssql_escape($str) {
    $str = (string) $str;
    return str_replace("'", "''", $str);
}