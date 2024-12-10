<?php
session_start();
$APP_TITLE = 'Process Visualisation';
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "123123";
$DB_NAME = "p2";
//$DB_HOST = "localhost";
//$DB_USER = "root";
//$DB_PASSWORD = "";
//$DB_NAME = "p1";


//Table Name
$tblSettings = 'settings';
$tblUsers = 'users';
$tblActiveRow='active_row';
$tblBuildAmount='build_amount';

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

$db = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit;
}

$g_shifts = array('shift1', 'shift2', 'shift3');

$current    = date('Y-m-d H:i:s');
$today      = date('Y-m-d');
$tomorrow   = date('Y-m-d', strtotime("+1 days"));
$yesterday  = date('Y-m-d', strtotime("-1 days"));
$weekToday  = date('N');

$STOCKING_AREAS = array(
    'System Fill',
    'Part Stocking',
    'Free Location'
);

$websocketKey = "eYiAbBPGoDi2mtsi3OQme5M7GIRAFDUz";
