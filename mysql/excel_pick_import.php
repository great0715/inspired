<?php
require_once 'PHPExcel/PHPExcel.php';
require_once 'PHPExcel/PHPExcel/IOFactory.php';
require_once("config.php");
require_once("functions.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);

global $dbMssql, $DB_NAME;

// Import Devan

// SQL statement to create table
// $sql = "CREATE TABLE excel_pick_import (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     Container VARCHAR(60) NOT NULL,
//     Module VARCHAR(60) NOT NULL,
//     Qty_Boxes INT(20) UNSIGNED,
//     Stocking_Date DATE,
//     shift VARCHAR(20)
// )";

// //checking if excel_pick_import table exists in database
// if ($result = $db->query("SHOW TABLE STATUS FROM " . $DB_NAME . " LIKE 'excel_pick_import'")) {
//     if (mysqli_num_rows($result) > 0) {
//         echo "Table excel_pick_import already exists" . "<br/>";
//     } else {
//         $db->query($sql);
//         echo "Table excel_pick_import created successfully" . "<br/>";
//     }
// }

// // Create Table Import Pick

// // SQL statement to create table
// $sql_list = "CREATE TABLE excel_pick_list (
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     Container VARCHAR(30) NOT NULL,
//     Module VARCHAR(30) NOT NULL,
//     Part_number VARCHAR(20) NOT NULL,
//     Kanban VARCHAR(10) NOT NULL,
//     No_box INT(10) UNSIGNED,
//     is_complete BOOLEAN
// )";

// //checking if excel_pick_import table exists in database
// if ($result = $db->query("SHOW TABLE STATUS FROM " . $DB_NAME . " LIKE 'excel_pick_list'")) {
//     if (mysqli_num_rows($result) > 0) {
//         echo "Table excel_pick_list already exists" . "<br/>";
//     } else {
//         $db->query($sql_list);
//         echo "Table excel_pick_list created successfully" . "<br/>";
//     }
// }

/**
 * SQL statement to create build_table
 */

// $sql_build_amount = "CREATE TABLE IF NOT EXISTS build_amount(
//     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     tbl_name VARCHAR(10),
//     amount VARCHAR(10),
//     DATE VARCHAR(10)
//   )";

// //checking if excel_pick_import table exists in database
// if ($result = $db->query("SHOW TABLE STATUS FROM " . $DB_NAME . " LIKE 'build_amount'")) {
//     if (mysqli_num_rows($result) > 0) {
//         echo "Table build_amount already exists" . "<br/>";
//     } else {
//         $db->query($sql_list);
//         echo "Table build_amount created successfully" . "<br/>";
//     }
// }

// ==============================================================

if (0 < $_FILES['file']['error']) {
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
} else {
    $file = '' . $_FILES['file']['name'];
    $kind = $_POST['kind'];
    $result = move_uploaded_file($_FILES['file']['tmp_name'], $file);

    if ($result) {
        //Import Excel to DB
        $excel = new PHPExcel();
        try {
            // load uploaded file
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            if ($kind == "system") {
                $sheet = $objPHPExcel->getSheet(0);
                $total_rows = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $records = array();

                for (
                    $row = 2;
                    $row <= $total_rows;
                    ++$row
                ) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $sheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();
                        $records[$row][$col] = $val;
                    }
                }
                $query = "INSERT INTO excel_pick_import(Container, Module, Qty_Boxes, Stocking_Date, shift) VALUES (?, ?, ?, ?, ?) ";

                $insert_cnt = 0;
                foreach ($records as $index => $row) {
                    if ($index > 0) {
                        if ($row[6] && $row[7] && $row[8] && $row[9] && $row[11]) {
                            $Container = $row[6];
                            $Module = $row[7];
                            $Qty_Boxes = (int) $row[8];
                            $Stocking_Date = $row[9];
                            $shift = $row[11];
                            $search_query = "SELECT id FROM excel_pick_import where container = '{$Container}' AND module = '{$Module}' AND stocking_date = '{$Stocking_Date}'";
                            $result = sqlsrv_query($dbMssql, $search_query);
                            $row = $result ? sqlsrv_fetch_array($result, SQLSRV_FETCH_NUMERIC) : false;
                            if ($row && count($row) > 0) {
                                $id = $row[0];
                                $update_query = "UPDATE excel_pick_import SET qty_boxes = ?, shift = ? where id = ?";
                                $stmtUpdate = sqlsrv_prepare($dbMssql, $update_query, array($Qty_Boxes, $shift, $id));
                                $resultUpdate = sqlsrv_execute($stmtUpdate);
                            } else {
                                $queryParam = [$Container, $Module, $Qty_Boxes, $Stocking_Date, $shift];
                                print_r($queryParam);
                                echo "<br />";
                                $result = sqlsrv_query($dbMssql, $query, $queryParam);
                                if ($result === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $insert_cnt++;
                            }
                            // echo $query;

                            // if (
                            //     $db->query($query) === FALSE
                            // ) {
                            //     echo "Error: " . $query . "<br>";
                            // }
                        }
                    }
                }
            } else if ($kind == "pack") {
                // Pack List

                $sheet_list = $objPHPExcel->getSheet(0);
                $total_rows_list = $sheet_list->getHighestRow();
                $highestColumn_list = $sheet_list->getHighestColumn();
                $highestColumnIndex_list = PHPExcel_Cell::columnIndexFromString($highestColumn_list);
                $records_list = array();

                $sql_list_delete = "DELETE FROM excel_pick_list";
                $stmtDelete = sqlsrv_prepare($dbMssql, $sql_list_delete);
                $resultDelete = sqlsrv_execute($stmtDelete);
                // if (

                //     $db->query($sql_list_delete) === TRUE
                // ) {
                //     echo "All data deleted successfully";
                // } else {
                //     echo "Error deleting data: " . $db->error;
                // }

                for ($row = 2; $row <= $total_rows_list; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex_list; ++$col) {
                        $cell = $sheet_list->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();
                        $records_list[$row][$col] = $val;
                    }
                }

                foreach ($records_list as $index => $row) {
                    if ($index > 0) {
                        if ($row[1] && $row[2] && $row[3] && $row[4] && $row[7]) {
                            $Container = $row[1];
                            $Module = $row[2];
                            $Part_number = $row[3];
                            $Kanban = $row[4];
                            $No_box = (int) $row[7];
                            $query = "INSERT INTO excel_pick_list(Container, Module, Part_number, Kanban, No_box, is_complete) VALUES (?,?,?,?,?,?)";
                            $queryParam = [$Container, $Module, $Part_number, $Kanban, $No_box, 0];
                            sqlsrv_query($dbMssql, $query, $queryParam);
                            // if (
                            //     $db->query($query) === FALSE
                            // ) {
                            //     echo "Error: pick list - " . $query . "<br>";
                            // }
                        }
                    }
                }
            } else if ($kind == "build") {
                // Pack List

                $sheet_list = $objPHPExcel->getSheet(0);
                $total_rows_list = $sheet_list->getHighestRow();
                $highestColumn_list = $sheet_list->getHighestColumn();
                $highestColumnIndex_list = PHPExcel_Cell::columnIndexFromString($highestColumn_list);
                $records_list = array();
                $count = 0;

                $sql_list_delete = "DELETE FROM build_amount";
                $stmtDelete = sqlsrv_prepare($dbMssql, $sql_list_delete);
                $resultDelete = sqlsrv_execute($stmtDelete);
                // if (
                //     $db->query($sql_list_delete) === TRUE
                // ) {
                //     echo "All data deleted successfully";
                // } else {
                //     echo "Error deleting data: " . $db->error;
                // }

                for ($col = 1; $col <= $highestColumnIndex_list; $col += 3) {
                    $date_cell = $sheet_list->getCellByColumnAndRow($col, 1);
                    $day_shift_cell = $sheet_list->getCellByColumnAndRow($col, 2);
                    $night_shift_cell = $sheet_list->getCellByColumnAndRow(($col + 1), 2);

                    $date_cell_val = $date_cell->getValue();
                    $day_shift_cell_val = $day_shift_cell->getValue();
                    $night_shift_cell_val = $night_shift_cell->getValue();
                    $date_val = "";
                    $date_obj = explode("-", $date_cell_val);

                    if ($date_obj[1] == "Jan") {
                        $date_val = $date_obj[0] . "-01-" . date('Y');
                    } else if ($date_obj[1] == "Feb") {
                        $date_val = $date_obj[0] . "-02-" . date('Y');
                    } else if ($date_obj[1] == "Mar") {
                        $date_val = $date_obj[0] . "-03-" . date('Y');
                    } else if ($date_obj[1] == "Apr") {
                        $date_val = $date_obj[0] . "-04-" . date('Y');
                    } else if ($date_obj[1] == "May") {
                        $date_val = $date_obj[0] . "-05-" . date('Y');
                    } else if ($date_obj[1] == "Jun") {
                        $date_val = $date_obj[0] . "-06-" . date('Y');
                    } else if ($date_obj[1] == "Jul") {
                        $date_val = $date_obj[0] . "-07-" . date('Y');
                    } else if ($date_obj[1] == "Aug") {
                        $date_val = $date_obj[0] . "-08-" . date('Y');
                    } else if ($date_obj[1] == "Sep") {
                        $date_val = $date_obj[0] . "-09-" . date('Y');
                    } else if ($date_obj[1] == "Oct") {
                        $date_val = $date_obj[0] . "-10-" . date('Y');
                    } else if ($date_obj[1] == "Nov") {
                        $date_val = $date_obj[0] . "-11-" . date('Y');
                    } else if ($date_obj[1] == "Dec") {
                        $date_val = $date_obj[0] . "-12-" . date('Y');
                    }

                    $records_list[$count][0] = $date_val;
                    $records_list[$count][1] = $day_shift_cell_val;
                    $records_list[$count][2] = $night_shift_cell_val;
                    $count++;
                }

                foreach ($records_list as $index => $row) {
                    // if ($index > 0) {
                    if ($row[0] && $row[1] && $row[2]) {
                        $date = $row[0];
                        $day_amount = $row[1];
                        $night_amount = $row[2];
                        $day_query = "INSERT INTO build_amount(tbl_name, amount, date) VALUES ('day','" . $day_amount . "','" . $date . "')";
                        $night_query = "INSERT INTO build_amount(tbl_name, amount, date) VALUES ('night','" . $night_amount . "','" . $date . "')";

                        $queryParamDay = ['day', $day_amount, $date];
                        sqlsrv_query($dbMssql, $day_query, $queryParamDay);
                        $queryParamNight = ['night', $night_amount, $date];
                        sqlsrv_query($dbMssql, $night_query, $queryParamNight);

                        // if (
                        //     $day_query_status === FALSE
                        // ) {
                        //     echo "Error: day build amount - " . $day_query . "<br>";
                        // } else if (
                        //     $night_query_status === FALSE
                        // ) {
                        //     echo "Error: night build amount - " . $night_query . "<br>";
                        // }
                    }
                    // }
                }
            }

            echo 'Success';
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
}