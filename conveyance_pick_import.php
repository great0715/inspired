<?php
require_once 'PHPExcel/PHPExcel.php';
require_once 'PHPExcel/PHPExcel/IOFactory.php';
require_once("config.php");
require_once("functions.php");
if (0 < $_FILES['file']['error']) {
    echo 'Error: ' . $_FILES['file']['error'] . '<br>';
} else {
    $file = '' . $_FILES['file']['name'];
    $result = move_uploaded_file($_FILES['file']['tmp_name'], $file);
    if ($result) {
        //Import Excel to DB
        $excel = new PHPExcel();
        try {
            // load uploaded file
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $sheet = $objPHPExcel->getSheet(0);
            $total_rows = $sheet->getHighestRow();
            $highestColumn      = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $records = array();
            for ($row = 1; $row <= $total_rows; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $records[$row][$col] = $val;
                }
            }
            usort($records, function ($a, $b) {
                if ($a[14] > $b[14])
                    return 1;
                else if ($a[14] < $b[14])
                    return -1;
                else {
                    return strcmp($a[8], $b[8]);
                }
            });

            foreach ($records as $index => $row) {
                if ($index > 0 && $row[13] == 'T') {
                    $kanban = $row[8];
                    $location = $row[17];
                    $dolly_location = $row[4];
                    $part_number = $row[7];
                    $delivery_address = $row[10];
                    $address = get_pick_address_by_kanban_and_delivery($kanban, $delivery_address);
                    $dolly = get_dolly_by_kanban_and_delivery($kanban, $delivery_address);
                    $pick_seq = get_pick_seq_by_kanban_and_delivery($kanban, $delivery_address);
                    $cycle = $row[1];
                    $kanban_date = date("Y-m-d", DateTime::createFromFormat("Y-m-d", $row[0])->getTimestamp());
                    $query = "INSERT INTO {$tblConveyancePicks} (kanban, cycle, [address], [location], dolly, kanban_date, imported_at, dolly_location, part_number, delivery_address, completed_reason, delivered_reason, helped_at, pick_seq, is_pick)
								values('" . $kanban . "'," . $cycle . ",'" . $address . "','" . $location . "','" . $dolly . "','" . $kanban_date . "', GETDATE(), '" . $dolly_location . "','" . $part_number . "','" . $delivery_address . "', 0, 0, NULL, " . ($pick_seq ?: 0) . ", 0)";

                    sqlsrv_query($dbMssql, $query);
                }
            }
            echo 'Success';
	} catch (Exception $e) {
		var_dump($row);
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
}
