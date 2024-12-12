<?php

//Login check
function login_check()
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function convert_seconds_to_minutes($input_seconds)
{
    $seconds = $input_seconds % 60;
    $minutes = ($input_seconds - $seconds) / 60;
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    if ($seconds < 10) {
        $seconds = '0' . $seconds;
    }
    return $minutes . ":" . $seconds;
}

function make_time_string($time)
{
    if (strlen($time) < 5) {
        $time = "0" . $time;
    }
    return $time;
}

function convert_date_string($date)
{
    $string = explode("/", $date);
    return $string[2] . '-' . $string[1] . '-' . $string[0];
}

/*
 * Settings
 */

function get_setting($set_type)
{
    global $dbMssql, $tblSettings;
    $query
        = "SELECT TOP 1 * FROM {$tblSettings} WHERE set_type = '{$set_type}'";
    $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
    if ($result && sqlsrv_num_rows($result) > 0) {
        $setting = sqlsrv_fetch_object($result);
        return $setting->set_value;
    } else {
        return '';
    }
}

function get_build_amount()
{
    global $dbMssql, $tblLive, $tblTag;

    $query = "SELECT value FROM {$tblTag}";
    $result = sqlsrv_query($dbMssql, $query);
    $res = sqlsrv_fetch_object($result);
    $tag = $res->value;
    $query = "SELECT TOP 1 value FROM {$tblLive} WHERE [name] = '{$tag}' ORDER BY id DESC";

    $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
    if (sqlsrv_num_rows($result) > 0) {
        $amount = sqlsrv_fetch_object($result);
        return $amount->value;
    } else {
        return '';
    }
}

function get_cycle_setting()
{
    global $db, $tblCycleSetting;

    $query = "SELECT value FROM {$tblCycleSetting}";
    $result = $db->query($query);
    if ($result && mysqli_num_rows($result) > 0) {
        $cycle = mysqli_fetch_object($result);
        return $cycle->value;
    } else {
        return '';
    }
}

function update_setting($set_type, $set_value)
{
    global $dbMssql, $tblSettings;
    $old_setting = get_setting($set_type);
    if ($old_setting != '') {
        $sql
            = "UPDATE {$tblSettings} SET set_value = '{$set_value}' WHERE set_type = '{$set_type}'";
    } else {
        $sql
            = "INSERT INTO {$tblSettings} (set_value, set_type) VALUES ('{$set_value}', '{$set_type}')";
    }

    return sqlsrv_query($dbMssql, $sql);
}

function save_setting($post_data)
{
    $set_type = $post_data['set_type'];
    $set_value = $post_data['set_value'];
    $result = update_setting($set_type, $set_value);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Failed';
    }
}


/*
 * Shift Setting & get shift information
 */
function shift_setting($post_data)
{
    $settings['shift1']['start'] = make_time_string($post_data['shift1_start'])
        . ":00";
    $settings['shift1']['end'] = make_time_string($post_data['shift1_end'])
        . ":00";

    $settings['shift2']['start'] = make_time_string($post_data['shift2_start'])
        . ":00";
    $settings['shift2']['end'] = make_time_string($post_data['shift2_end'])
        . ":00";

    $settings['shift3']['start'] = make_time_string($post_data['shift3_start'])
        . ":00";
    $settings['shift3']['end'] = make_time_string($post_data['shift3_end'])
        . ":00";

    for ($i = 0; $i < 3; $i++) {
        $index = $i + 1;
        $settings['shift1']['breaks']['start' . $index]
            = make_time_string($post_data['shift1_break_start'][$i]) . ":00";
        $settings['shift1']['breaks']['end' . $index]
            = make_time_string($post_data['shift1_break_end'][$i]) . ":00";

        $settings['shift2']['breaks']['start' . $index]
            = make_time_string($post_data['shift2_break_start'][$i]) . ":00";
        $settings['shift2']['breaks']['end' . $index]
            = make_time_string($post_data['shift2_break_end'][$i]) . ":00";

        $settings['shift3']['breaks']['start' . $index]
            = make_time_string($post_data['shift3_break_start'][$i]) . ":00";
        $settings['shift3']['breaks']['end' . $index]
            = make_time_string($post_data['shift3_break_end'][$i]) . ":00";
    }

    $shift_setting = json_encode($settings, true);

    $set_type = $post_data['set_type'];
    $result = update_setting($set_type, $shift_setting);

    if ($result) {
        echo "ok";
    } else {
        echo "fail";
    }
    exit;
}

function get_current_shift()
{
    global $current;
    $datetime = $current;
    $shift_pattern = get_setting('Shift Pattern');
    $datetime_arr = explode(" ", $datetime);
    $date = $datetime_arr[0];
    $week_day = date('N', strtotime($date));
    $next_date = date("Y-m-d", strtotime("+1 days", strtotime($date)));
    $pre_date = date("Y-m-d", strtotime("-1 days", strtotime($date)));

    $shift_settings = get_setting($shift_pattern);

    if ($shift_settings != '') {
        $shifts = json_decode($shift_settings, true);
    } else {
        $string = file_get_contents("./shift.json");
        $shifts = json_decode($string, true);
    }

    if ($shift_pattern == '2 shifts') {
        if ($week_day == 5) { //Friday
            if ($datetime < $date . " " . $shifts['shift1']['start']) {
                $shift['shift'] = "shift2";
                $shift['date'] = $pre_date;
                $shift['start'] = $pre_date . " " . $shifts['shift3']['start'];
                $shift['end'] = $date . " " . $shifts['shift3']['end'];
            } else {
                if (
                    $datetime >= $date . " " . $shifts['shift1']['start']
                    && $datetime < $date . " " . $shifts['shift3']['start']
                ) {
                    $shift['shift'] = "shift1";
                    $shift['date'] = $date;
                    $shift['start'] = $date . " " . $shifts['shift1']['start'];
                    $shift['end'] = $date . " " . $shifts['shift1']['end'];
                } else {
                    $shift['shift'] = "shift2";
                    $shift['date'] = $date;
                    $shift['start'] = $date . " " . $shifts['shift3']['start'];
                    $shift['end'] = $next_date . " " . $shifts['shift3']['end'];
                }
            }
        } else {
            if ($datetime < $date . " " . $shifts['shift1']['start']) {
                $shift['shift'] = "shift2";
                $shift['date'] = $pre_date;
                $shift['start'] = $pre_date . " " . $shifts['shift2']['start'];
                $shift['end'] = $date . " " . $shifts['shift2']['end'];
            } else {
                if (
                    $datetime >= $date . " " . $shifts['shift1']['start']
                    && $datetime < $date . " " . $shifts['shift2']['start']
                ) {
                    $shift['shift'] = "shift1";
                    $shift['date'] = $date;
                    $shift['start'] = $date . " " . $shifts['shift1']['start'];
                    $shift['end'] = $date . " " . $shifts['shift1']['end'];
                } else {
                    $shift['shift'] = "shift2";
                    $shift['date'] = $date;
                    $shift['start'] = $date . " " . $shifts['shift2']['start'];
                    $shift['end'] = $next_date . " " . $shifts['shift2']['end'];
                }
            }
        }
    } else {
        if ($datetime < $date . " " . $shifts['shift1']['start']) {
            $shift['shift'] = "shift3";
            $shift['date'] = $pre_date;
            $shift['start'] = $pre_date . " " . $shifts['shift3']['start'];
            $shift['end'] = $date . " " . $shifts['shift3']['end'];
        } else {
            if (
                $datetime >= $date . " " . $shifts['shift1']['start']
                && $datetime < $date . " " . $shifts['shift2']['start']
            ) {
                $shift['shift'] = "shift1";
                $shift['date'] = $date;
                $shift['start'] = $date . " " . $shifts['shift1']['start'];
                $shift['end'] = $date . " " . $shifts['shift1']['end'];
            } else {
                if (
                    $datetime >= $date . " " . $shifts['shift2']['start']
                    && $datetime < $date . " " . $shifts['shift3']['start']
                ) {
                    $shift['shift'] = "shift2";
                    $shift['date'] = $date;
                    $shift['start'] = $date . " " . $shifts['shift2']['start'];
                    $shift['end'] = $date . " " . $shifts['shift2']['end'];
                } else {
                    $shift['shift'] = "shift3";
                    $shift['date'] = $date;
                    $shift['start'] = $date . " " . $shifts['shift3']['start'];
                    $shift['end'] = $next_date . " " . $shifts['shift3']['end'];
                }
            }
        }
    }

    return $shift;
}

function get_star_end_by_date_shift($date, $shift, $area)
{
    $shift_pattern = get_setting($area . ' Shift Pattern');
    $week_day = date('N', strtotime($date));
    $shift_settings = get_setting($shift_pattern);
    if ($shift_settings != '') {
        $shifts = json_decode($shift_settings, true);
    } else {
        $string = file_get_contents("./shift.json");
        $shifts = json_decode($string, true);
    }

    if ($week_day == 5) { //Friday
        if ($shift == 'shift2') {
            $start = $shifts['shift3']['start'];
            $end = $shifts['shift3']['end'];
        } else {
            $start = $shifts[$shift]['start'];
            $end = $shifts[$shift]['end'];
        }
    } else {
        $start = $shifts[$shift]['start'];
        $end = $shifts[$shift]['end'];
    }

    $data['start'] = $start;
    $data['end'] = $end;
    return $data;
}

/*
 * User
 */
function get_all_users()
{
    global $dbMssql, $tblUsers;
    $query = "SELECT * FROM {$tblUsers}";
    $result = sqlsrv_query($dbMssql, $query);

    $users = [];
    while ($user = sqlsrv_fetch_object($result)) {
        array_push(
            $users,
            [
                'user_id' => $user->ID,
                'username' => $user->username,
                'staff' => $user->staff,
                'type' => $user->type,
                'last_login' => $user->last_login,
            ]
        );
    }

    return $users;
}

function read_users()
{
    $users = get_all_users();
    foreach ($users as $user) {
        echo '<tr>';
        echo '<td>' . $user['username'] . '</td>';
        echo '<td>' . $user['staff'] . '</td>';
        if ($user['type'] == 1) {
            echo '<td><span style="color: red;">Administrator</span></td>';
        } else {
            echo '<td><span style="color: green;">User</span></td>';
        }

        if (!empty($user['last_login'])) {
            echo '<td>' . $user['last_login']->format('d/m/Y H:i:s')
                . '</td>';
        } else {
            echo '<td></td>';
        }
        echo '<td style="text-align: center;">';
        echo '<button class="btn btn-primary btn-sm edit-user" value="'
            . $user['user_id']
            . '" type="button"><i class="fas fa-edit"></i></button>&nbsp;';
        echo '<button class="btn btn-danger btn-sm delete-user" type="button" value="'
            . $user['user_id'] . '"><i class="fas fa-trash"></i></button>';
        echo '</td>';
        echo '</tr>';
    }
}

function get_user_info($user_id)
{
    global $dbMssql, $tblUsers;
    $query = "SELECT * FROM {$tblUsers} WHERE [ID] = {$user_id}";
    $result = sqlsrv_query($dbMssql, $query);
    return sqlsrv_fetch_object($result);
}

function read_user($post_data)
{
    $user_id = $post_data['user_id'];
    $user = get_user_info($user_id);
    echo json_encode($user, true);
}

function save_user($post_data)
{
    global $dbMssql, $tblUsers;
    $user_id = $post_data['user_id'] ? $post_data['user_id'] : 0;
    $username = $post_data['username'];
    $staff = $post_data['staff'];
    $user_type = $post_data['user_type'];
    $query = "";
    if ($user_id == 0) {
        $query
            = "INSERT INTO {$tblUsers}  ([username], [staff], [type]) value ('{$username}', '{$staff}', '{$user_type}')";
    } else {
        $query
            = "UPDATE {$tblUsers} SET [username] = '{$username}', [staff] = '{$staff}', [type] = '{$user_type}' WHERE [ID] = {$user_id}";
    }

    $result = sqlsrv_query($dbMssql, $query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function delete_user($post_data)
{
    global $dbMssql, $tblUsers;
    $user_id = $post_data['user_id'];
    $query = "DELETE FROM {$tblUsers} WHERE [ID] = {$user_id}";
    $result = sqlsrv_query($dbMssql, $query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function get_user_names($user_ids)
{
    $user_names = [];
    if (!empty($user_ids)) {
        $user_ids = explode(",", $user_ids);
        foreach ($user_ids as $user_id) {
            $user = get_user_info($user_id);
            array_push($user_names, $user->username);
        }
    }
    return implode(", ", $user_names);
}

/*
 * Stocking Lane Management
 */

function save_lane($post_data)
{
    global $db, $tblStocking, $STOCKING_AREAS;
    $area_index = $post_data['area_index'];
    $lane_id = $post_data['lane_id'];
    $lane_no = $post_data['lane_no'];
    $barcode_in = $post_data['barcode_in'];
    //$barcode_out = $post_data['barcode_out'];
    $barcode_out = '';
    $allocation = $post_data['allocation'];
    $height = $post_data['height'];
    $area = $STOCKING_AREAS[$area_index];
    if ($lane_id == 0) {
        $query = "INSERT INTO {$tblStocking}  (`lane_no`, `barcode_in`, `barcode_out`, `allocation`, `height`, `area`)
                    value ('{$lane_no}', '{$barcode_in}', '{$barcode_out}', '{$allocation}', '{$height}', '{$area}')";
    } else {
        $query = "UPDATE {$tblStocking} SET `lane_no` = '{$lane_no}', `barcode_in` = '{$barcode_in}', `barcode_out` = '{$barcode_out}',
                    `allocation` = '{$allocation}', `height` = '{$height}' WHERE `id` = {$lane_id}";
    }
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function get_all_lanes($area, $order = null)
{
    global $db, $tblStocking;
    if ($order == null) {
        $query = "SELECT * FROM {$tblStocking} WHERE `area` = '{$area}'";
    } else {
        $query = "SELECT * FROM {$tblStocking} WHERE `area` = '{$area}' "
            . $order;
    }
    $result = $db->query($query);
    $lanes = [];
    while ($lane = mysqli_fetch_object($result)) {
        array_push($lanes, $lane);
    }

    return $lanes;
}

function read_lanes($post_data)
{
    global $STOCKING_AREAS;
    $area_index = $post_data['area_index'];
    $area = $STOCKING_AREAS[$area_index];
    $lanes = get_all_lanes($area);
    echo '<table class="table table-bordered table-striped">';
    echo '<thead>';
    echo '<tr><th>Lane No.</th><th>Barcode</th><th>Allocation</th><th>Height</th><th></th></tr>';
    echo '</thead>';
    if (count($lanes) > 0) {
        foreach ($lanes as $lane) {
            echo '<tr>';
            echo '<td style="text-align: center;">' . $lane->lane_no . '</td>';
            echo '<td style="text-align: center;">' . $lane->barcode_in . '</td>';
            //echo '<td style="text-align: center;">'.$lane->barcode_out.'</td>';
            echo '<td style="text-align: center;">' . $lane->allocation . '</td>';
            echo '<td style="text-align: center;">' . $lane->height . '</td>';
            echo '<td style="text-align: center;">';
            echo '<button class="btn btn-primary btn-sm edit-lane" value="'
                . $lane->id
                . '" type="button"><i class="fas fa-edit"></i></button>&nbsp;';
            echo '<button class="btn btn-danger btn-sm delete-lane" type="button" value="'
                . $lane->id . '"><i class="fas fa-trash"></i></button>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5" style="text-align: center;">There is no lane yet.</td></tr>';
    }
}

function get_lane_by_id($lane_id)
{
    global $db, $tblStocking;
    $query = "SELECT * FROM {$tblStocking} WHERE `id` = {$lane_id}";
    $result = $db->query($query);
    return mysqli_fetch_object($result);
}

function get_lane($post_data)
{
    $lane_id = $post_data['lane_id'];
    $lane = get_lane_by_id($lane_id);
    echo json_encode($lane, true);
}

function delete_lane($post_data)
{
    global $db, $tblStocking;
    $lane_id = $post_data['lane_id'];
    $query = "DELETE FROM {$tblStocking} WHERE `id` = {$lane_id}";
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function set_stocking_action($post_data)
{
    $stocking_action = $post_data['stocking_action'];
    $_SESSION['stocking_action'] = $stocking_action;
    echo 'Success';
}

/*
 * Part Management
 */
function get_all_parts()
{
    global $db, $tblParts;
    $query = "SELECT * FROM {$tblParts} ORDER BY `part_no`";
    $result = $db->query($query);
    $parts = [];
    while ($part = mysqli_fetch_object($result)) {
        array_push($parts, $part);
    }
    return $parts;
}

function read_parts()
{
    $parts = get_all_parts();
    echo '<table class="table table-bordered table-striped">';
    echo '<thead>';
    echo '<tr><th>Part No.</th><th>Part Name</th><th>Amount</th><th>Lock</th><th>Levels</th><th></th></tr>';
    echo '</thead>';
    if (count($parts) > 0) {
        foreach ($parts as $part) {
            $ar = [];
            if ($part->sf) {
                $ar[] = "System Fill";
            }
            if ($part->ps) {
                $ar[] = "Part Stocking";
            }
            if ($part->fl) {
                $ar[] = "Free Location";
            }
            echo '<tr>';
            echo '<td style="text-align: center;">' . $part->part_no . '</td>';
            echo '<td style="text-align: center;">' . $part->part_name . '</td>';
            echo '<td style="text-align: center;">' . $part->amount . '</td>';
            echo '<td style="text-align: center;">' . implode(",", $ar) . '</td>';
            echo '<td style="text-align: center;">
                    <div class="row text-center justify-content-center">
                        <input type="text" class="form-control mr-2" id="low" name="low" placeholder="LOW" style="width:70px" readonly value="'
                . $part->level_low . '">
                        <input type="text" class="form-control" id="medium" name="medium" placeholder="MEDIUM" style="width:100px" readonly value="'
                . $part->level_medium . '">

                    </div>
                </td>';
            echo '<td style="text-align: center;">';
            echo '<button class="btn btn-primary btn-sm edit-part" value="'
                . $part->id
                . '" type="button"><i class="fas fa-edit"></i></button>&nbsp;';
            echo '<button class="btn btn-danger btn-sm delete-part" type="button" value="'
                . $part->id . '"><i class="fas fa-trash"></i></button>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6" style="text-align: center;">There is no part yet.</td></tr>';
    }
}

function save_part($post_data)
{
    global $db, $tblParts;
    $part_id = $post_data['part_id'] ? $post_data['part_id'] : 0;
    $part_no = $post_data['part_no'];
    $part_name = $post_data['part_name'];
    $amount = $post_data['amount'];
    $level_low = $post_data['level_low'];
    $level_medium = $post_data['level_medium'];
    $sf = $post_data['sf_check'] == 'true' ? 1 : 0;
    $ps = $post_data['ps_check'] == 'true' ? 1 : 0;
    $fl = $post_data['fl_check'] == 'true' ? 1 : 0;
    if ($part_id == 0) {
        $query = "INSERT INTO {$tblParts}  (`part_no`, `part_name`, `amount`, `sf`, `ps`, `fl`, `level_low`, `level_medium`)
                    value ('{$part_no}', '{$part_name}', '{$amount}', {$sf}, {$ps}, {$fl}, {$level_low}, {$level_medium})";
    } else {
        $query
            = "UPDATE {$tblParts} SET `part_no` = '{$part_no}', `part_name` = '{$part_name}', `amount` = '{$amount}', `sf` = '{$sf}', `ps` = '{$ps}', `fl` = '{$fl}', `level_low` = '{$level_low}', `level_medium` = '{$level_medium}' WHERE `id` = {$part_id}";
    }
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function get_part_by_id($id)
{
    global $db, $tblParts;
    $query = "SELECT * FROM {$tblParts} WHERE `id` = {$id}";
    $result = $db->query($query);
    return mysqli_fetch_object($result);
}

function get_part_by_no($no)
{
    global $db, $tblParts;
    $query = "SELECT * FROM {$tblParts} WHERE `part_no` = '{$no}' LIMIT 1";
    $result = $db->query($query);
    return mysqli_fetch_array($result);
}

function get_part($post_data)
{
    $part_id = $post_data['part_id'];
    $part = get_part_by_id($part_id);
    echo json_encode($part, true);
}

function delete_part($post_data)
{
    global $db, $tblParts;
    $part_id = $post_data['part_id'];
    $query = "DELETE FROM {$tblParts} WHERE `id` = {$part_id}";
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

/*
 * Stocking Input
 */
function read_barcode($post_data)
{
    global $db, $tblStocking, $tblScanLog, $_SESSION;
    $flag = false;
    $shift_id = $post_data['shift_id'];
    $shift_date = $post_data['shift_date'];
    $page = $post_data['page'];
    $user_id = $_SESSION['user']['user_id'];

    $part_barcodes = explode(",", $_POST['part']);
    $lane_barcodes = explode(",", $_POST['lane']);
    $data['error'] = '';
    $data['success'] = '';

    foreach ($part_barcodes as $index => $part_barcode) {
        if (strlen($part_barcode) > 10) {
            $part_barcode = substr($part_barcode, 10, 2);
        }
        $lane_barcode = $lane_barcodes[$index];
        $part = get_part_by_no($part_barcode);
        if ($part) {
            //Get Lane Information
            $query
                = "SELECT * FROM {$tblStocking} WHERE `barcode_in` = '{$lane_barcode}' LIMIT 1";
            $result = $db->query($query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_array($result);
                if ($row['area'] == 'Free Location') {
                    goto a;
                }
                if (
                    ($row['area'] == 'System Fill') && ($part['sf'] == 0)
                    || ($row['area'] == 'Part Stocking') && ($part['ps'] == 0)
                    || ($row['area'] == 'Free Location') && ($part['fl'] == 0)
                ) {
                    $data['error'] = "Part is locked for lane";
                } else {
                    $lane_id = $row['id'];
                    $allocation = $row['allocation'];
                    //Get left allocation
                    $query
                        = "SELECT * FROM {$tblScanLog} WHERE `lane_id` = '{$lane_id}' AND `booked_in` = 1 AND `booked_out` = 0";
                    $result = $db->query($query);
                    $allocated = mysqli_num_rows($result);
                    $left_allocation = $allocation - $allocated;
                    $stocking_action = $_SESSION['stocking_action'];
                    if ($stocking_action == 'in') {
                        if ($left_allocation > 0) {
                            $query = "INSERT INTO {$tblScanLog}  (`part`, `lane_id`, `booked_in`, `booked_out`, `page`, `shift`, `shift_date`, `user_id`, `booked_in_time`)
                                    value ('{$part_barcode}', '{$lane_id}', 1, 0, '{$page}', '{$shift_id}', '{$shift_date}', {$user_id}, NOW())";
                            $db->query($query);
                        } else {
                            $data['error'] = 'Lane allocation already was 0.';
                        }
                    } else {
                        $query
                            = "SELECT * FROM {$tblScanLog} WHERE `part` = '{$part_barcode}' AND `lane_id` = '{$lane_id}' AND `booked_in` = 1 AND `booked_out` = 0";
                        $result = $db->query($query);
                        $chk = mysqli_num_rows($result);
                        if ($chk > 0) {
                            $scanned = mysqli_fetch_object($result);
                            $update
                                = "UPDATE {$tblScanLog} SET `booked_out` = 1, `out_user_id` = {$user_id}, `booked_out_time` = NOW() WHERE id = {$scanned->id}";
                            $db->query($update);
                        } else {
                            $data['error'] = 'There is no scanned in lane';
                        }
                    }
                }
            } else {
                a:
                $flag = true;
                // In case of free location
                $ind_no = -1;
                $lane_str = strval($lane_barcode);
                foreach (str_split($lane_str) as $ind => $cha) {
                    if (is_numeric($cha)) {
                        $ind_no = $ind;
                        break;
                    }
                }
                $lane_str_prefix = substr($lane_str, 0, $ind_no);
                $lane_num = $lane_str_prefix . "1";
                $lane_index = intval(substr($lane_str, $ind_no));

                $query
                    = "SELECT * FROM {$tblStocking} WHERE `area` = 'Free Location' AND `barcode_in` = '{$lane_num}' LIMIT 1";
                $result = $db->query($query);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_array($result);

                    if (
                        ($row['area'] == 'System Fill') && ($part['sf'] == 0)
                        || ($row['area'] == 'Part Stocking')
                        && ($part['ps'] == 0)
                        || ($row['area'] == 'Free Location')
                        && ($part['fl'] == 0)
                    ) {
                        $data['error'] = "Part is locked for lane";
                    } else {
                        $lane_id = $row['id'];
                        $allocation = $row['height'];
                        //Get left allocation
                        $query
                            = "SELECT * FROM {$tblScanLog} WHERE `lane_id` = '{$lane_id}' AND `lane_id_fl` = '{$lane_index}' AND `booked_in` = 1 AND `booked_out` = 0";
                        $result = $db->query($query);
                        $allocation_done = mysqli_num_rows($result);
                        $stocking_action = $_SESSION['stocking_action'];
                        if ($stocking_action == 'in') {
                            if ($allocation_done < $allocation) {
                                $query = "INSERT INTO {$tblScanLog}  (`part`, `lane_id`, `lane_id_fl`, `booked_in`, `booked_out`, `page`, `shift`, `shift_date`, `user_id`, `booked_in_time`)
                                        value ('{$part_barcode}', '{$lane_id}', '{$lane_index}', 1, 0, '{$page}', '{$shift_id}', '{$shift_date}', {$user_id}, NOW())";
                                $db->query($query);
                            } else {
                                $data['error']
                                    = 'Lane allocation already was 0.';
                            }
                        } else {
                            $query
                                = "SELECT * FROM {$tblScanLog} WHERE `part` = '{$part_barcode}' AND `lane_id` = '{$lane_id}' AND `lane_id_fl` = '{$lane_index}' AND `booked_in` = 1 AND `booked_out` = 0";
                            $result = $db->query($query);
                            $chk = mysqli_num_rows($result);
                            if ($chk > 0) {
                                $scanned = mysqli_fetch_object($result);
                                $update
                                    = "UPDATE {$tblScanLog} SET `booked_out` = 1, `out_user_id` = {$user_id}, `booked_out_time` = NOW() WHERE id = {$scanned->id}";
                                $db->query($update);
                            } else {
                                $data['error'] = 'There is no scanned in lane';
                            }
                        }
                    }
                } else {
                    $data['error'] = 'Location barcode is incorrect.';
                }
            }
        } else {
            $data['error'] = 'Part No is incorrect.';
        }
        if ($data['error'] == '') {
            if ($_SESSION['stocking_action'] == "out") {
                if ($flag) {
                    $data['success'] = 'Part : ' . $part['part_no'] . '('
                        . $part['part_no'] . ') has been scanned out ' . $row['area']
                        . ', ' . $lane_barcode;
                } else {
                    $data['success'] = 'Part : ' . $part['part_no'] . '('
                        . $part['part_no'] . ') has been scanned out ' . $row['area']
                        . ', Lane' . $row['lane_no'];
                }
            } else {
                if ($flag) {
                    $data['success'] = 'Part : ' . $part['part_no'] . '('
                        . $part['part_no'] . ') has been scanned to ' . $row['area']
                        . ', ' . $lane_barcode;
                } else {
                    $data['success'] = 'Part : ' . $part['part_no'] . '('
                        . $part['part_no'] . ') has been scanned to ' . $row['area']
                        . ', ' . $row['lane_no'];
                }
            }
        }
    }

    $booked_in_out = get_booked_in_out($page, $shift_id, $shift_date);
    $data['booked_in'] = $booked_in_out['booked_in'];
    $data['booked_out'] = $booked_in_out['booked_out'];
    echo json_encode($data, true);
}

function get_booked_in_out($page, $shift_id, $shift_date)
{
    global $db, $tblScanLog, $_SESSION;
    $query
        = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `shift` = '{$shift_id}' AND `shift_date` = '{$shift_date}' AND `booked_in` = 1";
    $result = $db->query($query);
    $data['booked_in'] = mysqli_num_rows($result);
    $query
        = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `shift` = '{$shift_id}' AND `shift_date` = '{$shift_date}' AND `booked_out` = 1";
    $result = $db->query($query);
    $data['booked_out'] = mysqli_num_rows($result);
    return $data;
}

function get_filled_lane($lane_id)
{
    global $db, $tblScanLog;
    $query
        = "SELECT * FROM {$tblScanLog} WHERE `lane_id` = {$lane_id} AND `booked_in` = 1 AND `booked_out` = 0";
    $result = $db->query($query);
    $filled = [];
    while ($row = mysqli_fetch_object($result)) {
        $date_in = date('d/m/Y H:i:s', strtotime($row->booked_in_time));
        $user = get_user_info($row->user_id);
        array_push(
            $filled,
            [
                'part_no' => $row->part,
                'date_in' => $date_in,
                'member' => $user->username
            ]
        );
    }
    return $filled;
}

function read_scan_table($post_data)
{
    global $db, $tblScanLog;
    $shift_id = $post_data['shift_id'];
    $shift_date = $post_data['shift_date'];
    $page = $post_data['page'];
    $query
        = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `shift` = '{$shift_id}' AND `shift_date` = '{$shift_date}' ORDER BY `booked_in_time` ASC";
    $result = $db->query($query);
    while ($row = mysqli_fetch_object($result)) {
        echo '<tr>';
        echo '<td>' . $row->part . '</td>';
        $lane = get_lane_by_id($row->lane_id);
        echo '<td>' . $lane->area . '</td>';
        echo '<td>Lane ' . $lane->lane_no . '</td>';
        echo '<td style="color: green;">IN</td>';
        echo '<td>' . date('d/m/Y H:i:s', strtotime($row->booked_in_time))
            . '</td>';
        $user = get_user_info($row->user_id);
        echo '<td>' . $user->username . '</td>';
        echo '</tr>';

        if ($row->booked_out == 1) {
            echo '<tr>';
            echo '<td>' . $row->part . '</td>';
            echo '<td>' . $lane->area . '</td>';
            echo '<td>Lane ' . $lane->lane_no . '</td>';
            echo '<td style="color: red;">OUT</td>';
            echo '<td>' . date('d/m/Y H:i:s', strtotime($row->booked_out_time))
                . '</td>';
            $user = get_user_info($row->out_user_id);
            echo '<td>' . $user->username . '</td>';
            echo '</tr>';
        }
    }
}

function set_help_alarm($post_data)
{
    global $dbMssql, $tblHelpAlarm, $_SESSION;
    $page = $post_data['page'];
    $user_id = $_SESSION['user']['user_id'];
    {
        $sql
            = "INSERT INTO {$tblHelpAlarm}  (user_id, clicked_time, is_confirm, [page]) values ('{$user_id}', GETDATE(), 0, '{$page}') ";

        $result = sqlsrv_query($dbMssql, $sql);
        $help_id = getLastInsertedId($dbMssql);
    }

    if ($page == 'Container Devan') {
        $username = $_SESSION['user']['username'];
        echo '<h3>MEMBER: ' . $username . '</h3>';
        echo '<h3>TIME/DATE: ' . date('H:i d/m/y') . '</h3>';
        echo '<input type="hidden" id="help_alarm_id" value="' . $help_id . '">';
    } else {
        if ($page == 'Stocking') {
            $username = $_SESSION['user']['username'];
            echo '<h3>MEMBER: ' . $username . '</h3>';
            echo '<h3>TIME/DATE: ' . date('H:i d/m/y') . '</h3>';
            echo '<input type="hidden" id="help_alarm_id" value="' . $help_id
                . '">';
        } else {
            if ($page == 'Driver') {
                //        $username = $_SESSION['user']['username'];
                echo '<input type="hidden" id="help_alarm_id" value="' . $help_id
                    . '">';
            }
        }
    }
}

function get_help_alarm($post_data)
{
    global $db, $tblHelpAlarm;
    $page = $post_data['page'];
    // $query = "SELECT * FROM {$tblHelpAlarm} WHERE `is_confirm` = 0 AND page ='{$page}' LIMIT 1";
    $query = "SELECT * FROM {$tblHelpAlarm} WHERE `is_confirm` = 0 LIMIT 1";
    $result = $db->query($query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_object($result);
        $user = get_user_info($row->user_id);
        echo '<h3>ANDON HELP</h3>';
        echo '<h3>MEMBER: ' . $user->username . '</h3>';
        echo '<h3>TIME: ' . date('d/m/Y H:i:s', strtotime($row->clicked_time))
            . '</h3>';
        echo '<input type="hidden" id="confirm_help_alarm_id" value="' . $row->id
            . '">';
    } else {
        echo 'NO HELP';
    }
}

function get_help_alarm_for_overview($page)
{
    global $db, $tblHelpAlarm;
    $query
        = "SELECT * FROM {$tblHelpAlarm} WHERE `is_confirm` = 0 AND `page` = '{$page}' LIMIT 1";
    $result = $db->query($query);
    if (mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}


function confirm_help_alarm($post_data)
{
    global $dbMssql, $tblHelpAlarm, $_SESSION;
    $alarm_id = $post_data['alarm_id'];
    $query
        = "UPDATE {$tblHelpAlarm} SET is_confirm = 1, turnoff_time = GETDATE() WHERE id = {$alarm_id}";
    sqlsrv_query($dbMssql, $query);
}

function get_overview_screen()
{
    global $STOCKING_AREAS;
    foreach ($STOCKING_AREAS as $index => $area) {
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        if ($index == 0) {
            echo '<div class="card card-primary">';
            $cell_bg = '#007bff';
        } else {
            if ($index == 1) {
                echo '<div class="card card-success">';
                $cell_bg = '#28a745';
            } else {
                echo '<div class="card card-warning">';
                $cell_bg = '#ffc107';
            }
        }

        echo '<div class="card-header">';
        echo '<h3 class="card-title">' . $area . '</h3>';
        echo '</div>';

        echo '<div class="card-body">';
        echo '<div style="overflow-x: auto; white-space: nowrap;">';
        $lanes = get_all_lanes($area);
        foreach ($lanes as $lane) {
            $allocation = $lane->allocation;
            $height = $lane->height;

            if ($allocation % $height != 0) {
                $remainder = $height - ($allocation % $height);
                $rows = (int) ($allocation / $height) + 1;
            } else {
                $remainder = 0;
                $rows = (int) ($allocation / $height);
            }
            $height_px = (int) 140 / $height;
            $total_width = $rows * $height_px;

            $filled = get_filled_lane($lane->id);
            echo '<div style="width: auto; display: inline-block;">';
            echo '<h5 style="font-size:16px; text-align: center;">Lane'
                . $lane->lane_no . ' Filled. ' . count($filled) . '/' . $lane->allocation
                . '</h5>';
            echo '<table style="width: auto; margin: 10px;" class="float-left">';
            for ($i = 1; $i <= $height; $i++) {
                echo '<tr>';
                for ($c = 1; $c <= $rows; $c++) {
                    $td_index = $height * ($c - 1) + ($height - $i);
                    if ($remainder >= $i && $c == $rows) {
                        echo '<td style="background-color: #FFFFFF; border: 0; width: 50px; height: 50px;">&nbsp;</td>';
                    } else {
                        if (isset($filled[$td_index])) {
                            echo '<td class="has-details" style="background-color: '
                                . $cell_bg
                                . '; border: 1px solid #a5a3a3; width: 50px; height: 50px;">';
                            echo '<span>&nbsp;</span>';
                            echo '<span class="details" style="width: 300px;">';
                            echo 'Part No: ' . $filled[$td_index]['part_no']
                                . '<br/>';
                            echo 'Location: ' . $lane->barcode_in . '<br/>';
                            echo 'Date IN: ' . $filled[$td_index]['date_in']
                                . '<br/>';
                            echo 'Member: ' . $filled[$td_index]['member'];
                            echo '</span>';
                        } else {
                            echo '<td style="background-color: #FFFFFF; border: 1px solid #a5a3a3; width: 50px; height: 50px;">';
                            echo '&nbsp;';
                            echo '</td>';
                        }
                    }
                }
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>'; // card body

        echo '</div>'; // card
        echo '</div>'; // col-md-12
        echo '</div>'; // row
    }
}

function read_history($post_data)
{
    global $db, $tblScanLog;
    $from_date = convert_date_string($post_data['from_date']);
    $start = $from_date . " 00:00:00";
    $to_date = convert_date_string($post_data['to_date']);
    $end = $to_date . " 23:59:59";

    $query
        = "SELECT * FROM {$tblScanLog} WHERE booked_in_time BETWEEN '{$start}' AND  '{$end}' ORDER BY `booked_in_time` ASC";
    $result = $db->query($query);
    echo '<table id="history_table" class="table table-bordered table-striped dataTable dtr-inline">';
    echo '<thead>';
    echo '<th>Location</th>';
    echo '<th>Lane</th>';
    echo '<th>Part number</th>';
    echo '<th>Timestamp</th>';
    echo '<th>Member</th>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = mysqli_fetch_object($result)) {
        $lane = get_lane_by_id($row->lane_id);
        $user = get_user_info($row->user_id);
        echo '<tr>';
        echo '<td>' . $lane->barcode_in . '</td>';
        echo '<td>Lane ' . $lane->lane_no . '</td>';
        echo '<td>' . $row->part . '</td>';
        echo '<td><span style="display: none;">' . $row->booked_in_time . '</span>'
            . date('d/m/Y H:i:s', strtotime($row->booked_in_time)) . '</td>';
        echo '<td>' . $user->username . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

function read_kanban_list()
{
    global $db;
    $updateQuery
        = "UPDATE `excel_pick_list` JOIN `final_data` ON `excel_pick_list`.Container = `final_data`.Container SET `excel_pick_list`.is_complete = `final_data`.complete WHERE `excel_pick_list`.Module = `final_data`.Module";
    $db->query($updateQuery);

    $query = "SELECT * FROM `excel_pick_list` GROUP BY Kanban";
    $result = $db->query($query);
    echo '<table id="kanban_table" class="table table-bordered table-striped dataTable dtr-inline">';
    echo '<thead>';
    echo '<th>Kanban</th>';
    echo '<th>Stock</th>';
    echo '<th>Part number</th>';
    echo '<th>Max</th>';
    echo '<th>Min</th>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = mysqli_fetch_object($result)) {
        $itemQuery = "SELECT * FROM `excel_pick_list` WHERE Kanban = '"
            . $row->Kanban . "' AND is_complete = 1";
        $kanbanQuery = "SELECT * FROM `part_to_kanban` WHERE Kanban = '"
            . $row->Kanban . "'";
        $itemResult = $db->query($itemQuery);
        $kanbanResult = $db->query($kanbanQuery);

        $stock = 0;
        $max = '';
        $min = '';
        while ($rowItem = mysqli_fetch_object($itemResult)) {
            $stock += (int) $rowItem->No_box;
        }

        $deliveryQuery
            = "SELECT * FROM `conveyance_picks` WHERE kanban = '{$row->Kanban}' AND is_delivered = 1";
        $deliveryResult = $db->query($deliveryQuery);
        $deliveryCount = mysqli_num_rows($deliveryResult);
        $stock = $stock - $deliveryCount;

        if ($stock < 0) {
            $stock = 0;
        }

        if (mysqli_num_rows($kanbanResult) > 0) {
            $obj = mysqli_fetch_object($kanbanResult);
            $max = $obj->max > 0 ? $obj->max : "";
            $min = $obj->min > 0 ? $obj->min : "";
        }

        echo '<tr>';
        echo '<td>' . $row->Kanban . '</td>';
        echo '<td>' . $stock . '</td>';
        echo '<td>' . $row->Part_number . '</td>';
        echo '<td>' . $max . '</td>';
        echo '<td>' . $min . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}

function read_stock_level()
{
    global $db;

    $updateQuery
        = "UPDATE `excel_pick_list` JOIN `final_data` ON `excel_pick_list`.Container = `final_data`.Container SET `excel_pick_list`.is_complete = `final_data`.complete WHERE `excel_pick_list`.Module = `final_data`.Module";
    $db->query($updateQuery);

    // $query = "SELECT a.*, b.* FROM `excel_pick_list` AS a LEFT JOIN `part_to_kanban` AS b ON a.`Kanban` = b.`kanban` WHERE (a.`No_box` < b.`min` OR a.`No_box` > b.`max`) AND a.`is_complete` = 1 GROUP BY a.`Kanban`";
    $query
        = "SELECT a.*, b.*,c.* FROM `excel_pick_list` AS a LEFT JOIN `part_to_kanban` AS b ON a.`Kanban` = b.`kanban` LEFT JOIN `final_data` AS c ON a.`Container` = c.`container` WHERE a.`Module` = c.`module` AND (a.`No_box` < b.`min` OR a.`No_box` > b.`max`) AND a.`is_complete` = 1 GROUP BY a.`Kanban`";
    $result = $db->query($query);
    $minContent = "";
    $maxContent = "";
    while ($row = mysqli_fetch_object($result)) {
        $stock = 0;
        $itemQuery = "SELECT * FROM `excel_pick_list` WHERE Kanban = '"
            . $row->Kanban . "' AND is_complete = 1";
        $itemResult = $db->query($itemQuery);

        while ($rowItem = mysqli_fetch_object($itemResult)) {
            $stock += (int) $rowItem->No_box;
        }

        $deliveryQuery
            = "SELECT * FROM `conveyance_picks` WHERE kanban = '{$row->Kanban}' AND is_delivered = 1";
        $deliveryResult = $db->query($deliveryQuery);
        $deliveryCount = mysqli_num_rows($deliveryResult);
        $stock = $stock - $deliveryCount;
        $deliveryResult = mysqli_fetch_object($deliveryResult);
        if ($row->min != 0 && $row->max != 0) {
            $inName = str_replace(' ', '&nbsp;', $row->finishNm);
            $inTime = str_replace(' ', '&nbsp;', $row->finishTime);
            $outName = str_replace(
                ' ',
                '&nbsp;',
                isset($deliveryResult) ? $deliveryResult->delivered_user : ""
            );
            $outTime = str_replace(
                ' ',
                '&nbsp;',
                isset($deliveryResult) ? $deliveryResult->deliveried_at : ""
            );
            if ($stock < $row->min) {
                $minContent = $minContent . "<div class='low-item' onclick=kanban_detail('{$row->Kanban}','{$stock}/{$row->min}','{$inName}','{$inTime}','{$outName}','{$outTime}','{$deliveryCount}')>
                                        <h2>{$row->Kanban}</h2>
                                        <h2>{$stock}/{$row->min}</h2>
                                    </div>";
            } else {
                if ($stock > $row->max) {
                    $maxContent = $maxContent . "<div class='high-item' onclick=kanban_detail('{$row->Kanban}','{$stock}/{$row->max}','{$inName}','{$inTime}','{$outName}','{$outTime}','{$deliveryCount}')>
                                        <h2>{$row->Kanban}</h2>
                                        <h2>{$stock}/{$row->max}</h2>
                                    </div>";
                }
            }
        }
    }
    $arr = [
        'min' => strlen($minContent) > 0 ? $minContent : "No data",
        'max' => strlen($maxContent) > 0 ? $maxContent : "No data"
    ];
    echo json_encode($arr);
}


/*
 * Container Devan
 */

function get_container_devan($start, $end)
{
    global $dbMssql, $tblContainerDevan;
    $query
        = "SELECT * FROM {$tblContainerDevan} WHERE [date] BETWEEN  '{$start}' AND '{$end}' ORDER BY [date] ASC, [time] ASC";
    $result = sqlsrv_query($dbMssql, $query);
    $items = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $items[] = $row;
    }

    return $items;
}

function update_container_devan($post_data)
{
    global $dbMssql, $tblContainerDevan;
    $value = $post_data['value'];
    $field = $post_data['field'];
    $row_id = $post_data['row_id'];
    $update_query
        = "UPDATE {$tblContainerDevan} SET [{$field}] = '{$value}' WHERE id = {$row_id}";
    $result = sqlsrv_query($dbMssql, $update_query);
    if ($result) {
        echo 'Success';
    } else {
        echo 'Failed';
    }
}

function complete_container_devan($post_data)
{
    global $dbMssql, $tblContainerDevan, $_SESSION;

    $row_id = $post_data['row_id'];
    $user_id = $_SESSION['user']['user_id'];
    $revan_state = $post_data['renban'];

    if ($revan_state == 'revan') {
        $update_query
            = "UPDATE {$tblContainerDevan} SET revan_state = '{$revan_state}' WHERE id = {$row_id}";
    } else {
        $update_query
            = "UPDATE {$tblContainerDevan} SET is_completed = 1, completed_at = GETDATE(), completed_by = '{$user_id}', revan_state = 'completed' WHERE id = {$row_id}";
    }

    $result = sqlsrv_query($dbMssql, $update_query);

    if ($result) {
        $query = "SELECT * FROM {$tblContainerDevan} WHERE id = {$row_id}";
        $r = sqlsrv_query($dbMssql, $query);
        $row1 = [];
        while ($rt = sqlsrv_fetch_array($r, SQLSRV_FETCH_ASSOC)) {
            $row1[] = $rt;
        }

        $pre_fix = get_setting('renban_no_prefix');

        if ($row1['revan_state'] == 'scheduled') {
            $str_pre_fix = strval($pre_fix);
            $ind_no = -1;
            foreach (str_split($str_pre_fix) as $ind => $cha) {
                if (is_numeric($cha)) {
                    $ind_no = $ind;
                    break;
                }
            }
            $new_pre_fix_str = substr($str_pre_fix, 0, $ind_no + 1);
            $new_pre_fix_num = intval(substr($str_pre_fix, $ind_no));
            $new_pre_fix = $new_pre_fix_str . strval($new_pre_fix_num + 1);
            update_setting("renban_no_prefix", $new_pre_fix);
            echo $new_pre_fix;
        } else {
            echo $pre_fix;
        }
    } else {
        echo 'Failed';
    }
}

function read_container_devan($post_data)
{
    $year_month = explode("/", $post_data['year_month']);
    $this_month_start = $year_month[1] . '-' . $year_month[0] . '-01';
    $start_date = date(
        'Y-m-d',
        strtotime('previous sunday', strtotime($this_month_start))
    );
    $this_month_end = date("Y-m-t", strtotime($this_month_start));
    $end_date = date(
        'Y-m-d',
        strtotime('next monday', strtotime($this_month_end))
    );

    $container_devan = get_container_devan($start_date, $end_date);
    $pre_date = '';
    if (count($container_devan) > 0) {
        foreach ($container_devan as $index => $row) {
            $date = $row['date']->format('Y-m-d');
            $week_day = date('l', strtotime($date));
            if ($pre_date != $date) {
                if ($week_day == 'Monday') {
                    echo '<tr>';
                    echo '<td colspan="38" style="height: 20px; background-color: #d5d5d5;"></td>';
                    echo '</tr>';
                } else {
                    echo '<tr>';
                    echo '<td colspan="38" style="height: 20px; background-color: white;"></td>';
                    echo '</tr>';
                }
            }

            echo '<tr class="devan-row" data-row="' . $row['id']
                . '" data-container="' . $row['in_house_container_number']
                . '" data-schedule_date="' . $date . '">';
            if ($row['revan_state'] == 'scheduled') {
                $style = 'background-color:red;color:white;';
            } else {
                if (
                    $row['revan_state'] == 'revan'
                    || $row['revan_state'] == 'completed'
                ) {
                    $style = 'background-color:#CCFFCC;';
                } else {
                    $style = '';
                }
            }
            echo '<!--------Delivery Management------->';
            echo '<th style="font-weight: bold;">' . date(
                'd-M D',
                strtotime($date)
            ) . '</th>';
            echo '<th style="font-weight: bold;">' . $row['shift'] . '</th>';
            echo '<th style="font-weight: bold; border-right: 1px solid #878787;">'
                . $row['time'] . '</th>';

            echo '<td style="' . $style
                . '"><input type="text" name="inbound_renban_air_freight_case_number" class="form-control input-value" value="'
                . $row['inbound_renban_air_freight_case_number'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="shipping_line" class="form-control input-value" value="'
                . $row['shipping_line'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="number_of_zr_modules" class="form-control input-value" value="'
                . $row['number_of_zr_modules'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="container_number" class="form-control input-value" style="width: 180px;" value="'
                . $row['container_number'] . '"></td>';
            echo '<td style="' . $style . '">' . $row['pentalver_instructions']
                . '</td>';

            echo '<td style="' . $style
                . '"><input type="text" name="departure_inbound_renban" class="form-control input-value" value="'
                . $row['departure_inbound_renban'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="departure_export_load_reference" class="form-control input-value" value="'
                . $row['departure_export_load_reference'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="departure_shipping_line" class="form-control input-value" value="'
                . $row['departure_shipping_line'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="departure_container_number" class="form-control input-value" style="width: 180px;" value="'
                . $row['departure_container_number'] . '"></td>';

            echo '<td style="' . $style
                . '"><input type="text" name="on_dock_inbound_renban" class="form-control input-value" value="'
                . $row['on_dock_inbound_renban'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="on_dock_shipping_line" class="form-control input-value" value="'
                . $row['on_dock_shipping_line'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="on_doc_container_number" class="form-control input-value" style="width: 180px;" value="'
                . $row['on_doc_container_number'] . '"></td>';

            echo '<!--------In House Management-------->';
            echo '<td style="' . $style . '"></td>';
            echo '<td style="' . $style . '">' . $row['in_house_instructions']
                . '</td>';
            echo '<td style="' . $style
                . '"><input type="text" name="confirm_gl_tl_instructions_print_name" class="form-control input-value" value="'
                . $row['confirm_gl_tl_instructions_print_name'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="confirm_gl_customs_check_print_name" class="form-control input-value" value="'
                . $row['confirm_gl_customs_check_print_name'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="confirm_module_condition_quantity" class="form-control input-value" value="'
                . $row['confirm_module_condition_quantity'] . '"></td>';

            echo '<td style="' . $style
                . '"><input type="text" name="devan_inbound_renban_no_1" class="form-control input-value" value="'
                . $row['devan_inbound_renban_no_1'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="devan_export_renban" class="form-control input-value" value="'
                . $row['devan_export_renban'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="devan_shipping_line" class="form-control input-value" value="'
                . $row['devan_shipping_line'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="devan_zr" class="form-control input-value" style="width: 120px;" value="'
                . $row['devan_zr'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="pipcont_pipseal" class="form-control input-value" value="'
                . $row['pipcont_pipseal'] . '"></td>';

            echo '<td style="' . $style
                . '"><input type="text" name="in_house_container_number" class="form-control input-value" style="width: 180px;" value="'
                . $row['in_house_container_number'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="expected_seal_no" class="form-control input-value" value="'
                . $row['expected_seal_no'] . '"></td>';

            echo '<td style="' . $style . '"></td>';

            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_inbound_renban_no_1" class="form-control input-value" value="'
                . $row['deeside_yard_inbound_renban_no_1'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_tapped_modules_no_1" class="form-control input-value" value="'
                . $row['deeside_yard_tapped_modules_no_1'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_container_number_no_1" class="form-control input-value" style="width: 180px;" value="'
                . $row['deeside_yard_container_number_no_1'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_inbound_renban_no_2" class="form-control input-value" value="'
                . $row['deeside_yard_inbound_renban_no_2'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_tapped_modules_no_2" class="form-control input-value" value="'
                . $row['deeside_yard_tapped_modules_no_2'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_container_number_no_2" class="form-control input-value" style="width: 180px;" value="'
                . $row['deeside_yard_container_number_no_2'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_inbound_renban_no_3" class="form-control input-value" value="'
                . $row['deeside_yard_inbound_renban_no_3'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_tapped_modules_no_3" class="form-control input-value" value="'
                . $row['deeside_yard_tapped_modules_no_3'] . '"></td>';
            echo '<td style="' . $style
                . '"><input type="text" name="deeside_yard_container_number_no_3" class="form-control input-value" style="width: 180px;" value="'
                . $row['deeside_yard_container_number_no_3'] . '"></td>';
            echo '</tr>';
            $pre_date = $date;
        }
    } else {
        echo 'there is no data yet';
    }
}

function read_container_devan_member_screen($post_data)
{
    global $dbMssql, $tblContainerDevan, $today;

    if ($post_data['date'] == 'today') {
        //$query = "SELECT * FROM {$tblContainerDevan} WHERE `revan_state` = 'scheduled' ORDER BY `date` ASC LIMIT 1";
        $query
            = "SELECT TOP 1 * FROM {$tblContainerDevan} WHERE [date] >= '{$today}' AND is_completed = 0 AND inbound_renban_air_freight_case_number!='' ORDER BY [date], shift ASC";
    } else {
        $date = convert_date_string($post_data['date']);
        $query
            = "SELECT TOP 1 * FROM {$tblContainerDevan} WHERE [date] >= '{$date}' AND is_completed = 0  AND inbound_renban_air_freight_case_number!='' ORDER BY [date], shift ASC";
    }

    $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
    if (sqlsrv_num_rows($result) > 0) {
        $devans = [];
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $devans[] = $row;
        }
        
        $renban_no = get_setting('renban_no_prefix');
        $devan = $devans[0];

        echo '<div class="row" style="background-color: #1797FF; color: #FFF;">';
        echo '<div class="offset-md-2 col-md-7" style="padding: 50px 10px; min-width: 650px;">';
        if ($devan['shift'] == 'D') {
            $shift = 'Days';
        } else {
            $shift = 'Night';
        }

        //if north america member plan, we show it at the top
        $devan_plan = $devan['inbound_renban_air_freight_case_number'];
        $devan_plan_flag = false;
        $complete_btn_disabled = "disabled";
        if (
            strpos(strtolower($devan_plan), "devan") !== false
            || strpos(strtolower($devan_plan), "america") !== false
        ) {
            $devan_plan_flag = true;
        }

        //if north america member plan
        if ($devan_plan_flag) {
            $devan_plan_value = rtrim(explode("-", $devan_plan)[0]);
            echo '<h1 style="font-size: 48px;text-align: center;">'
                . $devan_plan_value . '</h1>';
            $complete_btn_disabled = "";
        }

        //Date, Shift and Time
        echo '<h1 style="font-size: 48px;"><span style="margin-right: 148px;">'
            . $devan['date']->format('d/m/Y')
            . '</span><span style="margin-right: 60px;">' . $shift . '</span><span>'
            . $devan['time'] . '</span></h1>';
        //Container Renban
        //if north america member dose not need to confirm container number to press finish
        if (
            !strpos(strtolower($devan_plan), "devan")
            && !strpos(strtolower($devan_plan), "america")
        ) {
            echo '<label style="font-size: 48px; font-weight: normal">Container Renban:</label>';
            echo '<input type="text" id="container_renban" name="container_renban" class="form-control" style="width: 420px; display: inline-block; height: 60px; font-size: 48px;">';
            echo '<button class="btn btn-primary" data-revan="'
                . $devan['revan_state']
                . '" id="btn_chk_container_renban" style="height: 60px; margin-left: 20px; width: 160px; margin-top: -20px; font-size: 32px;" value="'
                . $devan['on_dock_inbound_renban'] . '">CHECK</button>';
        }

        //Container No
        echo '<h1 style="font-size: 48px;">Container No: <span style="color: white;">'
            . $devan['on_doc_container_number'] . '</span></h1>';

        //Reban
        echo '<h1 style="font-size: 48px;">';
        echo 'Renban No: <span id="renban_no">' . $renban_no . '</span>';
        echo '</h1>';

        //Reban
        echo '<div style="width: 100%; text-align: center;" >';
        echo '<button class="btn btn-success" id="btn_complete" style="width: 240px; font-size:36px; margin:0;" '
            . $complete_btn_disabled . ' value="' . $devan['id']
            . '" data-renban="check">Complete</button>';
        echo '</div>';
        echo '</div>';
        echo '<div class="col-md-3" style="display: flex; align-items: center;">';
        echo '<button class="btn bg-yellow devan-help" style="font-size: 36px; border-radius: 100px; width: 200px; height: 200px;">Help <br/>Andon</button>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p style="text-align: center; padding: 30px; font-size: 30px;">There is no scheduled job yet</p>';
    }
}

function get_live_stocking_overview($post_data)
{
    // global $db, $tblContainerDevan, $today;

    $data['stocking_alarm'] = get_help_alarm_for_overview('Stocking');
    $data['sessions'] = get_all_sessions();
    echo json_encode($data, true);
}

function get_live_deban($post_data)
{
    global $db, $tblContainerDevan, $today;

    $user = $post_data['user'];
    if ($post_data['date'] == 'today') {
        //$query = "SELECT * FROM {$tblContainerDevan} WHERE `revan_state` = 'scheduled' ORDER BY `date` ASC LIMIT 1";
        $query
            = "SELECT * FROM {$tblContainerDevan} WHERE `date` >= '{$today}' AND `is_completed` = 0 AND `inbound_renban_air_freight_case_number`!='' ORDER BY `date` ASC LIMIT 1";
    } else {
        $date = convert_date_string($post_data['date']);
        $query
            = "SELECT * FROM {$tblContainerDevan} WHERE `date` >= '{$date}' AND `is_completed` = 0  AND `inbound_renban_air_freight_case_number`!='' ORDER BY `date` ASC LIMIT 1";
    }

    $result = $db->query($query);
    if (mysqli_num_rows($result) > 0) {
        $devan = mysqli_fetch_array($result);
        //var_dump($devan);exit();
        //Update Renban No
        $renban_no = get_setting('renban_no_prefix');
        //$renban_no = update_renban_no($devan['id']);


        if (get_help_alarm_for_overview('Container Devan')) {
            echo '<div class="row m-0 p-0" style="background-color: red; color: #FFF;">';
        } else {
            echo '<div class="row m-0 p-0" style="border-radius: 15px; background-color: #1797FF; color: #FFF;">';
        }
        echo '<div>';
        if ($devan['shift'] == 'D') {
            $shift = 'Days';
        } else {
            $shift = 'Night';
        }

        //if north america member plan, we show it at the top
        $devan_plan = $devan['inbound_renban_air_freight_case_number'];
        $devan_plan_flag = false;
        $complete_btn_disabled = "disabled";
        if (
            strpos(strtolower($devan_plan), "devan") !== false
            || strpos(strtolower($devan_plan), "america") !== false
        ) {
            $devan_plan_flag = true;
        }

        //if north america member plan
        if ($devan_plan_flag) {
            $devan_plan_value = rtrim(explode("-", $devan_plan)[0]);
            echo '<h1 style="font-size: 38px;text-align: center;">'
                . $devan_plan_value . '</h1>';
            $complete_btn_disabled = "";
        }

        //Date, Shift and Time
        echo '<div class="row mx-0">';
        echo '<div class="col-md-8">';
        echo '<h1 style="font-size: 32px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Current Devan: </p>'
            . '<span style="margin-right: 20px; text-transform: uppercase;font-weight:bold">'
            . date('d/m/Y', strtotime($devan['date']))
            . '</span><span style="margin-right: 20px; text-transform: uppercase;font-weight:bold">'
            . $shift . '</span><span style="font-weight:bold">' . $devan['time']
            . '</span></h1>';
        echo '</div>';
        echo '<div class="col-md-4">';
        echo '<h1 style="font-size: 32px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Member: </p>'
            . '<span style="margin-right: 0px; text-transform: uppercase;">'
            . $user . '</span></h1>';
        echo '</div>';
        echo '</div>';
        //Container Renban
        //if north america member dose not need to confirm container number to press finish
        // if (!strpos(strtolower($devan_plan), "devan") && !strpos(strtolower($devan_plan), "america")) {
        //     echo '<label style="font-size: 48px; font-weight: normal">Container Renban:</label>';
        //     echo '<input type="text" id="container_renban" name="container_renban" class="form-control" style="width: 420px; display: inline-block; height: 60px; font-size: 48px;">';
        //     echo '<button class="btn btn-primary" data-revan="' . $devan['revan_state'] . '" id="btn_chk_container_renban" style="height: 60px; margin-left: 20px; width: 160px; margin-top: -20px; font-size: 32px;" value="' . $devan['on_dock_inbound_renban'] . '">CHECK</button>';
        // }

        //Container No
        echo '<div class="row mt-4 mx-0">';
        echo '<div class="col-md-8">';
        echo '<h1 style="font-size: 32px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Container No: </p><span style="color: white; font-size: 48px; font-weight:bold">'
            . $devan['on_doc_container_number'] . '</span></h1>';
        echo '</div>';
        $revan_state = $devan['revan_state'] == "scheduled" ? "Yes" : "No";
        echo '<div class="col-md-4">';
        echo '<h1 style="font-size: 32px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Revan: </p>'
            . '<span style="margin-right: 20px; text-transform: uppercase; font-size: 48px;font-weight:bold">'
            . $revan_state . '</span></h1>';
        echo '</div>';
        echo '</div>';

        // echo '<h1 style="font-size: 38px;"><p style="color: black">Container No: </p><span style="color: white;">' . $devan['on_doc_container_number'] . '</span></h1>';

        //Reban
        // echo '<h1 style="font-size: 48px;">';
        // echo 'Renban No: <span id="renban_no">' . $renban_no . '</span>';
        // echo '</h1>';

        //Reban
        // echo '<div style="width: 100%; text-align: center;" >';
        // echo '<button class="btn btn-success" id="btn_complete" style="width: 240px; font-size:36px; margin:0;" ' . $complete_btn_disabled . ' value="' . $devan['id'] . '" data-renban="check">Complete</button>';
        // echo '</div>';
        // echo '</div>';
        // echo '<div class="col-md-3" style="display: flex; align-items: center;">';
        // echo '<button class="btn bg-yellow devan-help" style="font-size: 36px; border-radius: 100px; width: 200px; height: 200px;">Help <br/>Andon</button>';
        // echo '</div>';
        // echo '</div>';
    } else {
        echo '<p style="text-align: center; padding: 30px; font-size: 30px;">There is no scheduled job yet</p>';
    }
}


function get_live_deban_overview1($post_data)
{
    global $db, $tblContainerDevan, $today;

    $user = ''; //$post_data['user'];
    if ($post_data['date'] == 'today') {
        //$query = "SELECT * FROM {$tblContainerDevan} WHERE `revan_state` = 'scheduled' ORDER BY `date` ASC LIMIT 1";
        $query
            = "SELECT * FROM {$tblContainerDevan} WHERE `date` >= '{$today}' AND `is_completed` = 0 AND `inbound_renban_air_freight_case_number`!='' ORDER BY `date` ASC LIMIT 1";
    } else {
        $date = convert_date_string($post_data['date']);
        $query
            = "SELECT * FROM {$tblContainerDevan} WHERE `date` >= '{$date}' AND `is_completed` = 0  AND `inbound_renban_air_freight_case_number`!='' ORDER BY `date` ASC LIMIT 1";
    }

    $result = $db->query($query);
    if (mysqli_num_rows($result) > 0) {
        $devan = mysqli_fetch_array($result);
        //var_dump($devan);exit();
        //Update Renban No
        $renban_no = get_setting('renban_no_prefix');
        //$renban_no = update_renban_no($devan['id']);


        if (get_help_alarm_for_overview('Container Devan')) {
            echo '<div class="row m-0 p-0" style="background-color: red; color: #FFF;">';
        } else {
            echo '<div class="row m-0 p-0" style="border-radius: 15px; color: black;">';
        }
        echo '<div style="width:100%">';
        if ($devan['shift'] == 'D') {
            $shift = 'Days';
        } else {
            $shift = 'Night';
        }

        //if north america member plan, we show it at the top
        $devan_plan = $devan['inbound_renban_air_freight_case_number'];
        $devan_plan_flag = false;
        $complete_btn_disabled = "disabled";
        if (
            strpos(strtolower($devan_plan), "devan") !== false
            || strpos(strtolower($devan_plan), "america") !== false
        ) {
            $devan_plan_flag = true;
        }

        //if north america member plan
        if ($devan_plan_flag) {
            $devan_plan_value = rtrim(explode("-", $devan_plan)[0]);
            echo '<h1 style="font-size: 38px;text-align: center;">'
                . $devan_plan_value . '</h1>';
            $complete_btn_disabled = "";
        }

        //Date, Shift and Time
        echo '<div class="row mx-0">';
        echo '<div class="col-md-8">';

        echo '<h1 style="font-size: 22px;"><p style="color: gray; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Current Devan: </p>'
            . '<span style="margin-right: 20px; text-transform: uppercase;font-weight:bold">'
            . date('d/m/Y', strtotime($devan['date']))
            . '</span><span style="margin-right: 20px; text-transform: uppercase;font-weight:bold">'
            . $shift . '</span><span style="font-weight:bold">' . $devan['time']
            . '</span></h1>';


        echo '<div class="mx-auto my-2">';
        echo '<h1 style="font-size: 22px;"><p style="color: gray; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Container No: </p><span style="color: black; font-size: 38px; font-weight:bold">'
            . $devan['on_doc_container_number'] . '</span></h1>';
        echo '</div>';

        echo '<div class="col-md-4 d-none">';
        echo '<h1 style="font-size: 22px;"><p style="color: gray; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Member: </p>'
            . '<span style="margin-right: 0px; text-transform: uppercase;">'
            . $user . '</span></h1>';
        echo '</div>';
        echo '</div>';
        //Container Renban
        //if north america member dose not need to confirm container number to press finish
        // if (!strpos(strtolower($devan_plan), "devan") && !strpos(strtolower($devan_plan), "america")) {
        //     echo '<label style="font-size: 48px; font-weight: normal">Container Renban:</label>';
        //     echo '<input type="text" id="container_renban" name="container_renban" class="form-control" style="width: 420px; display: inline-block; height: 60px; font-size: 48px;">';
        //     echo '<button class="btn btn-primary" data-revan="' . $devan['revan_state'] . '" id="btn_chk_container_renban" style="height: 60px; margin-left: 20px; width: 160px; margin-top: -20px; font-size: 32px;" value="' . $devan['on_dock_inbound_renban'] . '">CHECK</button>';
        // }

        //Container No
        echo '<div class="col-md-4 my-auto">';

        $revan_state = $devan['revan_state'] == "scheduled" ? "Yes" : "No";
        echo '<div class="mx-auto">';
        echo '<h1 style="font-size: 22px;">
        <p style="color: gray; text-transform: uppercase; font-weight:bold; margin-bottom:auto" >Revan: </p>'
            . '<span style="margin-right: 20px; text-transform: uppercase; font-size: 60px;font-weight:bold">'
            . $revan_state . '</span></h1>';
        echo '</div>';
        echo '</div>';

        echo '</div>';

        // echo '<h1 style="font-size: 38px;"><p style="color: black">Container No: </p><span style="color: white;">' . $devan['on_doc_container_number'] . '</span></h1>';

        //Reban
        // echo '<h1 style="font-size: 48px;">';
        // echo 'Renban No: <span id="renban_no">' . $renban_no . '</span>';
        // echo '</h1>';

        //Reban
        // echo '<div style="width: 100%; text-align: center;" >';
        // echo '<button class="btn btn-success" id="btn_complete" style="width: 240px; font-size:36px; margin:0;" ' . $complete_btn_disabled . ' value="' . $devan['id'] . '" data-renban="check">Complete</button>';
        // echo '</div>';
        // echo '</div>';
        // echo '<div class="col-md-3" style="display: flex; align-items: center;">';
        // echo '<button class="btn bg-yellow devan-help" style="font-size: 36px; border-radius: 100px; width: 200px; height: 200px;">Help <br/>Andon</button>';
        // echo '</div>';
        // echo '</div>';
    } else {
        echo '<p style="text-align: center; padding: 30px; font-size: 30px;">There is no scheduled job yet</p>';
    }
}


function get_live_deban_team_leader($post_data)
{
    global $db, $tblContainerDevan, $today;

    // $user = $post_data['user'];
    $user = '';
    // $sessions = get_all_sessions();
    // $user = $_SESSION['current_user_name'];
    if ($post_data['date'] == 'today') {
        //$query = "SELECT * FROM {$tblContainerDevan} WHERE `revan_state` = 'scheduled' ORDER BY `date` ASC LIMIT 1";
        $query
            = "SELECT * FROM {$tblContainerDevan} WHERE `date` >= '{$today}' AND `is_completed` = 0 AND `inbound_renban_air_freight_case_number`!='' ORDER BY `date` ASC LIMIT 1";
    } else {
        $date = convert_date_string($post_data['date']);
        $query
            = "SELECT * FROM {$tblContainerDevan} WHERE `date` >= '{$date}' AND `is_completed` = 0  AND `inbound_renban_air_freight_case_number`!='' ORDER BY `date` ASC LIMIT 1";
    }

    $result = $db->query($query);
    if (mysqli_num_rows($result) > 0) {
        $devan = mysqli_fetch_array($result);
        //var_dump($devan);exit();
        //Update Renban No
        $renban_no = get_setting('renban_no_prefix');
        //$renban_no = update_renban_no($devan['id']);


        if (get_help_alarm_for_overview('Container Devan')) {
            echo '<div class="row m-0 p-0" style="background-color: red; color: #FFF;">';
        } else {
            echo '<div class="row m-0 p-0" style="border-radius: 15px; background-color: #1797FF; color: #FFF;">';
        }
        echo '<div>';
        if ($devan['shift'] == 'D') {
            $shift = 'Days';
        } else {
            $shift = 'Night';
        }

        //if north america member plan, we show it at the top
        $devan_plan = $devan['inbound_renban_air_freight_case_number'];
        $devan_plan_flag = false;
        $complete_btn_disabled = "disabled";
        if (
            strpos(strtolower($devan_plan), "devan") !== false
            || strpos(strtolower($devan_plan), "america") !== false
        ) {
            $devan_plan_flag = true;
        }

        //if north america member plan
        if ($devan_plan_flag) {
            $devan_plan_value = rtrim(explode("-", $devan_plan)[0]);
            echo '<h1 style="font-size: 38px;text-align: center;">'
                . $devan_plan_value . '</h1>';
            $complete_btn_disabled = "";
        }

        //Date, Shift and Time
        echo '<div class="row mx-0">';
        echo '<div class="col-md-8">';
        echo '<h1 style="font-size: 18px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Current Devan: </p>'
            . '<span style="margin-right: 0px; text-transform: uppercase;font-weight:bold">'
            . date('d/m/Y', strtotime($devan['date']))
            . '</span><span style="margin-right: 20px; text-transform: uppercase;font-weight:bold">'
            . $shift . '</span><span style="font-weight:bold">' . $devan['time']
            . '</span></h1>';
        echo '</div>';
        echo '<div class="col-md-4" style="margin-left: 0px;">';
        echo '<h1 style="font-size: 18px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto;">Member: </p>'
            . '<span id="member_name" style="margin-right: 0px; text-transform: uppercase;">'
            . $user . '</span></h1>';
        echo '</div>';
        echo '</div>';
        //Container Renban
        //if north america member dose not need to confirm container number to press finish
        // if (!strpos(strtolower($devan_plan), "devan") && !strpos(strtolower($devan_plan), "america")) {
        //     echo '<label style="font-size: 48px; font-weight: normal">Container Renban:</label>';
        //     echo '<input type="text" id="container_renban" name="container_renban" class="form-control" style="width: 420px; display: inline-block; height: 60px; font-size: 48px;">';
        //     echo '<button class="btn btn-primary" data-revan="' . $devan['revan_state'] . '" id="btn_chk_container_renban" style="height: 60px; margin-left: 20px; width: 160px; margin-top: -20px; font-size: 32px;" value="' . $devan['on_dock_inbound_renban'] . '">CHECK</button>';
        // }

        //Container No
        echo '<div class="row mt-4 mx-0">';
        echo '<div class="col-md-8">';
        echo '<h1 style="font-size: 18px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Container No: </p><span style="color: white; font-size: 28px; font-weight:bold">'
            . $devan['on_doc_container_number'] . '</span></h1>';
        echo '</div>';
        $revan_state = $devan['revan_state'] == "scheduled" ? "Yes" : "No";
        echo '<div class="col-md-4">';
        echo '<h1 style="font-size: 18px;"><p style="color: black; text-transform: uppercase; font-weight:bold; margin-bottom:auto">Revan: </p>'
            . '<span style="margin-left: 20px; text-transform: uppercase; font-size: 28px;font-weight:bold">'
            . $revan_state . '</span></h1>';
        echo '</div>';
        echo '</div>';

        // echo '<h1 style="font-size: 38px;"><p style="color: black">Container No: </p><span style="color: white;">' . $devan['on_doc_container_number'] . '</span></h1>';

        //Reban
        // echo '<h1 style="font-size: 48px;">';
        // echo 'Renban No: <span id="renban_no">' . $renban_no . '</span>';
        // echo '</h1>';

        //Reban
        // echo '<div style="width: 100%; text-align: center;" >';
        // echo '<button class="btn btn-success" id="btn_complete" style="width: 240px; font-size:36px; margin:0;" ' . $complete_btn_disabled . ' value="' . $devan['id'] . '" data-renban="check">Complete</button>';
        // echo '</div>';
        // echo '</div>';
        // echo '<div class="col-md-3" style="display: flex; align-items: center;">';
        // echo '<button class="btn bg-yellow devan-help" style="font-size: 36px; border-radius: 100px; width: 200px; height: 200px;">Help <br/>Andon</button>';
        // echo '</div>';
        // echo '</div>';
    } else {
        echo '<p style="text-align: center; padding: 30px; font-size: 30px;">There is no scheduled job yet</p>';
    }
}

function update_renban_no($devan_id)
{
    global $db, $tblContainerDevan;
    $pre_fix = get_setting('renban_no_prefix');
    //$query = "SELECT * FROM {$tblContainerDevan} WHERE id < '{$devan_id}' AND is_completed = 1 ORDER BY `departure_inbound_renban` DESC LIMIT 1";
    $query
        = "SELECT * FROM {$tblContainerDevan} WHERE is_completed = 1 ORDER BY `departure_inbound_renban` DESC LIMIT 1";
    $result = $db->query($query);
    if (mysqli_num_rows($result) > 0) {
        $devan = mysqli_fetch_array($result);
        $old_inbound_renban = $devan['departure_inbound_renban'];
        $new_reban_no = (int) $old_inbound_renban + 1;
        if ($new_reban_no < 10) {
            $renban_no = $pre_fix . '0000' . $new_reban_no;
        } else {
            if ($new_reban_no >= 10 && $new_reban_no < 100) {
                $renban_no = $pre_fix . '000' . $new_reban_no;
            } else {
                if ($new_reban_no >= 100 && $new_reban_no < 1000) {
                    $renban_no = $pre_fix . '00' . $new_reban_no;
                } else {
                    if ($new_reban_no >= 1000 && $new_reban_no < 10000) {
                        $renban_no = $pre_fix . '0' . $new_reban_no;
                    } else {
                        $renban_no = $pre_fix . $new_reban_no;
                    }
                }
            }
        }
        $update_query
            = "UPDATE {$tblContainerDevan} SET `departure_inbound_renban` = '{$new_reban_no}' WHERE id = {$devan_id}";
    } else {
        $update_query
            = "UPDATE {$tblContainerDevan} SET `departure_inbound_renban` = '1' WHERE id = {$devan_id}";
        $renban_no = $pre_fix . '00001';
    }
    $result = $db->query($update_query);
    return $renban_no;
}

function update_revan_state($post_data)
{
    global $dbMssql, $tblContainerDevan;
    $container_number = $post_data['container_number'];
    $today = date('Y-m-d');
    $query
        = "UPDATE {$tblContainerDevan} SET revan_state = 'scheduled' WHERE in_house_container_number = '{$container_number}' AND [date] >= '{$today}'";
    $result = sqlsrv_query($dbMssql, $query);
    if ($result) {
        echo $today;
    } else {
        echo 'Failed';
    }
}


/*
 * Stocking Page Input
 */

function read_area_lane_status($post_data, $direction = null)
{
    global $db, $tblScanLog, $STOCKING_AREAS, $tblParts;
    $part_no = $post_data['part_no'];
    if (strlen($part_no) > 10) {
        $part_no = substr($part_no, 10, 2);
    }
    $page = $post_data['page'];
    if (!isset($direction)) {
        $direction = $_SESSION['stocking_action'];
    } else {
        $direction = $post_data['direction'];
    }
    if (empty($part_no)) {
        $query
            = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0";
    } else {
        $query
            = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `part` = '{$part_no}'";
    }
    $result = $db->query($query);
    $rows = mysqli_num_rows($result);
    $filled_lanes = [];
    if ($rows > 0) {
        while ($row = mysqli_fetch_object($result)) {
            if (!in_array($row->lane_id, $filled_lanes)) {
                array_push($filled_lanes, $row->lane_id);
            }
        }
    } else {
        $part_no = '';
    }
    $query = "SELECT * FROM {$tblParts} WHERE part_no='" . $part_no . "'";
    $this_part = mysqli_fetch_array($db->query($query));

    $areas = [];

    $lanes = [];
    $areas_name = $STOCKING_AREAS;
    // if(empty($this_part))
    //     return;
    {
        if (!intval($this_part['sf'])) {
            unset($areas_name[array_search('System Fill', $areas_name)]);
        }
        if (!intval($this_part['fl'])) {
            unset($areas_name[array_search('Free Location', $areas_name)]);
        }
        if (!intval($this_part['ps'])) {
            unset($areas_name[array_search('Part Stocking', $areas_name)]);
        }
    }

    foreach ($areas_name as $index => $area) {
        $lanes = get_all_lanes($area);
        foreach ($lanes as $lane) {
            $allocation = $lane->allocation;
            $query
                = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} ORDER BY booked_in_time";
            $result = $db->query($query);
            $filled_allocations = mysqli_num_rows($result);
            $left_allocations = $allocation - $filled_allocations;
            // var_dump($lane->area);
            if ($lane->area == "Free Location") {
                $lane_no = $lane->barcode_in;
            } else {
                $lane_no = 'Lane ' . $lane->lane_no;
            }
            if (!empty($part_no)) {
                if (
                    $filled_allocations == 0
                    || in_array($lane->id, $filled_lanes)
                ) {
                    $areas[$area][] = [
                        'lane_old' => 'Lane ' . $lane->lane_no,
                        'lane' => $lane_no,
                        'allocation' => $allocation,
                        'filled_allocation' => $filled_allocations,
                    ];
                }
            } else {
                if ($filled_allocations == 0) {
                    $areas[$area][] = [
                        'lane_old' => 'Lane ' . $lane->lane_no,
                        'lane' => $lane_no,
                        'allocation' => $allocation,
                        'filled_allocation' => $filled_allocations,
                    ];
                }
            }
        }
    }

    $return_html = '<div class="row">';
    $all_lanes = [];
    $stock_available_lanes = [];
    $lanes_html = "";
    $lanes_count
        = []; //to get the location/area which has the most number of available lanes
    $lanes_have_most_stock = ""; //to get the lane which has the most stocks > 0
    $area_have_most_stock = "";
    $default_lane_stock = '';
    $areas = array_reverse($areas);
    foreach ($areas as $area => $lanes) {
        $all_lanes = $lanes;
        $default_lane_stock = (isset($lanes) && !empty($lanes))
            ? $lanes[0]["filled_allocation"] : 0;
        break;
    }

    foreach ($areas as $area => $lanes) {
        $all_lanes = $lanes;

        $lanes_html .= '<div class="col-sm-4">';
        $lanes_html .= '<h1>' . $area . '</h1>';
        $lanes_html .= '<table class="table table-bordered table-striped">';
        if (!empty($part_no)) {
            $lanes_html .= '<tr><th>Part</th><th>' . $part_no . '</th></tr>';
        }

        $lanes_html .= '<tr><td colspan="2" style="text-align: left;">Lanes Available</td></tr>';

        foreach ($lanes as $lane) {
            if (
                $lane['filled_allocation'] > 0
                && $lane["filled_allocation"] >= $default_lane_stock
                && $lane["filled_allocation"] <= $lane["allocation"]
            ) {
                $area_have_most_stock = $area;
                // var_dump('BBBBBBBBBBBBB' , $lane);
                $lanes_have_most_stock = $lane['lane'];
            }

            // var_dump($lane);
            $lanes_html .= '<tr class="cursor_pointed lane_row" data-lane="'
                . $lane["lane"] . '" style="cursor: pointer;">';
            $lanes_html .= '<td>' . $lane['lane_old'] . '</td>';
            $lanes_html .= '<td>' . $lane['filled_allocation'] . '/'
                . $lane['allocation'] . '</td>';
            $lanes_html .= '</tr>';

            //show scan in/out lane info with available stocks > 0
            // var_dump($lane);
            // $lane_no = explode(" ", $lane['lane'])[1];
            // if(str_contains($lane['lane'], " ") )
            //     array_push($stock_available_lanes, explode(" ", $lane['lane'])[1]);
            // else
            array_push(
                $stock_available_lanes,
                explode(" ", $lane['lane_old'])[1]
            );
        }

        //array_push($lanes_count, count($lanes));
        $lanes_html .= '</table>';
        $lanes_html .= '</div>';
    }
    $lanes_html .= '</div>';

    //set default values
    if (empty($lanes_have_most_stock) && empty($area_have_most_stock)) {
        foreach ($areas as $area => $lanes) {
            $area_have_most_stock = $area;
            foreach ($lanes as $lane) {
                // var_dump('AAAAAAAAAAAA' , $lane);
                $lanes_have_most_stock = $lane['lane'];
                break;
            }
        }
    }

    //for scan in case
    /*$most_have_lanes_index = array_search(max($lanes_count), $lanes_count);
    $area_code = $STOCKING_AREAS[$most_have_lanes_index];*/

    //oldest scaned in/out lanes html on the top of the table
    $oldest_scanned_lanes_html
        = get_oldest_lanes_scanned(
            $stock_available_lanes,
            $post_data,
            $direction,
            $area_have_most_stock,
            $lanes_have_most_stock
        );

    $return_html = $return_html . $oldest_scanned_lanes_html . $lanes_html;
    // var_dump($oldest_scanned_lanes_html);
    echo $return_html;
}

/*
 * Stocking Page Overview
 */

function read_area_lane_status_overview($post_data, $direction = null)
{
    global $db, $tblScanLog, $STOCKING_AREAS, $tblParts;
    $part_no = $post_data['part_no'];
    if (strlen($part_no) > 10) {
        $part_no = substr($part_no, 10, 2);
    }
    $page = $post_data['page'];
    if (!isset($direction)) {
        $direction = $_SESSION['stocking_action'];
    } else {
        $direction = $post_data['direction'];
    }
    if (empty($part_no)) {
        $query
            = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0";
    } else {
        $query
            = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `part` = '{$part_no}'";
    }
    $result = $db->query($query);
    $rows = mysqli_num_rows($result);
    $filled_lanes = [];
    if ($rows > 0) {
        while ($row = mysqli_fetch_object($result)) {
            if (!in_array($row->lane_id, $filled_lanes)) {
                array_push($filled_lanes, $row->lane_id);
            }
        }
    } else {
        $part_no = '';
    }
    $query = "SELECT * FROM {$tblParts} WHERE part_no='" . $part_no . "'";
    $this_part = mysqli_fetch_array($db->query($query));

    $areas = [];

    $lanes = [];
    $areas_name = $STOCKING_AREAS;
    // if(empty($this_part))
    //     return;
    {
        if (!intval($this_part['sf'])) {
            unset($areas_name[array_search('System Fill', $areas_name)]);
        }
        if (!intval($this_part['fl'])) {
            unset($areas_name[array_search('Free Location', $areas_name)]);
        }
        if (!intval($this_part['ps'])) {
            unset($areas_name[array_search('Part Stocking', $areas_name)]);
        }
    }

    foreach ($areas_name as $index => $area) {
        $lanes = get_all_lanes($area);
        foreach ($lanes as $lane) {
            $allocation = $lane->allocation;
            $query
                = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} ORDER BY booked_in_time";
            $result = $db->query($query);
            $filled_allocations = mysqli_num_rows($result);
            $left_allocations = $allocation - $filled_allocations;
            // var_dump($lane->area);
            if ($lane->area == "Free Location") {
                $lane_no = $lane->barcode_in;
            } else {
                $lane_no = 'Lane ' . $lane->lane_no;
            }
            if (!empty($part_no)) {
                if (
                    $filled_allocations == 0
                    || in_array($lane->id, $filled_lanes)
                ) {
                    $areas[$area][] = [
                        'lane_old' => 'Lane ' . $lane->lane_no,
                        'lane' => $lane_no,
                        'allocation' => $allocation,
                        'filled_allocation' => $filled_allocations,
                    ];
                }
            } else {
                if ($filled_allocations == 0) {
                    $areas[$area][] = [
                        'lane_old' => 'Lane ' . $lane->lane_no,
                        'lane' => $lane_no,
                        'allocation' => $allocation,
                        'filled_allocation' => $filled_allocations,
                    ];
                }
            }
        }
    }

    $return_html = '<div class="row">';
    $all_lanes = [];
    $stock_available_lanes = [];
    $lanes_html = "";
    $lanes_count
        = []; //to get the location/area which has the most number of available lanes
    $lanes_have_most_stock = ""; //to get the lane which has the most stocks > 0
    $area_have_most_stock = "";
    $default_lane_stock = [];
    $areas = array_reverse($areas);
    foreach ($areas as $area => $lanes) {
        $all_lanes = $lanes;
        $default_lane_stock = (isset($lanes) && !empty($lanes))
            ? $lanes[0]["filled_allocation"] : 0;
        break;
    }

    foreach ($areas as $area => $lanes) {
        $all_lanes = $lanes;

        $lanes_html .= '<div class="col-sm-4">';
        $lanes_html .= '<h1>' . $area . '</h1>';
        $lanes_html .= '<table class="table table-bordered table-striped">';
        if (!empty($part_no)) {
            $lanes_html .= '<tr><th>Part</th><th>' . $part_no . '</th></tr>';
        }

        $lanes_html .= '<tr><td colspan="2" style="text-align: left;">Lanes Available</td></tr>';

        foreach ($lanes as $lane) {
            if (
                $lane['filled_allocation'] > 0
                && $lane["filled_allocation"] >= $default_lane_stock
                && $lane["filled_allocation"] <= $lane["allocation"]
            ) {
                $area_have_most_stock = $area;
                // var_dump('BBBBBBBBBBBBB' , $lane);
                $lanes_have_most_stock = $lane['lane'];
            }

            // var_dump($lane);
            $lanes_html .= '<tr class="cursor_pointed lane_row" data-lane="'
                . $lane["lane"] . '" style="cursor: pointer;">';
            $lanes_html .= '<td>' . $lane['lane_old'] . '</td>';
            $lanes_html .= '<td>' . $lane['filled_allocation'] . '/'
                . $lane['allocation'] . '</td>';
            $lanes_html .= '</tr>';

            //show scan in/out lane info with available stocks > 0
            // var_dump($lane);
            // $lane_no = explode(" ", $lane['lane'])[1];
            // if(str_contains($lane['lane'], " ") )
            //     array_push($stock_available_lanes, explode(" ", $lane['lane'])[1]);
            // else
            array_push(
                $stock_available_lanes,
                explode(" ", $lane['lane_old'])[1]
            );
        }

        //array_push($lanes_count, count($lanes));
        $lanes_html .= '</table>';
        $lanes_html .= '</div>';
    }
    $lanes_html .= '</div>';

    //set default values
    if (empty($lanes_have_most_stock) && empty($area_have_most_stock)) {
        foreach ($areas as $area => $lanes) {
            $area_have_most_stock = $area;
            foreach ($lanes as $lane) {
                // var_dump('AAAAAAAAAAAA' , $lane);
                $lanes_have_most_stock = $lane['lane'];
                break;
            }
        }
    }

    //for scan in case
    /*$most_have_lanes_index = array_search(max($lanes_count), $lanes_count);
    $area_code = $STOCKING_AREAS[$most_have_lanes_index];*/

    //oldest scaned in/out lanes html on the top of the table
    $oldest_scanned_lanes_html
        = get_oldest_lanes_scanned(
            $stock_available_lanes,
            $post_data,
            $direction,
            $area_have_most_stock,
            $lanes_have_most_stock
        );

    $return_html = $return_html . $oldest_scanned_lanes_html; // . $lanes_html;
    // var_dump($oldest_scanned_lanes_html);
    echo $return_html;
}

//get oldest scan in/out lane info
function get_oldest_lanes_scanned(
    $stock_available_lanes,
    $post_data,
    $direction,
    $area_code,
    $lean_name
) { // direction : in/out
    global $db, $tblScanLog, $tblStocking;
    $part_no = $post_data["part_no"];
    $return_html = '';

    //if direction out-> the one with the oldest part scanned in
    // if ($direction == "out")
    {
        if ($stock_available_lanes) {
            $sort_query
                = "SELECT id, part, page, lane_id, lane_id_fl FROM {$tblScanLog} WHERE `lane_id` IN ("
                . implode(',', $stock_available_lanes) . ")"
                . " AND booked_out_time IS NULL AND LCASE(part)='"
                . strtolower($part_no) . "' ORDER BY booked_in_time LIMIT 1";
        } else {
            return $return_html;
        }

        $sort_result = $db->query($sort_query);
        if (mysqli_num_rows($sort_result) > 0) {
            while ($row = mysqli_fetch_object($sort_result)) {
                $part_no = $row->part;

                if ($row->lane_id_fl) {
                    $query
                        = "SELECT * FROM {$tblStocking} WHERE id = {$row->lane_id}";

                    $res = mysqli_fetch_object($db->query($query));

                    $ind_no = -1;
                    foreach (str_split($res->barcode_in) as $ind => $cha) {
                        if (is_numeric($cha)) {
                            $ind_no = $ind;
                            break;
                        }
                    }
                    $new_pre_fix_str = substr($res->barcode_in, 0, $ind_no);
                    $new_pre_fix = $new_pre_fix_str . strval($row->lane_id_fl);
                    $lean_name = $new_pre_fix;
                } else {
                    $tem
                        = mysqli_fetch_object($db->query("SELECT * FROM {$tblStocking} WHERE id={$row->lane_id}"));
                    $lean_name = "Lane " . $tem->lane_no;
                    $area_code = $tem->area;
                }
            }
        }
    }

    $return_html = '
        <div class="col-sm-4 oldest_info_div" style="color: white;text-align: center;">
            <h2>Part: ' . strtoupper($part_no) . '</h2>
        </div>
        <div class="col-sm-4 oldest_info_div" style="color: yellow;text-align: center;">
            <h2>Area: ' . $area_code . '</h2>
        </div>
        <div class="col-sm-4 oldest_info_div" style="color: red;text-align: center;">
            <h2>Lane: ' . $lean_name . '</h2>
        </div>
    ';

    return $return_html;
}

function get_filled_lanes_by_part($post_data)
{
    global $db, $tblScanLog;
    $part_no = $post_data['part_no'];
    if (strlen($part_no) > 10) {
        $part_no = substr($part_no, 10, 2);
    }
    $page = $post_data['page'];
    $query
        = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `part` = '{$part_no}'";
    $result = $db->query($query);
    $rows = mysqli_num_rows($result);
    $filled_lanes = [];
    //$lanes = array();
    if ($rows > 0) {
        while ($row = mysqli_fetch_object($result)) {
            if (!in_array($row->lane_id, $filled_lanes)) {
                array_push($filled_lanes, $row->lane_id);
                //array_push($lanes, get_lane_by_id($row->lane_id));
            }
        }
    }

    $part = get_part_by_no($part_no);
    $data['part'] = $part;
    $amount = 0;
    $lanes = [];
    foreach ($filled_lanes as $lane_id) {
        $query
            = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane_id}";
        $result = $db->query($query);
        $lane_inf = get_lane_by_id($lane_id);
        $location_index = $lane_inf->allocation;
        $locations = [];
        while ($row = mysqli_fetch_object($result)) {
            if ($lane_inf->area == 'Free Location') {
                array_push($locations, $row->lane_id_fl);
                $amount += $part['amount'];
            } else {
                if (strtoupper($row->part) == strtoupper($part['part_no'])) {
                    array_push($locations, $location_index);
                    $amount += $part['amount'];
                }
            }
            $location_index--;
        }
        array_push(
            $lanes,
            [
                'lane_id' => $lane_inf->id,
                'lane_no' => $lane_inf->lane_no,
                'area' => $lane_inf->area,
                'locations' => implode(", ", $locations),
                'lane_inf' => $lane_inf,
            ]
        );
    }
    $data['lanes'] = $lanes;
    $data['amount'] = $amount;
    echo json_encode($data, true);
}

function load_overview_screen($post_data)
{
    global $db, $tblScanLog, $STOCKING_AREAS;
    $page = $post_data['page'];
    $td_data = [];
    foreach ($STOCKING_AREAS as $index => $area) {
        $lanes = get_all_lanes($area);
        foreach ($lanes as $lane) {
            $query
                = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id}";

            $result = $db->query($query);
            $filled_allocations = mysqli_num_rows($result);

            $allocations = $lane->allocation;
            $height = $lane->height;
            if ($area == 'Free Location') {
                $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);

                $lane_id_fls = array_column($arr, "lane_id_fl");
                $temp = array_count_values($lane_id_fls);
                for ($i = $allocations; $i > 0; $i--) {
                    $td_class = '';
                    if (array_search($i, $lane_id_fls) !== false) {
                        if ($temp[$i] == 3) {
                            $td_class = 'full-td';
                        } else {
                            if ($temp[$i] == 2) {
                                $td_class = 'm-full-td';
                            } else {
                                if ($temp[$i] == 1) {
                                    $td_class = 'l-full-td';
                                }
                            }
                        }
                    }

                    if ($td_class != '') {
                        array_push(
                            $td_data,
                            [
                                'id' => 'td_' . $lane->id . '_' . $i,
                                'td_class' => $td_class
                            ]
                        );
                    }
                }
            }

            if ($area == 'Part Stocking') {
                // $index = ceil($allocations / $height);
                // if ($allocations % $height != 0)
                //     $index++;
                $index = 1;
                for ($i = $height; $i <= $allocations; $i += $height) {
                    $td_class = '';
                    $box_size = $index * $height;
                    if ($filled_allocations >= $box_size) {
                        $td_class = 'full-td';
                    } else {
                        $diff = $box_size - $filled_allocations;
                        $reminder = $filled_allocations % $height;
                        if ($diff < $height) {
                            if ($reminder < 2) {
                                $td_class = 'l-full-td';
                            } else {
                                $td_class = 'm-full-td';
                            }
                        }
                    }

                    if ($td_class != '') {
                        array_push(
                            $td_data,
                            [
                                'id' => 'td_' . $lane->id . '_' . $i,
                                'td_class' => $td_class
                            ]
                        );
                    }

                    $index++;
                }

                if ($allocations < $i && $allocations > ($i - $height)) {
                    if ($filled_allocations >= $height) {
                        $td_class = 'full-td';
                    } else {
                        if ($filled_allocations <= $height - 1) {
                            $td_class = 'm-full-td';
                        } else {
                            if ($filled_allocations <= $height - 1) {
                                $td_class = 'l-full-td';
                            } else {
                                $td_class = '';
                            }
                        }
                    }

                    if ($td_class != '') {
                        array_push(
                            $td_data,
                            [
                                'id' => 'td_' . $lane->id . '_' . $i,
                                'td_class' => $td_class
                            ]
                        );
                    }
                }
            }

            if ($area == 'System Fill') {
                if ($allocations % $height == 0) {
                    $start = $allocations;
                } else {
                    $start = $allocations + ($height - $allocations % $height);
                }
                $index = 1;
                for ($i = $height; $i <= $start; $i += $height) {
                    $td_class = '';
                    $box_size = $index * $height;
                    if ($filled_allocations >= $box_size) {
                        $td_class = 'full-td';
                    } else {
                        $diff = $box_size - $filled_allocations;
                        $reminder = $filled_allocations % $height;
                        if ($diff < $height) {
                            if ($reminder < 2) {
                                $td_class = 'l-full-td';
                            } else {
                                $td_class = 'm-full-td';
                            }
                        }
                    }
                    if ($td_class != '') {
                        array_push(
                            $td_data,
                            [
                                'id' => 'td_' . $lane->id . '_' . $i,
                                'td_class' => $td_class
                            ]
                        );
                    }
                    $index++;
                }
            }
        }
    }

    echo json_encode($td_data, true);
}

// function get_box_data($post_data)
// {
//     global $db, $tblScanLog;
//     $lane_id = $post_data['lane_id'];
//     $page = $post_data['page'];
//     $box_index = $post_data['box_index'];
//     $lane = get_lane_by_id($lane_id);

//     $area = $lane->area;
//     $height = $lane->height;
//     $allocations = $lane->allocation;
//     // if ($area == 'Free Location')
//     //     $height = 1;

//     $start = $height * $box_index;
//     $end = $height;

//     // echo '<table class="table">';
//     // echo '<tr>';
//     // if ($area == 'Free Location')
//     //     echo '<td>Area: ' . $area . '</td>';
//     // else
//     //     echo '<td colspan="2">Area: ' . $area . '</td>';
//     // $colspan = $height - 1;
//     // echo '<td colspan="3">Lane: ' . $lane->lane_no . '</td>';
//     // echo '</tr>';

//     // echo '<tr>';
//     // echo '<td style="">Location: </td>';
//     // if ($area == 'Free Location') {
//     //     $location = $allocations - $box_index;
//     //     echo '<td colspan=3 style="">' . substr($lane->barcode_in, 0, -1) . $location . '</td>';
//     // } else {
//     //     $s = $allocations - $box_index * $height;
//     //     $e = $s - $height;
//     //     for ($i = $box_index * $height + 1; $i <= ($box_index + 1) * $height; $i++) {
//     //         echo '<td style="">' . $i . '</td>';
//     //     }
//     // }
//     // echo '</tr>';

//     if ($area == 'Free Location') {
//         $lane_id_fl = $lane->allocation - $box_index;
//         $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} AND `lane_id_fl` = {$lane_id_fl} ORDER BY `booked_in_time` ASC";
//     } else {
//         $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id}
//                 ORDER BY `booked_in_time` ASC LIMIT {$start}, {$end}";
//     }

//     $result = $db->query($query);
//     if (mysqli_num_rows($result)) {
//         $data = array();
//         while ($row = mysqli_fetch_object($result)) {
//             if (!empty($row->user_id)) {
//                 $user = get_user_info($row->user_id);
//                 $user_name = $user->username;
//             } else {
//                 $user_name = '';
//             }

//             $part = get_part_by_no($row->part);
//             if ($part)
//                 $amount = $part['amount'];
//             else
//                 $amount = '';

//             array_push($data, array(
//                 'date' => date('d/m/Y', strtotime($row->booked_in_time)),
//                 'member' => $user_name,
//                 'part_no' => $row->part,
//                 'amount' => $amount,
//             ));
//         }
//     } else {
//         $left = $allocations - $box_index;
//         $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} AND `lane_id_fl` = {$left}
//         ORDER BY `booked_in_time` ASC LIMIT 1";
//         $result = $db->query($query);
//         $data = array();
//         while ($row = mysqli_fetch_object($result)) {
//             if (!empty($row->user_id)) {
//                 $user = get_user_info($row->user_id);
//                 $user_name = $user->username;
//             } else {
//                 $user_name = '';
//             }

//             $part = get_part_by_no($row->part);
//             if ($part)
//                 $amount = $part['amount'];
//             else
//                 $amount = '';

//             array_push($data, array(
//                 'date' => date('d/m/Y', strtotime($row->booked_in_time)),
//                 'member' => $user_name,
//                 'part_no' => $row->part,
//                 'amount' => $amount,
//             ));
//         }
//     }


//     // echo '<tr>';
//     // echo '<td>Date IN: </td>';
//     // $cols = 1;
//     foreach ($data as $item) {
//         // echo '<td>' . $item['date'] . '</td>';
//         // $cols++;
//         echo '<div class="row">
//                 <div class="col-sm-4 oldest_info_div" style="color: white;text-align: center;">
//                     <h2>Part: ' . $item['part_no'] . '</h2>
//                 </div>
//                 <div class="col-sm-4 oldest_info_div" style="color: white;text-align: center;">
//                     <h2>Area: ' . $area . '</h2>
//                 </div>
//                 <div class="col-sm-4 oldest_info_div" style="color: white;text-align: center;">
//                     <h2>Lane: ' . $lane->barcode_in . '</h2>
//                 </div>
//                 <div class="col-sm-4 oldest_info_div" style="color: white;text-align: center;">
//                     <h2>Date IN: ' . $item['date'] . '</h2>
//                 </div>
//                 <div class="col-sm-4 oldest_info_div" style="color: green;text-align: center;">
//                     <h2>Member: ' . $item['member'] . '</h2>
//                 </div>
//                 <div class="col-sm-4 oldest_info_div" style="color: blue;text-align: center;">
//                     <h2>Amount: ' . $item['amount'] . '</h2>
//                 </div>
//             </div>';
//     }

//     // if ($height > 1)
//     //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     //         echo '<td></td>';
//     //     }
//     // echo '</tr>';

//     // //Member
//     // echo '<tr>';
//     // echo '<td>Member: </td>';
//     // $cols = 1;
//     // foreach ($data as $item) {
//     //     echo '<td>' . $item['member'] . '</td>';
//     //     $cols++;
//     // }
//     // if ($height > 1)
//     //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     //         echo '<td></td>';
//     //     }
//     // echo '</tr>';

//     // // echo '<tr>';
//     // // echo '<td>Part No: </td>';
//     // // $cols = 1;
//     // // foreach ($data as $item) {
//     // //     echo '<td>' . $item['part_no'] . '</td>';
//     // //     $cols++;
//     // // }
//     // // if ($height > 1)
//     // //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     // //         echo '<td></td>';
//     // //     }
//     // // echo '</tr>';

//     // echo '<tr>';
//     // echo '<td>Amount: </td>';
//     // $cols = 1;
//     // foreach ($data as $item) {
//     //     echo '<td>' . $item['amount'] . '</td>';
//     //     $cols++;
//     // }
//     // if ($height > 1)
//     //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     //         echo '<td></td>';
//     //     }
//     // echo '</tr>';
//     // echo '</tr>';


//     // echo '</table>';
// }

// function get_box_data($post_data)
// {
//     global $db, $tblScanLog;
//     $lane_id = $post_data['lane_id'];
//     $page = $post_data['page'];
//     $box_index = $post_data['box_index'];
//     $lane = get_lane_by_id($lane_id);

//     $area = $lane->area;
//     $height = $lane->height;
//     $allocations = $lane->allocation;
//     // if ($area == 'Free Location')
//     //     $height = 1;

//     $start = $height * $box_index;
//     $end = $height;

//     echo '<div class="row">';


//     if ($area == 'Free Location') {
//         $lane_id_fl = $lane->allocation - $box_index;
//         $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} AND `lane_id_fl` = {$lane_id_fl} ORDER BY `booked_in_time` ASC LIMIT 1";
//     } else {
//         $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id}
//                 ORDER BY `booked_in_time` ASC LIMIT 1";
//     }

//     $result = $db->query($query);
//     if (mysqli_num_rows($result)) {
//         $data = array();
//         while ($row = mysqli_fetch_object($result)) {
//             if (!empty($row->user_id)) {
//                 $user = get_user_info($row->user_id);
//                 $user_name = $user->username;
//             } else {
//                 $user_name = '';
//             }

//             $part = get_part_by_no($row->part);
//             if ($part)
//                 $amount = $part['amount'];
//             else
//                 $amount = '';

//             array_push($data, array(
//                 'date' => date('d/m/Y', strtotime($row->booked_in_time)),
//                 'member' => $user_name,
//                 'part_no' => $row->part,
//                 'amount' => $amount,
//             ));
//         }
//     } else {
//         $left = $allocations - $box_index;
//         $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} AND `lane_id_fl` = {$left}
//         ORDER BY `booked_in_time` ASC LIMIT 1";
//         $result = $db->query($query);
//         $data = array();
//         while ($row = mysqli_fetch_object($result)) {
//             if (!empty($row->user_id)) {
//                 $user = get_user_info($row->user_id);
//                 $user_name = $user->username;
//             } else {
//                 $user_name = '';
//             }

//             $part = get_part_by_no($row->part);
//             if ($part)
//                 $amount = $part['amount'];
//             else
//                 $amount = '';

//             array_push($data, array(
//                 'date' => date('d/m/Y', strtotime($row->booked_in_time)),
//                 'member' => $user_name,
//                 'part_no' => $row->part,
//                 'amount' => $amount,
//             ));
//         }
//     }
//     echo '<div class="col-sm-1 oldest_info_div" style="color: white;text-align: center;">';
//     // echo '<h2>Part: ' . $lane->lane_no . '</h2>';
//     // echo '<h2>Part: ' . $lane->barcode_in . '</h2>';
//     foreach ($data as $item) {
//         echo '<h2>Part: ' . $item['part_no'] . '</h2>';
//     }
//     echo '</div>';
//     echo '<div class="col-sm-3 oldest_info_div" style="color: white;text-align: center;">';
//     if ($area == 'Free Location')
//         echo '<h2 class="text-warning">Area: ' . $area . '</h2>';
//     else
//         echo '<h2 class="text-warning>Area: ' . $area . '</h2>';
//     $colspan = $height - 1;
//     // echo '<h2>Lane: ' . $lane->lane_no . '</h2>';
//     echo '</div>';

//     echo '<div class="col-sm-1 oldest_info_div" style="color: white;text-align: center;">';
//     echo '<h2 class="text-red">Lane: ';
//     if ($area == 'Free Location') {
//         $location = $allocations - $box_index;
//         echo substr($lane->barcode_in, 0, -1) . $location . '</h2>';
//     } else {
//         $s = $allocations - $box_index * $height;
//         $e = $s - $height;
//         for ($i = $box_index * $height + 1; $i <= ($box_index + 1) * $height; $i++) {
//             echo '<h2>' . $i . '</h2>';
//         }
//     }
//     echo '</div>';


//     echo '<div class="col-sm-3 oldest_info_div" style="color: white;text-align: center;">';
//     // echo '<div class="row">';
//     echo '<h2>Date IN: ';
//     $cols = 1;
//     foreach ($data as $item) {
//         echo $item['date'] . '</h2>';
//         $cols++;
//     }
//     // if ($height > 1)
//     //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     //         echo '<h2></h2>';
//     //     }
//     echo '</div>';

//     //Member
//     echo '<div class="col-sm-2 oldest_info_div" style="color: white;text-align: center;">';
//     echo '<h2 class="text-green">Member: ';
//     $cols = 1;
//     foreach ($data as $item) {
//         echo $item['member'] . '</h2>';
//         $cols++;
//     }
//     // if ($height > 1)
//     //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     //         echo '<h2></h2>';
//     //     }
//     echo '</div>';

//     echo '<div class="col-sm-2 oldest_info_div" style="color: white;text-align: center;">';
//     echo '<h2>Amount: ';
//     $cols = 1;
//     foreach ($data as $item) {
//         echo $item['amount'] . '</h2>';
//         $cols++;
//     }
//     // if ($height > 1)
//     //     for ($i = 0; $i < $height - $cols + 1; $i++) {
//     //         echo '<h2></h2>';
//     //     }
//     echo '</div>';
//     echo '</div>';
// }

function get_box_data($post_data)
{
    global $db, $tblScanLog;
    $lane_id = $post_data['lane_id'];
    $page = $post_data['page'];
    $box_index = $post_data['box_index'];
    $lane = get_lane_by_id($lane_id);

    $area = $lane->area;
    $height = $lane->height;
    $allocations = $lane->allocation;
    // if ($area == 'Free Location')
    //     $height = 1;

    $start = $height * $box_index;
    $end = $height;

    // echo '<table class="table">';
    echo '<div class="row" style="justify-content: space-between";>';
    // if ($area == 'Free Location')
    echo '<div class="col-md-4" style="text-align:center; color: yellow"><p class="h2 text-center">Area: '
        . $area . '</p></div>';
    // echo '<div class="col-md-4"></div>';
    // else
    // echo '<td colspan="2">Area: ' . $area . '</td>';
    $colspan = $height - 1;
    // echo '<div class="cold-md-2">Lane: ' . $lane->lane_no . '</div>';
    echo '<div class="col-md-4" style="text-align:center; color: red"><p class="h2">Lane: '
        . $lane->barcode_in . '</p></div>';
    // echo '</tr>';

    // echo '<tr>';
    echo '<div class="col-md-4"><p class="h2 text-center">Location: ';
    if ($area == 'Free Location') {
        $location = $allocations - $box_index;
        echo substr($lane->barcode_in, 0, -1) . $location . '</p></div>';
    } else {
        $s = $allocations - $box_index * $height;
        $e = $s - $height;
        for (
            $i = $box_index * $height + 1;
            $i <= ($box_index + 1) * $height;
            $i++
        ) {
            if ($i < ($box_index + 1) * $height) {
                echo $i . ', ';
            } else {
                echo $i;
            }
        }
        echo '</p></div>';
    }
    echo '</div>';

    if ($area == 'Free Location') {
        $lane_id_fl = $lane->allocation - $box_index;
        $query
            = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} AND `lane_id_fl` = {$lane_id_fl} ORDER BY `booked_in_time` ASC";
    } else {
        $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id}
                ORDER BY `booked_in_time` ASC LIMIT {$start}, {$end}";
    }

    $result = $db->query($query);
    if (mysqli_num_rows($result)) {
        $data = [];
        while ($row = mysqli_fetch_object($result)) {
            if (!empty($row->user_id)) {
                $user = get_user_info($row->user_id);
                if (isset($user->username)) {
                    $user_name = $user->username;
                } else {
                    $user_name = '';
                }
            } else {
                $user_name = '';
            }

            $part = get_part_by_no($row->part);
            if ($part) {
                $amount = $part['amount'];
            } else {
                $amount = '';
            }

            array_push(
                $data,
                [
                    'date' => date('d/m/Y', strtotime($row->booked_in_time)),
                    'member' => $user_name,
                    'part_no' => $row->part,
                    'amount' => $amount,
                ]
            );
        }
    } else {
        $left = $allocations - $box_index;
        $query = "SELECT * FROM {$tblScanLog} WHERE `page` = '{$page}' AND `booked_in` = 1 AND `booked_out` = 0 AND `lane_id` = {$lane->id} AND `lane_id_fl` = {$left}
        ORDER BY `booked_in_time` ASC LIMIT 1";
        $result = $db->query($query);
        $data = [];
        while ($row = mysqli_fetch_object($result)) {
            if (!empty($row->user_id)) {
                $user = get_user_info($row->user_id);
                $user_name = $user->username;
            } else {
                $user_name = '';
            }

            $part = get_part_by_no($row->part);
            if ($part) {
                $amount = $part['amount'];
            } else {
                $amount = '';
            }

            array_push(
                $data,
                [
                    'date' => date('d/m/Y', strtotime($row->booked_in_time)),
                    'member' => $user_name,
                    'part_no' => $row->part,
                    'amount' => $amount,
                ]
            );
        }
    }


    $cols = 1;
    foreach ($data as $item) {
        echo '<div class="row">';
        echo '<div class="col-md-4" style="text-align: center;"><p class="h2">Date IN: '
            . $item['date'] . '</p></div>';
        echo '<div class="col-md-3" style="text-align: center; color:green;"><p class="h2">Member: '
            . $item['member'] . '</p></div>';
        echo '<div class="col-md-2" style="text-align: center;"><p class="h2">Part: '
            . $item['part_no'] . '</p></div>';
        echo '<div class="col-md-3" style="text-align: center; color:blue;"><p class="h2">Amount: '
            . $item['amount'] . '</p></div>';
        echo '</div>';
        $cols++;
    }
    if ($height > 1) {
        for ($i = 0; $i < $height - $cols + 1; $i++) {
            echo '<div></div>';
        }
    }
    // echo '</tr>';

    //Member
    // echo '<tr>';

    // $cols = 1;
    // foreach ($data as $item) {
    //     echo '<div>' . $item['member'] . '</div>';
    //     $cols++;
    // }
    // if ($height > 1)
    //     for ($i = 0; $i < $height - $cols + 1; $i++) {
    //         echo '<div></div>';
    //     }
    // // echo '</tr>';

    // // echo '<tr>';

    // $cols = 1;
    // foreach ($data as $item) {
    //     echo '<div class="col-md-2">' . $item['part_no'] . '</div>';
    //     $cols++;
    // }
    // if ($height > 1)
    //     for ($i = 0; $i < $height - $cols + 1; $i++) {
    //         echo '<div></div>';
    //     }
    // // echo '</tr>';

    // // echo '<tr>';

    // $cols = 1;
    // foreach ($data as $item) {
    //     echo '<div class="col-md-1">' . $item['amount'] . '</div>';
    //     $cols++;
    // }
    // if ($height > 1)
    //     for ($i = 0; $i < $height - $cols + 1; $i++) {
    //         echo '<div></div>';
    //     }
    // echo '</tr>';
    // echo '</tr>';


    // echo '</div>';
}


function go_to_overstock($post_data)
{
    global $db, $tblOverstock;
    $part = $post_data['part'];
    $page = $post_data['page'];
    $stock_action = $_SESSION['stocking_action'];
    if ($stock_action == 'in') {
        $query
            = "INSERT INTO {$tblOverstock}  (`part`, `page`, `created_at`) value ('{$part}', '{$page}', NOW())";
        $db->query($query);
    } else {
        $query
            = "SELECT * FROM {$tblOverstock} WHERE part='{$part}' AND page ='{$page}' LIMIT 1";
        $result = $db->query($query);
        $row = mysqli_fetch_object($result);
        if ($row) {
            $id = $row->id;
            $query = "DELETE FROM {$tblOverstock} WHERE id = {$id}";
            $db->query($query);
        }
    }
    echo 'Success';
}

function overstock_view()
{
    global $db, $tblOverstock;
    $query
        = "SELECT part, COUNT(part) as 'quantity' FROM {$tblOverstock} GROUP BY part";
    $result = $db->query($query);
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr><th>Part</th><th>Quantity</th></tr>';
    echo '</thead>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['part'] . '</td>';
        echo '<td>X' . $row['quantity'] . '</td>';
        echo '</tr>';
    }
    echo "</table>";
}

/*get barcode in from lane number*/
function get_barcodein_from_laneno()
{
    $lane_no = $_POST["lane_id"];
    global $db, $tblStocking;
    $query
        = "SELECT barcode_in as 'barcode' FROM {$tblStocking} where `lane_no` ='"
        . $lane_no . "'";
    $result = $db->query($query);

    $row = mysqli_fetch_object($result);
    if ($row) {
        echo $row->barcode;
    } else {
        echo "failure";
    }
}

/*
 * Conveyance
 */
function read_kanban_box($post_data)
{
    global $dbMssql, $tblConveyancePicks, $tblDolly, $tblOPRSetting;
    $pick_date = convert_date_string($post_data['pick_date']);
    $kanban_id = $post_data['kanban_id'];
    $status = $post_data['status'];
    $zone = "0";
    // print_r($post_data);exit();
    if (isset($post_data['zone'])) {
        $zone = $post_data['zone'];
    }
    if ($zone == "0") {
        $zone = "All";
    }
    $error = '';

    //get cycle
    $max_cycle = -1;
    if ($post_data['cycle'] == -1) {
        $query1 = "SELECT MAX(cycle) as max_cycle FROM {$tblConveyancePicks} WHERE kanban_date = '{$pick_date}'";
        $result1 = sqlsrv_query($dbMssql, $query1);
        $r1 = sqlsrv_fetch_object($result1);
        $max_cycle = $r1->max_cycle;
        
        $data['query1'] = $query1;
    }

    if ($post_data['cycle'] == -1) // $cur = $max_cycle;
    {
        $cur = 1;
    } else {
        $cur = $post_data['cycle'];
    }
    //Get total unpicked kanban

    $data['post_data_cycle'] = $post_data['cycle'];
    $data['cur'] = $cur;
    
    $query = "SELECT DISTINCT pk.dolly FROM part_to_kanban pk INNER JOIN {$tblConveyancePicks} cp ON cp.kanban_date='{$pick_date}' AND cp.kanban = pk.kanban AND LEN(pk.dolly) > 0 AND cp.cycle={$cur} ORDER BY pk.dolly";

    $result = sqlsrv_query($dbMssql, $query);
    $res_zones = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $res_zones[] = $row;
    }

    $data['zones'] = $res_zones;
    $data['sql'] = $query;
    $num_rows = 0;

    if ($max_cycle) {
        //Get pick status
        $query = "SELECT * FROM {$tblConveyancePicks} WHERE kanban_date = '{$pick_date}' AND cycle={$cur}";
        $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        $total = sqlsrv_num_rows($result);

        //Get picked kanban
        if ($post_data['status'] == 'delivery') {
            $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_delivered = 1 AND kanban_date = '{$pick_date}' AND cycle={$cur}";
        } else {
            $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed = 1 AND kanban_date = '{$pick_date}' AND cycle={$cur}";
        }
        $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        $total_picked = sqlsrv_num_rows($result);
        $data['pick_sql'] = $query;
        $data['pick_status'] = $total_picked . '/' . $total;
        
        if ($kanban_id == 0) {
            if ($status == 'pick') {
                if ($zone != 'All') //Help or Other filters
                {
                    if ($zone != 'Help') //other filter
                    {
                        $query
                            = "SELECT cp.* FROM {$tblConveyancePicks} as cp WHERE cp.kanban_date = '{$pick_date}' AND cp.cycle='{$post_data['cycle']}' AND cp.dolly = '{$zone}' ORDER BY  cp.is_help ASC, cp.is_completed ASC, cp.is_delivered ASC, cp.imported_at ASC, cp.pick_seq ASC, cp.kanban ASC";
                    } else //Help filter
                    {
                        $query
                            = "SELECT cp.* FROM {$tblConveyancePicks} as cp WHERE cp.kanban_date = '{$pick_date}' AND cp.cycle='{$post_data['cycle']}' AND cp.is_help = 1 ORDER BY  cp.is_help ASC, cp.is_completed ASC, cp.is_delivered ASC, cp.imported_at ASC, cp.pick_seq ASC, cp.kanban ASC";
                    }
                } else // All filter
                {
                    $query
                        = "SELECT * FROM {$tblConveyancePicks} WHERE dolly != '' AND kanban_date = '{$pick_date}' AND cycle=1 ORDER BY  is_help ASC, is_completed ASC, imported_at ASC, pick_seq ASC, kanban ASC";
                }

                $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
                $num_rows = sqlsrv_num_rows($result);
            } else {
                if ($zone != 'All') //Help or Other filters
                {
                    if ($zone != 'Help') //other filter
                    {
                        $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_delivered=0 AND kanban_date = '{$pick_date}' AND cycle='{$post_data['cycle']}' AND dolly = '{$zone}' ORDER BY is_help ASC, is_delivered ASC, imported_at ASC, kanban ASC";
                    } else //Help filter
                    {
                        $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_delivered=0 AND kanban_date = '{$pick_date}' AND cycle='{$post_data['cycle']}' AND is_help=1 ORDER BY is_help ASC, is_delivered ASC, imported_at ASC, kanban ASC";
                    }
                } else // All filter
                {
                    $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_delivered=0 AND kanban_date = '{$pick_date}' AND cycle='{$cur}' ORDER BY is_help ASC, is_delivered ASC, imported_at ASC, kanban ASC";
                }
                
                $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
                $num_rows = sqlsrv_num_rows($result);
            }
        } else {
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE id = {$kanban_id}";
            $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
            $num_rows = sqlsrv_num_rows($result);
        }

        $tmp = sqlsrv_query($dbMssql, "SELECT * FROM {$tblDolly}");

        if ($tmp === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        
        $dolly_colors = [];
        while ($row = sqlsrv_fetch_array($tmp, SQLSRV_FETCH_ASSOC)) {
            $dolly_colors[] = $row;
        }

        if (!empty($dolly_colors)) {
            $dolly_colors = array_combine(
                array_column($dolly_colors, "name"),
                array_column($dolly_colors, "color")
            );
        }

        if($num_rows == 0 && $status='delivery'){
            $data['is_delivered'] = 1;
        }
        if ($num_rows > 0) {
            $res = [];
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $res[] = $row;
            }
            if ($status == 'delivery') {
                $arr = [
                    "SPS2",
                    "ASSY1",
                    "ASSY2",
                    "SB1",
                    "SB2",
                    "ASSY3",
                    "HS1",
                    "KARAKURI",
                    "CH2",
                    "SPS3",
                    "SPS4"
                ];
                usort($res, function ($a, $b) use ($arr) {
                    $ind1 = 1111111;
                    $ind2 = 1111111;
                    foreach ($arr as $index => $each_arr) {
                        if (
                            strpos($a['delivery_address'], $each_arr)
                            !== false
                        ) {
                            $ind1 = $index;
                        }
                        if (
                            strpos($b['delivery_address'], $each_arr)
                            !== false
                        ) {
                            $ind2 = $index;
                        }
                    }
                    if ($ind1 > $ind2) {
                        return 1;
                    } elseif ($ind1 < $ind2) {
                        return -1;
                    } else {
                        return strcmp(
                            $a['delivery_address'],
                            $b['delivery_address']
                        );
                    }
                });
            }

            $kanban = $res[0];
            
            $data['kanban_id'] = $kanban['id'];
            $data['kanban'] = $kanban['kanban'];
            $data['part_number'] = $kanban['part_number'];
            $data['address'] = $kanban['address'];
            $data['is_completed'] = $kanban['is_completed'];
            $data['is_delivered'] = $kanban['is_delivered'];
            $data['is_help'] = $kanban['is_help'];
            $data['dolly_location'] = $kanban['dolly_location'];
            $data['delivery_address'] = $kanban['delivery_address'];
            $data['cur_cycle'] = $kanban['cycle'];
            $data['max_cycle'] = $max_cycle;
            $data['dolly'] = $kanban['dolly'];
            $data['dolly_color'] = $dolly_colors[$data['dolly']];
            $_SESSION['overview_pick_pick'] = $data['pick_status'];
            $_SESSION['overview_pick_zone'] = $zone;
            $_SESSION['overview_pick_cycle'] = $data['cur_cycle'] . '/'
                . $data['max_cycle'];
            $_SESSION['overview_pick_kanban'] = $data['kanban'];
            $_SESSION['overview_pick_user'] = $_SESSION['user']['username'];
            $_SESSION['overview_pick_OPR'] = ceil($total
                / get_opr_setting('pick') * 100);

            $_SESSION['overview_delivery_pick'] = $data['pick_status'];
            $_SESSION['overview_delivery_zone'] = $data['dolly'];
            $_SESSION['overview_delivery_cycle'] = $data['cur_cycle'] . '/'
                . $data['max_cycle'];
            $_SESSION['overview_delivery_kanban'] = $data['kanban'];
            $_SESSION['overview_delivery_address'] = $data['address'];
            $_SESSION['overview_delivery_user'] = $_SESSION['user']['username'];
            $_SESSION['overview_delivery_OPR'] = ceil($total
                / get_opr_setting('delivery') * 100);

            $data['mySession'] = $_SESSION;
        }
    } else {
        $error = "There is no kanban";
    }
    $data['error'] = $error;
    echo json_encode($data, true);
}

function get_opr_setting($type)
{
    global $tblOPRSetting, $db;
    $query = "SELECT * FROM {$tblOPRSetting} WHERE opr_type = '{$type}'";
    $result = $db->query($query);
    $k = mysqli_fetch_object($result);
    if ($k) {
        return $k->value;
    } else {
        return '';
    }
}

function get_part_number_by_kanban($kanban)
{
    global $tblPart2Kanban, $db;
    $query = "SELECT * FROM {$tblPart2Kanban} WHERE kanban = '{$kanban}'";
    $result = $db->query($query);
    $k = mysqli_fetch_object($result);
    if ($k) {
        return $k->part_number;
    } else {
        return '';
    }
}

function get_pick_address_by_kanban_and_delivery($kanban, $delivery_address)
{
    global $tblPart2Kanban, $dbMssql;
    $d_address = str_replace(".", "", $delivery_address);
    $d_address = str_replace("-", "", $d_address);
    $query = "SELECT * FROM {$tblPart2Kanban} WHERE kanban LIKE '{$kanban}%' AND REPLACE(REPLACE(delivery_address, '-', ''), '.' ,'') = '{$d_address}'";
    
    $result = sqlsrv_query($dbMssql, $query);
    $k = sqlsrv_fetch_object($result);
    
    if ($k) {
        // var_dump($k);
        return $k->pick_address;
    } else {
        return '';
    }
}


function get_pick_seq_by_kanban_and_delivery($kanban, $delivery_address)
{
    global $tblPart2Kanban, $dbMssql;
    $d_address = str_replace(".", "", $delivery_address);
    $d_address = str_replace("-", "", $d_address);
    $query = "SELECT * FROM {$tblPart2Kanban} WHERE kanban LIKE '{$kanban}%' AND REPLACE(REPLACE(delivery_address, '-', ''), '.' ,'') = '{$d_address}'";
    $result = sqlsrv_query($dbMssql, $query);
    $k = sqlsrv_fetch_object($result);
    if ($k) {
        return $k->pick_seq;
    } else {
        return '';
    }
}


function get_dolly_by_kanban_and_delivery($kanban, $delivery_address)
{
    global $tblPart2Kanban, $dbMssql;
    $d_address = str_replace(".", "", $delivery_address);
    $d_address = str_replace("-", "", $d_address);
    $query = "SELECT * FROM {$tblPart2Kanban} WHERE kanban LIKE '{$kanban}%' AND REPLACE(REPLACE(delivery_address, '-', ''), '.' ,'') = '{$d_address}'";
    $result = sqlsrv_query($dbMssql, $query);
    $k = sqlsrv_fetch_object($result);
    if ($k) {
        return $k->dolly;
    } else {
        return '';
    }
}


function read_pick_list($post_data)
{
    global $dbMssql, $tblConveyancePicks, $tblDolly;
    $pick_date = convert_date_string($post_data['pick_date']);
    $current_kanban_id = $post_data['current_kanban_id'];
    $status = $post_data['status'];
    if (isset($post_data['zone'])) {
        $zone = $post_data['zone'];
    }
    $cur = $post_data['cycle'];
    if ($cur == -1) $cur =  1;
    if ($status == 'pick') {
        if ($zone != '0') //Help or Other filters
        {
            if ($zone != 'Help') //other filter
            {
                $query
                    = "SELECT cp.* FROM {$tblConveyancePicks} as cp WHERE cp.kanban_date = '{$pick_date}' AND cp.cycle='{$cur}' AND cp.dolly = '{$zone}' ORDER BY cp.address ASC, cp.is_help ASC, cp.is_completed ASC, cp.is_delivered ASC, cp.imported_at ASC, cp.pick_seq ASC, cp.kanban ASC";
            } else //Help filter
            {
                $query
                    = "SELECT cp.* FROM {$tblConveyancePicks} as cp WHERE cp.kanban_date = '{$pick_date}' AND cp.cycle='{$cur}' AND cp.is_help = 1 ORDER BY cp.address ASC, cp.is_help ASC, cp.is_completed ASC, cp.is_delivered ASC, cp.imported_at ASC, cp.pick_seq ASC, cp.kanban ASC";
            }
        } else // All filter
        {
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE dolly != '' AND kanban_date = '{$pick_date}' AND cycle='{$cur}' ORDER BY [address] ASC, is_help ASC, is_completed ASC, imported_at ASC, pick_seq ASC, kanban ASC";
        }
    }else{
        if ($zone != '0') //Help or Other filters
        {
            if ($zone != 'Help') //other filter
            {
                $query = "SELECT * FROM {$tblConveyancePicks} WHERE kanban_date = '{$pick_date}' AND cycle='{$cur}' AND dolly = '{$zone}' ORDER BY is_help ASC, is_delivered ASC, imported_at ASC, kanban ASC";
            } else //Help filter
            {
                $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_delivered=0 AND kanban_date = '{$pick_date}' AND cycle='{$cur}' AND is_help=1 ORDER BY is_help ASC, is_delivered ASC, imported_at ASC, kanban ASC";
            }
        } else // All filter
        {
            $query = "SELECT * FROM {$tblConveyancePicks} WHERE dolly != '' AND kanban_date = '{$pick_date}' AND cycle='{$cur}' ORDER BY is_help ASC, is_delivered ASC, imported_at ASC, kanban ASC";
        }
    }

    // echo $query;
    $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
    $num_rows = sqlsrv_num_rows($result);
    $n = intval(floor($num_rows / 2)) + 1;
    $picks = [];
    $res = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $res[] = $row;
    }

    if ($status == 'delivery') {
        $arr = [
            "SPS2",
            "ASSY1",
            "ASSY2",
            "SB1",
            "SB2",
            "ASSY3",
            "HS1",
            "KARAKURI",
            "CH2",
            "SPS3",
            "SPS4"
        ];
        usort($res, function ($a, $b) use ($arr) {
            $ind1 = 1111111;
            $ind2 = 1111111;
            foreach ($arr as $index => $each_arr) {
                if (str_contains($a['delivery_address'], $each_arr)) {
                    $ind1 = $index;
                }
                if (str_contains($b['delivery_address'], $each_arr)) {
                    $ind2 = $index;
                }
            }
            if ($ind1 > $ind2) {
                return 1;
            } elseif ($ind1 < $ind2) {
                return -1;
            } else {
                return strcmp($a['delivery_address'], $b['delivery_address']);
            }
        });
    }
    $tmp = sqlsrv_query($dbMssql, "SELECT * FROM {$tblDolly}");
    $dolly_colors = [];
    while ($row = sqlsrv_fetch_array($tmp, SQLSRV_FETCH_ASSOC)) {
        $dolly_colors[] = $row;
    }
    $dolly_colors = array_combine(
        array_column($dolly_colors, "name"),
        array_column($dolly_colors, "color")
    );

    // if current_kanban_id is 1st item in list, move it to the bottom
    /*if($num_rows > 0 && $res[0]['kanban'] == $current_kanban_id) {
    $tmp_kanban = $res[0];
    $res[0] = $res[$num_rows - 1];
    $res[$num_rows - 1] = $tmp_kanban;
    // $tmp_kanban = $res[0]['kanban'];
    }*/
    foreach ($res as $pick) {
        // $dolly = get_dolly_by_name($pick['kanban']);
        // $dolly = get_dolly_by_name_delivery($pick['kanban'], $pick['delivery_address']);
        // $dolly_name = '';
        // $dolly_color = '';
        // if ($dolly) {
        //     $dolly_name = $dolly->dolly;
        //     if(isset($dolly_colors[$dolly_name]))
        //         $dolly_color = $dolly_colors[$dolly_name];
        // }

        $picks[] = [
            'id' => $pick['id'],
            'kanban' => $pick['kanban'],
            'address' => $pick['address'],
            // 'dolly' => $dolly_name,
            'dolly' => $pick['dolly'],
            'dolly_location' => $pick['dolly_location'],
            // 'color' => $dolly_color,
            'color' => $dolly_colors[$pick['dolly']] ?? null,
            'is_completed' => $pick['is_completed'],
            'is_help' => $pick['is_help'],
            'is_delivered' => $pick['is_delivered'],
            'delivery_address' => $pick['delivery_address'],
            'cycle' => $pick['cycle'],
        ];
    }
    // usort($picks, fn($a, $b) => strcmp($a['kanban'], $b['kanban']));

    echo '<div class="col-md-6" style="min-height: 360px;">';
    if ($num_rows > 0) {
        echo '<table class="table table-bordered">';
        if ($picks[0]['id'] == $current_kanban_id) {
            make_pick_list_tr_first($picks[0], $status);
        } else {
            make_pick_list_tr($picks[0], $status);
        }
        // make_pick_list_tr_first($picks[0], $status);
        for ($i = 1; $i < $n; $i++) {
            make_pick_list_tr($picks[$i], $status);
        }
        echo '</table>';
    }
    echo '</div>';

    echo '<div class="col-md-6"  style="min-height: 360px;">';
    if ($num_rows > 0) {
        echo '<table class="table table-bordered">';
        for ($i = $n; $i < $num_rows; $i++) {
            make_pick_list_tr($picks[$i], $status);
        }
        echo '</table>';
    }
    echo '</div>';
}

function make_pick_list_tr_first($pick, $status)
{
    if ($status == 'pick') {
        if ($pick['is_completed'] == 1) {
            $class = 'completed-kanban';
        } else {
            if ($pick['is_help'] == 1) {
                $class = 'helped-kanban';
            } else {
                $class = 'uncompleted-kanban';
            }
        }
    } else {
        if ($pick['is_delivered'] == 1) {
            $class = 'completed-kanban';
        } else {
            if ($pick['is_help'] == 1) {
                $class = 'helped-kanban';
            } else {
                $class = 'uncompleted-kanban';
            }
        }
    }
    echo '<tr style="background-color : #C4C4C4" data-kanban="' . $pick['id']
        . '">';
    echo '<td style="text-align: center;">';
    echo '<span>kanban</span><br/>';
    echo '<span class="' . $class . ' select-kanban" data-kanban="' . $pick['id'] . '">'
        . $pick['kanban'] . '</span>';
    echo '</td>';
    echo '<td style="text-align: center;">';
    echo '<span>Address</span><br/>';
    if ($status == 'pick') {
        echo '<span class="' . $class . '  select-kanban" data-kanban="' . $pick['id']
            . '">' . $pick['address'] . '</span>';
    } else {
        echo '<span class="' . $class . '  select-kanban" data-kanban="' . $pick['id']
            . '">' . $pick['delivery_address'] . '</span>';
    }
    echo '</td>';
    // echo '<td style="text-align: center; background-color: ' . $pick['color'] . '">';
    // echo '<button class="btn  select-kanban" style="width: 100px; height: 30px; background-color: ' . $pick['color'] . '"  data-kanban="' . $pick['id'] . '">'. strtoupper($pick['dolly']) . '</button>';
    echo '<td class="select-kanban" style="text-align: center; vertical-align: middle; font-size: x-large; color: white; background-color: '
        . $pick['color'] . '"  data-kanban="' . $pick['id'] . '">'
        . strtoupper($pick['dolly']) . '<p class="cycle">CYCLE ' . $pick['cycle']
        . '</p></td>';
    echo '</td>';
    echo '</tr>';
}

function make_pick_list_tr($pick, $status)
{
    if ($status == 'pick') {
        if ($pick['is_completed'] == 1) {
            $class = 'completed-kanban';
        } else {
            if ($pick['is_help'] == 1) {
                $class = 'helped-kanban';
            } else {
                $class = 'uncompleted-kanban';
            }
        }
    } else {
        if ($pick['is_delivered'] == 1) {
            $class = 'completed-kanban';
        } else {
            if ($pick['is_help'] == 1) {
                $class = 'helped-kanban';
            } else {
                $class = 'uncompleted-kanban';
            }
        }
    }

    echo '<tr data-kanban="' . $pick['id'] . '">';
    echo '<td style="text-align: center;">';
    echo '<span>kanban</span><br/>';
    echo '<span class="' . $class . ' select-kanban" data-kanban="' . $pick['id'] . '">'
        . $pick['kanban'] . '</span>';
    echo '</td>';
    echo '<td style="text-align: center;">';
    echo '<span>Address</span><br/>';
    if ($status == 'pick') {
        echo '<span class="' . $class . '  select-kanban" data-kanban="' . $pick['id']
            . '">' . $pick['address'] . '</span>';
    } else {
        echo '<span class="' . $class . '  select-kanban" data-kanban="' . $pick['id']
            . '">' . $pick['delivery_address'] . '</span>';
    }
    echo '</td>';
    // echo '<td style="text-align: center; background-color: ' . $pick['color'] . '">';
    echo '<td class="select-kanban" style="text-align: center; vertical-align: middle; font-size: x-large; color: white; background-color: '
        . $pick['color'] . '"  data-kanban="' . $pick['id'] . '">'
        . strtoupper($pick['dolly']) . '<p class="cycle">CYCLE ' . $pick['cycle']
        . '</p></td>';
    // echo '<button class="btn  select-kanban" style="width: 100px; height: 30px; background-color: ' . $pick['color'] . '"  data-kanban="' . $pick['id'] . '">'. strtoupper($pick['dolly']) . '</button>';
    echo '</td>';
    echo '</tr>';
}

function conveyance_pick($post_data)
{
    global $tblConveyancePicks, $dbMssql, $current;
    $kanban_id = $post_data['kanban_id'];
    $user = $_SESSION['user']['user_id'];
    $current = $post_data['today'];

    if (isset($post_data['reason'])) {
        $reason = $post_data['reason'];
    } else {
        $reason = 0;
    }
    if ($kanban_id) {
        $query = "";
        if ($post_data['is_pick'] == "1") {
            $query
                = "UPDATE {$tblConveyancePicks} SET is_completed = 1, picked_at ='{$current}', completed_reason = {$reason} WHERE id = {$kanban_id}";
        } else {
            $query
                = "UPDATE {$tblConveyancePicks} SET is_delivered = 1, deliveried_at ='{$current}', completed_reason = {$reason} WHERE id = {$kanban_id}";
        }
        // echo $query;
        $result = sqlsrv_query($dbMssql, $query);
        if ($result) {
            echo 'ok';
        } else {
            echo 'failed';
        }
    } else {
        echo 'failed';
    }
}


function search_barcode($post_data)
{
    // global $tblConveyancePicks, $db, $current;
    // $kanban_id = $post_data['kanban_id'];
    // if ($kanban_id) {
    //     $query = "UPDATE {$tblConveyancePicks} SET is_completed = 1, picked_at ='{$current}' WHERE id = {$kanban_id}";
    //     $result = $db->query($query);
    //     if ($result)
    //         echo 'ok';
    //     else
    //         echo 'failed';
    // } else {
    //     echo 'failed';
    // }
    echo 'ok';
}

function check_pick_finish($post_data)
{
    global $tblConveyancePicks, $dbMssql;
    $pick_date = convert_date_string($post_data['pick_date']);
    $query1
        = "SELECT MAX(cycle) as max_cycle FROM {$tblConveyancePicks} WHERE kanban_date = '{$pick_date}'";
    $result1 = sqlsrv_fetch_object(sqlsrv_query($dbMssql, $query1));
    $max_cycle = $result1->max_cycle;
    $status = $post_data['status'];

    if ($status == 'pick') {
        if ($post_data['cycle'] == -1) {
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed = 0 AND kanban_date = '{$pick_date}'";
        } else // var_dump()
        {
            if ($post_data['zone'] == -1) {
                $query
                    = "SELECT * FROM {$tblConveyancePicks} WHERE cycle={$post_data['cycle']} AND is_completed = 0 AND kanban_date = '{$pick_date}'";
            } else {
                $query
                    = "SELECT * FROM {$tblConveyancePicks} WHERE cycle='{$post_data['cycle']}' AND dolly='{$post_data['zone']}' AND is_completed = 0 AND kanban_date = '{$pick_date}'";
            }
        }

        $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        $num = sqlsrv_num_rows($result);
        var_dump($num);
        if ($num == 0) {
            if (
                $post_data['cycle'] == $max_cycle
                || $post_data['cycle'] == -1
            ) {
                echo 'finish';
            } else {
                echo 'success';
            }
        } else {
            $query1
                = "SELECT * FROM {$tblConveyancePicks} WHERE cycle={$post_data['cycle']} AND is_completed = 0 AND is_help = 1 AND kanban_date = '{$pick_date}' AND dolly='{$post_data['zone']}'";
            $result1 = sqlsrv_query($dbMssql, $query1, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
            $num1 = sqlsrv_num_rows($result1);
            if ($num1 == 0) {
                echo 'in_progress';
            } else {
                if ($num1 == $num) {
                    echo 'success';
                } else {
                    echo 'in_help';
                }
            }
        }
    } else {
        $query
            = "SELECT * FROM {$tblConveyancePicks} WHERE cycle={$post_data['cycle']} AND is_delivered = 0 AND kanban_date='{$pick_date}'";
        $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        $num = sqlsrv_num_rows($result);
        if ($num == 0) {
            if (
                $post_data['cycle'] == $max_cycle
                || $post_data['cycle'] == -1
            ) {
                if ($max_cycle) {
                    echo 'finish';
                } else {
                    echo 'not';
                }
            } else {
                echo 'success';
            }
        } else {
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE cycle={$post_data['cycle']} AND is_completed = 1 AND is_delivered = 0 AND is_help = 1 AND kanban_date='{$pick_date}'";
            $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
            $num = sqlsrv_num_rows($result);
            if ($num == 0) {
                echo 'in_progress';
            } else {
                echo 'in_help';
            }
        }
    }
}

function conveyance_andon_help($post_data)
{
    global $tblConveyancePicks, $dbMssql;
    $kanban_id = $post_data['kanban_id'];
    $status = $post_data['status'];
    $today = $post_data['today'];
    $user = $_SESSION['user']['user_id'];

    if ($kanban_id) {
        if ($status == 'pick') {
            $query
                = "UPDATE {$tblConveyancePicks} SET is_completed = 0,is_pick = 1, is_help = 1, helped_user = '{$user}', helped_at = '{$today}' WHERE id = {$kanban_id}";
        } else {
            $query
                = "UPDATE {$tblConveyancePicks} SET is_delivered = 0,is_pick = 0, is_help = 1, helped_user = '{$user}', helped_at = '{$today}' WHERE id = {$kanban_id}";
        }
        $result = sqlsrv_query($dbMssql, $query);;
        if ($result) {
            echo 'ok';
        } else {
            echo 'failed';
        }
    } else {
        echo 'failed';
    }
}

function confirm_conveyance_andon_help($post_data)
{
    global $tblConveyancePicks, $tblHelpAlarm, $dbMssql;
    $kanban_id = $post_data['kanban_id'];
    $helped_user = $post_data['confirm_user_id'];
    if (isset($post_data['reason'])) {
        $reason = $post_data['reason'];
    } else {
        $reason = 0;
    }
    // $is_completed = isset($post_data['is_completed']) ? $post_data['is_completed'] : 0;
    // $is_delivered = isset($post_data['is_delivered']) ? $post_data['is_delivered'] : 0;
    if ($kanban_id) {
        if (isset($post_data['is_completed'])) {
            $query
                = "UPDATE {$tblConveyancePicks} SET picked_at = '{$post_data['today']}', is_completed = 1, completed_reason = {$reason} WHERE id = {$kanban_id}";
        }
        if (isset($post_data['is_delivered'])) {
            $query
                = "UPDATE {$tblConveyancePicks} SET deliveried_at = '{$post_data['today']}', is_delivered = 1, delivered_reason = {$reason} WHERE id = {$kanban_id}";
        }
        $result = sqlsrv_query($dbMssql, $query);
        if ($result) {
            echo 'ok';
        } else {
            echo $query;
        }
    } else {
        echo 'failed';
    }
}

function read_delivery_kanban_boxes($post_data)
{
    global $db, $tblConveyancePicks;
    $kanban_id = $post_data['kanban_id'];
    $direction = $post_data['direction'];

    if ($direction == 'prev') {
        if (empty($kanban_id)) {
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed =1 ORDER BY `imported_at` ASC LIMIT 3";
        } else {
            $max = $kanban_id + 1;
            $min = $kanban_id - 2;
            if ($min < 0) {
                $min = 0;
            }
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed = 1 AND id <= {$max} AND id > {$min} ORDER BY `imported_at` ASC LIMIT 3";
        }
    } else {
        if (empty($kanban_id)) {
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed = 1 ORDER BY `imported_at` ASC LIMIT 3";
        } else {
            $max = $kanban_id + 1;
            $min = $kanban_id - 2;
            $query
                = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed = 1 AND id <= {$max} AND id > {$min} ORDER BY `imported_at` ASC LIMIT 3";
        }
    }
    $result = $db->query($query);
    $num_rows = mysqli_num_rows($result);
    if ($num_rows == 0) {
        for ($i = 1; $i <= 3; $i++) {
            make_d_kanban_box($i);
        }
    } else {
        $rows = [];
        if (empty($kanban_id)) {
            array_push($rows, null);
        }
        while ($row = mysqli_fetch_array($result)) {
            array_push(
                $rows,
                [
                    'id' => $row['id'],
                    'kanban' => $row['kanban'],
                    'location' => $row['location'],
                    'is_delivered' => $row['is_delivered'],
                    'is_help' => $row['is_help'],
                ]
            );
        }
        if (empty($kanban_id) && count($rows) > 3) {
            unset($rows[count($rows) - 1]);
        }

        if ($direction == 'prev') {
            if (count($rows) == 2) {
                array_unshift($rows, null);
            }
            if (count($rows) == 1) {
                array_unshift($rows, null);
                array_unshift($rows, null);
            }
        } else {
            if (count($rows) == 2) {
                array_push($rows, null);
            }

            if (count($rows) == 1) {
                array_push($rows, null);
                array_push($rows, null);
            }
        }
        foreach ($rows as $index => $row) {
            $i = $index + 1;
            make_d_kanban_box($i, $row);
        }
    }
}

function make_d_kanban_box($index, $row = null)
{
    global $tblConveyancePicks, $db, $today;
    if ($row == null) {
        $kanban = '';
        $location = '';
        $kanban_id = '';
        $is_delivered = 0;
        $is_help = 0;
    } else {
        $kanban = $row['kanban'];
        $location = $row['location'];
        $kanban_id = $row['id'];
        $is_delivered = $row['is_delivered'];
        $is_help = $row['is_help'];
    }

    if ($index == 2) {
        $bg_class = 'blue-kanban';
    } else {
        $bg_class = 'grey-kanban';
    }

    if ($is_delivered == 1) {
        $bg_class = 'green-kanban';
    }
    if ($is_help == 1) {
        $bg_class = 'red-kanban';
    }

    if ($index == 1) {
        echo '<div class="col-md-3 m-0 p-0">';
        echo '<div class="d-flex flex-column align-items-center kanban '
            . $bg_class . '">';

        echo '<div class="item-div">';
        echo '<span>Prev.</span>';
        echo '</div>';

        echo '<div class="item-div" id="input_div">';
        echo '<input type="text" class="form-control kanban_input" id="kanban_input" name="kanban_input" autofocus placeholder="Kanban">';
        echo '<input type="hidden" id="input_type" name="input_type" value="kanban">';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<h2>Kanban</h2>';
        echo '<h1>' . $kanban . '</h1>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<a style="cursor: pointer;" id="go_prev_kanban"><img src="assets/img/prev.png" style="width: 15%; height: auto;"></a>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<h2>Location</h2>';
        echo '<h1>' . $location . '</h1>';
        echo '</div>';

        echo '<input type="hidden" id="prev_id" value="' . $kanban_id . '">';
        echo '</div>';
        echo '</div>';
    }

    if ($index == 2) {
        //Get total unpicked kanban
        $query = "SELECT * FROM {$tblConveyancePicks} WHERE is_completed = 1";
        $result = $db->query($query);
        $total_unpicked = mysqli_num_rows($result);

        //Get picked kanban
        $today_start = $today . " 00:00:00";
        $query
            = "SELECT * FROM {$tblConveyancePicks} WHERE is_delivered = 1 AND deliveried_at > '{$today_start}'";
        $result = $db->query($query);
        $total_picked = mysqli_num_rows($result);

        echo '<div class="col-md-6 m-0 p-0">';
        echo '<div class="d-flex flex-column kanban ' . $bg_class
            . '" id="current_kanban_area">';

        echo '<div class="item-div">';
        echo '<span>current</span>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<span class="date-string m-2">' . date('d/m/Y', strtotime($today))
            . '</span>';
        echo '<span class="date-string m-2">Delivery List</span>';
        echo '<span class="pick-list  m-2">' . $total_picked . '/' . $total_unpicked
            . '</span>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<h2 style="color: #b8b7b7;">Kanban: </h2>';
        echo '<h1>' . $kanban . '</h1>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<h2 style="color: #b8b7b7;">Address: </h2>';
        echo '<h1>' . $location . '</h1>';
        echo '</div>';
        echo '<input type="hidden" id="current_kanban_no" value="' . $kanban . '">';
        echo '<input type="hidden" id="current_kanban_location" value="'
            . $location . '">';
        echo '<input type="hidden" id="current_kanban_id" value="' . $kanban_id
            . '">';
        if ($is_help == 1) {
            echo '<input type="hidden" id="chk_is_help" value="1">';
        } else {
            echo '<input type="hidden" id="chk_is_help" value="0">';
        }
        echo '</div>';
        echo '</div>';
    }

    if ($index == 3) {
        echo '<div class="col-md-3 m-0 p-0">';
        echo '<div class="d-flex flex-column align-items-center kanban '
            . $bg_class . '">';

        echo '<div class="item-div">';
        echo '<span>Next</span>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<h2>Kanban</h2>';
        echo '<h1>' . $kanban . '</h1>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<a style="cursor: pointer;" id="go_next_kanban"><img src="assets/img/next.png" style="width: 15%; height: auto;"></a>';
        echo '</div>';

        echo '<div class="item-div">';
        echo '<h2>Address</h2>';
        echo '<h1>' . $location . '</h1>';
        echo '</div>';

        echo '<input type="hidden" id="next_id" value="' . $kanban_id . '">';
        echo '</div>';
        echo '</div>';
    }
}

function conveyance_delivery($post_data)
{
    global $tblConveyancePicks, $dbMssql, $current;
    $kanban_id = $post_data['kanban_id'];
    if (isset($post_data['reason'])) {
        $reason = $post_data['reason'];
    } else {
        $reason = 0;
    }
    if ($kanban_id) {
        $query
            = "UPDATE {$tblConveyancePicks} SET is_delivered = 1, deliveried_at ='{$current}', delivered_reason = {$reason} WHERE id = {$kanban_id}";
        $result = sqlsrv_query($dbMssql, $query);
        if ($result) {
            echo 'ok';
        } else {
            echo 'failed';
        }
    } else {
        echo 'failed';
    }
}

/*
 * Dolly Admin
 */
function get_all_dolly()
{
    global $db, $tblDolly;
    $query = "SELECT * FROM {$tblDolly} ORDER BY `name`";
    $result = $db->query($query);
    $dolly = [];
    while ($item = mysqli_fetch_object($result)) {
        array_push($dolly, $item);
    }
    return $dolly;
}

function get_dolly_by_name($name)
{
    global $db, $tblPart2Kanban;
    $query = "SELECT * FROM {$tblPart2Kanban} WHERE '{$name}' LIKE CONCAT('%', kanban, '%') LIMIT 1";
    $result = $db->query($query);
    $dolly = mysqli_fetch_object($result);
    return $dolly;
}

function get_dolly_by_name_delivery($name, $delivery)
{
    global $db, $tblPart2Kanban;
    $query
        = "SELECT * FROM {$tblPart2Kanban} WHERE kanban = '{$name}' AND delivery_address = '{$delivery}' LIMIT 1";
    $result = $db->query($query);
    $dolly = mysqli_fetch_object($result);
    return $dolly;
}

function save_dolly($post_data)
{
    global $db, $tblDolly;
    $dolly_id = $post_data['dolly_id'];
    $dolly_name = $post_data['dolly_name'];
    $color = $post_data['color'];
    if ($dolly_id == 0) {
        $query = "INSERT INTO {$tblDolly}  (`name`, `color`)
                    value ('{$dolly_name}', '{$color}')";
        $result = $db->query($query);
        $insert_id = $db->insert_id;
        echo '<tr id="tr_dolly_' . $insert_id . '">';
        echo '<td><input type="text" class="form-control dolly-name" name="dolly_name" value="'
            . $dolly_name . '"></td>';
        echo '<td>';
        echo '<div class="input-group dolly-colorpicker colorpicker-element" id="dolly_color_'
            . $insert_id . '">
                    <input type="text" class="form-control" name="dolly_color" id="input_dolly_color_'
            . $insert_id . '" value="' . $color . '" data-original-title="" title="">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-square" style="color: '
            . $color . '"></i></span>
                    </div>
                </div>';
        echo '</td>';
        echo '<td style="text-align: center;"><button type="button" class="btn btn-danger delete-dolly" value="'
            . $insert_id . '">Delete</button></td>';
        echo '</tr>';
    } else {
        $query
            = "UPDATE {$tblDolly} SET `name` = '{$dolly_name}', `color` = '{$color}' WHERE `id` = {$dolly_id}";
        $result = $db->query($query);
        if ($result) {
            echo 'Ok';
        } else {
            echo 'Fail';
        }
    }
}

function update_dolly($post_data)
{
    global $db, $tblDolly;
    $dolly_id = $post_data['dolly_id'];
    $field = $post_data['field'];
    $value = $post_data['value'];
    $update_query
        = "UPDATE {$tblDolly} SET `{$field}` = '{$value}' WHERE id = {$dolly_id}";
    $result = $db->query($update_query);
    if ($result) {
        echo 'Success';
    } else {
        echo 'Failed';
    }
}

function delete_dolly($post_data)
{
    global $db, $tblDolly;
    $dolly_id = $post_data['dolly_id'];
    $query = "DELETE FROM {$tblDolly} WHERE `id` = {$dolly_id}";
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

/*
 * Reason Admin
 */
function get_all_reason()
{
    global $dbMssql, $tblReason;
    $query = "SELECT * FROM {$tblReason} ORDER BY id";
    $result = sqlsrv_query($dbMssql, $query);
    $reason = [];
    while ($item = sqlsrv_fetch_object($result)) {
        array_push($reason, $item);
    }
    return $reason;
}

function get_reason_by_name($name)
{
    global $db, $tblReason;
    $query = "SELECT * FROM {$tblReason} WHERE name = '{$name}' LIMIT 1";
    $result = $db->query($query);
    $reason = mysqli_fetch_object($result);
    return $reason;
}

function save_reason($post_data)
{
    global $db, $tblReason;
    $reason_id = $post_data['reason_id'];
    $reason_name = $post_data['reason_name'];
    if ($reason_id == 0) {
        $query = "INSERT INTO {$tblReason}  (`name`)
                    value ('{$reason_name}')";
        $result = $db->query($query);
        $insert_id = $db->insert_id;
        echo '<tr id="tr_reason_' . $insert_id . '">';
        echo '<td>' . $reason_name . '</td>';
        echo '<td style="text-align: center;"><button type="button" class="btn btn-danger delete-reason" value="'
            . $insert_id . '">Delete</button></td>';
        echo '</tr>';
    } else {
        $query
            = "UPDATE {$tblReason} SET `name` = '{$reason_name}' WHERE `id` = {$reason_id}";
        $result = $db->query($query);
        if ($result) {
            echo 'Ok';
        } else {
            echo 'Fail';
        }
    }
}

function update_reason($post_data)
{
    global $db, $tblReason;
    $reason_id = $post_data['reason_id'];
    $field = $post_data['field'];
    $value = $post_data['value'];
    $update_query
        = "UPDATE {$tblReason} SET `{$field}` = '{$value}' WHERE id = {$reason_id}";
    $result = $db->query($update_query);
    if ($result) {
        echo 'Success';
    } else {
        echo 'Failed';
    }
}

function delete_reason($post_data)
{
    global $db, $tblReason;
    $reason_id = $post_data['reason_id'];
    $query = "DELETE FROM {$tblReason} WHERE `id` = {$reason_id}";
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

/*
 * Part to Kanban
 */

function get_all_part2kanban()
{
    global $db, $tblPart2Kanban;
    $query = "SELECT * FROM {$tblPart2Kanban} ORDER BY `kanban`";
    $result = $db->query($query);
    $kanban = [];
    while ($item = mysqli_fetch_object($result)) {
        array_push($kanban, $item);
    }
    return $kanban;
}

function save_part2kanban($post_data)
{
    global $db, $tblPart2Kanban;
    $item_id = $post_data['item_id'];
    $kanban = $post_data['kanban'];
    $dolly = $post_data['dolly'];
    $barcode = $post_data['barcode'];
    $pick_address = $post_data['pick_address'];
    $delivery_address = $post_data['delivery_address'];
    $delivery_address2 = $post_data['delivery_address2'];
    $part_number = $post_data['part_number'];
    $pick_seq = $post_data['pick_seq'];
    $min = $post_data['min'];
    $max = $post_data['max'];
    if ($item_id == 0) {
        $query = "INSERT INTO {$tblPart2Kanban}  (`kanban`, `part_number`, `dolly`, `barcode`, `pick_address`, `delivery_address`, `delivery_address2`, `pick_seq`,`min`,`max`)
                    value ('{$kanban}', '{$part_number}', '{$dolly}', '{$barcode}', '{$pick_address}', '{$delivery_address}', '{$delivery_address2}', '{$pick_seq}', '{$min}', '{$max}')";
        $db->query($query);
        $insert_id = $db->insert_id;
        echo '<tr id="tr_p2k_' . $insert_id . '">';
        echo '<td><input type="text" class="form-control kanban" name="kanban" value="'
            . $kanban . '"></td>';
        echo '<td>';
        echo '<input type="text" class="form-control part_number" name="part_number" value="'
            . $part_number . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control dolly" name="dolly" value="'
            . $dolly . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control barcode" name="barcode" value="'
            . $barcode . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control pick_address" name="pick_address" value="'
            . $pick_address . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control delivery_address" name="delivery_address" value="'
            . $delivery_address . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control delivery_address2" name="delivery_address2" value="'
            . $delivery_address2 . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control pick_seq" name="pick_seq" value="'
            . $pick_seq . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control min" name="min" value="'
            . $min . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control max" name="max" value="'
            . $max . '">';
        echo '</td>';
        echo '<td style="text-align: center;">';
        echo '<button type="button" style="margin-bottom:5px" id="part2KanbanSave" class="btn btn-primary btn-sm save-part2kanban" value="'
            . $insert_id . '">Save</button>';
        echo '<button type="button" id="part2KanbanDelete" class="btn btn-danger btn-sm delete-part2kanban" value="'
            . $insert_id . '">Delete</button>';
        echo '</td>';
        echo '</tr>';
    } else {
        var_dump((int) $item_id);
        $query
            = "UPDATE {$tblPart2Kanban} SET `kanban` = '{$kanban}', `part_number` = '{$part_number}', `dolly` = '{$dolly}', `barcode` = '{$barcode}', `pick_address` = '{$pick_address}', `delivery_address` = '{$delivery_address}', `delivery_address2` = '{$delivery_address2}', `pick_seq` = '{$pick_seq}', `min` = '{$min}', `max` = {$max} WHERE `id` = '{$item_id}'";
        $result = $db->query($query);
        if ($result) {
            echo 'Ok';
        } else {
            echo 'Fail';
        }
    }
}

function delete_part2kanban($post_data)
{
    global $db, $tblPart2Kanban;
    $item_id = $post_data['item_id'];
    $query = "DELETE FROM {$tblPart2Kanban} WHERE `id` = {$item_id}";
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function clear_part2kanban($post_data)
{
    global $db, $tblPart2Kanban;
    $query = "TRUNCATE TABLE {$tblPart2Kanban}";
    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function read_part2kanban()
{
    $dolly = get_all_part2kanban();
    foreach ($dolly as $item) {
        echo '<tr id="tr_p2k_' . $item->id . '">';
        echo '<td><input type="text" class="form-control kanban" name="kanban" value="'
            . $item->kanban . '"></td>';
        echo '<td>';
        echo '<input type="text" class="form-control part_number" name="part_number" value="'
            . $item->part_number . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control dolly" name="dolly" value="'
            . $item->dolly . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control barcode" name="barcode" value="'
            . $item->barcode . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control pick_address" name="pick_address" value="'
            . $item->pick_address . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control delivery_address" name="delivery_address" value="'
            . $item->delivery_address . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control delivery_address2" name="delivery_address2" value="'
            . $item->delivery_address2 . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control pick_seq" name="pick_seq" value="'
            . $item->pick_seq . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control min" name="min" value="'
            . $item->min . '">';
        echo '</td>';
        echo '<td>';
        echo '<input type="text" class="form-control max" name="max" value="'
            . $item->max . '">';
        echo '</td>';
        echo '<td style="text-align: center;">';
        echo '<button type="button" style="margin-bottom:5px" id="part2KanbanSave" class="btn btn-primary btn-sm save-part2kanban" value="'
            . $item->id . '">Save</button>';
        echo '<button type="button" id="part2KanbanDelete" class="btn btn-danger btn-sm delete-part2kanban" value="'
            . $item->id . '">Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
}


function get_kanban_id_by_name($post_data)
{
    global $dbMssql, $tblConveyancePicks;
    $pick_date = convert_date_string($post_data['pick_date']);
    $kanban = $post_data['kanban'];
    $cycle = $post_data['cycle'];
    // $status = $post_data['status'];
    $query
        = "SELECT TOP 1 id FROM {$tblConveyancePicks} WHERE kanban_date = '{$pick_date}' AND kanban = '{$kanban}' AND cycle = {$cycle}";
    $result = sqlsrv_query($dbMssql, $query);
    $data = sqlsrv_fetch_object($result);
    echo json_encode(intval($data->id), true);
}

function get_system_fill_percentage($post_data)
{
    global $db, $tblStocking;
    $query
        = "SELECT sum(allocation*height) as total FROM {$tblStocking} WHERE area='System Fill'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['total'] = $res['total'];

    $query
        = "select sum(A.booked_in - A.booked_out) as filled from scan_log as A inner join parts as B on UPPER(A.part) = UPPER(B.part_no) AND B.sf = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['filled'] = $res['filled'];

    echo json_encode($data, true);
}

function get_part_stocking_percentage($post_data)
{
    global $db, $tblStocking;
    $query
        = "SELECT sum(allocation*height) as total FROM {$tblStocking} WHERE area='Part Stocking'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['total'] = $res['total'];

    $query
        = "select sum(A.booked_in - A.booked_out) as filled from scan_log as A inner join parts as B on UPPER(A.part) = UPPER(B.part_no) AND B.ps = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['filled'] = $res['filled'];

    echo json_encode($data, true);
}

function get_free_location_percentage($post_data)
{
    global $db, $tblStocking;
    $query
        = "SELECT sum(allocation*height) as total FROM {$tblStocking} WHERE area='Free Location'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['total'] = $res['total'];

    $query
        = "select sum(A.booked_in - A.booked_out) as filled from scan_log as A inner join parts as B on UPPER(A.part) = UPPER(B.part_no) AND B.fl = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['filled'] = $res['filled'];

    echo json_encode($data, true);
}

function get_all_sessions()
{
    $allSessions = [];
    $sessionNames = scandir(session_save_path());

    foreach ($sessionNames as $sessionName) {
        $sessionName = str_replace("sess_", "", $sessionName);
        if (
            !str_contains($sessionName, ".")
        ) { //This skips temp files that aren't sessions
            $ss_id = session_id();
            session_abort();
            session_id($sessionName);
            session_start();
            $allSessions[$sessionName] = $_SESSION;
            session_abort();

            session_id($ss_id);
            session_start();
        }
    }
    return $allSessions;
}

function close_session($post_data)
{
    $session_name = $post_data['session_name'];
    // session_id($session_name);
    $ss_id = session_id();
    session_abort();

    session_id($session_name);
    session_start();
    // session_abort();
    session_destroy();

    // session_id($ss_id);
    // session_start();

    echo 'Ok';
}

function get_all_session_json($post_data)
{
    global $db, $tblConveyancePicks, $tblUsers, $tblHelpAlarm;
    $today = $post_data['today'];
    $yesterday = $post_data['yesterday'];
    $query
        = "SELECT count(distinct cycle) as remaining_cycles FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_help=1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['remaining_cycles'] = $res['remaining_cycles'];

    // $query = "SELECT kanban, cycle, (SELECT max(cycle) from {$tblConveyancePicks}  WHERE is_help = 1 AND is_completed = 0  AND kanban_date = '{$today}') AS max_cycle from {$tblConveyancePicks} WHERE is_help = 1 AND is_completed = 0 AND kanban_date = '{$today}'";
    $query
        = "SELECT cp.id, cp.is_completed, cp.kanban AS kanban, cp.cycle AS cycle, cp.helped_at AS helped_at, u.username AS username FROM {$tblConveyancePicks} AS cp INNER JOIN {$tblUsers} AS u ON cp.helped_user = u.ID AND cp.kanban_date >= '{$yesterday}' AND cp.kanban_date <= '{$today}' AND cp.is_help = 1";

    // $query = "SELECT cp.id, cp.is_completed, cp.kanban AS kanban, cp.cycle AS cycle, cp.helped_at AS helped_at, u.username AS username FROM {$tblConveyancePicks} AS cp INNER JOIN {$tblUsers} AS u ON cp.helped_user = u.ID AND cp.kanban_date >= '{$yesterday}' AND cp.kanban_date <= '{$today}' AND cp.is_help = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // $res =  mysqli_fetch_array($result);
    $data['id'] = array_column($res, "id");
    $data['is_completed'] = array_column($res, "is_completed");
    $data['incomplete_kanban'] = array_column($res, "kanban");
    $data['incomplete_cycle'] = array_column($res, "cycle");
    $data['helped_time'] = array_column($res, "helped_at");
    $data['helped_user'] = array_column($res, "username");


    $query
        = "SELECT MAX(cycle) as max_cycle FROM {$tblConveyancePicks} WHERE kanban_date = DATE('{$today}') AND is_help=1";

    // $query = "SELECT MAX(cycle) as max_cycle FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_help=1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['max_cycle'] = $res['max_cycle'];

    $query
        = "SELECT COUNT(*) as incomplete_pick_count FROM {$tblConveyancePicks} WHERE kanban_date = DATE('{$today}') AND is_help=1 AND is_completed = 0 AND is_pick=1";
    // $query = "SELECT COUNT(*) as incomplete_pick_count FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_help=1 AND is_completed = 0 AND is_pick=1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_pick_count'] = $res['incomplete_pick_count'];

    $query
        = "SELECT COUNT(*) as incomplete_delivery_count FROM {$tblConveyancePicks} WHERE kanban_date = DATE('{$today}')  AND is_help=1 AND is_delivered = 0 AND is_pick=0";
    // $query = "SELECT COUNT(*) as incomplete_delivery_count FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}'  AND is_help=1 AND is_delivered = 0 AND is_pick=0";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_delivery_count'] = $res['incomplete_delivery_count'];

    $query
        = "SELECT COUNT(*) as incomplete_stocking_count FROM {$tblHelpAlarm} WHERE DATE(clicked_time) = DATE('{$today}')  AND is_confirm=0 AND page ='Stocking'";
    // $query = "SELECT COUNT(*) as incomplete_stocking_count FROM {$tblHelpAlarm} WHERE clicked_time >= '{$yesterday}' AND clicked_time <= '{$today}'  AND is_confirm=0 AND page ='Stocking'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_stocking_count'] = $res['incomplete_stocking_count'];

    $query
        = "SELECT COUNT(*) as incomplete_devan_count FROM {$tblHelpAlarm} WHERE DATE(clicked_time) = DATE('{$today}')  AND is_confirm=0 AND page ='Container Devan'";
    // $query = "SELECT COUNT(*) as incomplete_devan_count FROM {$tblHelpAlarm} WHERE clicked_time >= '{$yesterday}' AND clicked_time <= '{$today}'  AND is_confirm=0 AND page ='Container Devan'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_devan_count'] = $res['incomplete_devan_count'];

    $query
        = "SELECT COUNT(*) as incomplete_driver_count FROM {$tblHelpAlarm} WHERE DATE(clicked_time = '{$today}')  AND is_confirm=0 AND page ='Driver'";
    // $query = "SELECT COUNT(*) as incomplete_driver_count FROM {$tblHelpAlarm} WHERE clicked_time >= '{$yesterday}' AND clicked_time <= '{$today}'  AND is_confirm=0 AND page ='Driver'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_driver_count'] = $res['incomplete_driver_count'];

    $query
        = "SELECT COUNT(*) as complete_count FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}'  AND is_completed = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['complete_count'] = $res['complete_count'];
    $data['users'] = get_all_sessions();
    echo json_encode($data, true);
}


function get_pick_reason_chat_value($post_data)
{
    global $db, $tblConveyancePicks, $tblReason;
    $query
        = "SELECT count(*) as count, t1.completed_reason as reason, t2.`name` FROM {$tblConveyancePicks} as t1 INNER JOIN {$tblReason} as t2 ON t1.completed_reason=t2.id AND t1.completed_reason <> 0 GROUP BY t1.completed_reason;";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $data['val'] = array_column($res, "count");
    $data['reason'] = array_column($res, "name");

    echo json_encode($data, true);
}

function get_delivery_reason_chat_value($post_data)
{
    global $db, $tblConveyancePicks, $tblReason;
    $query
        = "SELECT count(*) as count, t1.delivered_reason as reason, t2.`name` FROM {$tblConveyancePicks} as t1 INNER JOIN {$tblReason} as t2 ON t1.delivered_reason=t2.id AND t1.delivered_reason <> 0 GROUP BY t1.delivered_reason;";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $data['val'] = array_column($res, "count");
    $data['reason'] = array_column($res, "name");

    echo json_encode($data, true);
}

function get_part_chat_value($post_data)
{
    global $db, $tblScanLog;
    $query = "SELECT sum(booked_in-booked_out) as total FROM {$tblScanLog}";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['total'] = $res['total'];

    $query
        = "select sum(booked_in-booked_out) as val, part FROM {$tblScanLog} GROUP BY part";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // $res =  mysqli_fetch_array($result);
    $data['val'] = array_column($res, "val");
    $data['part'] = array_column($res, "part");


    $data['users'] = get_all_sessions();
    echo json_encode($data, true);
}

function save_tag($post_data)
{
    global $dbMssql, $tblTag;
    $tag_value = $post_data['tag_value'];
    $query = "TRUNCATE TABLE {$tblTag}";
    sqlsrv_query($dbMssql, $query);
    $query = "INSERT INTO {$tblTag}  ([value]) values ('{$tag_value}')";

    $result = sqlsrv_query($dbMssql, $query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function save_cycle_setting($post_data)
{
    global $db, $tblCycleSetting;
    $cycle_value = $post_data['cycle_value'];

    $query = "TRUNCATE TABLE {$tblCycleSetting}";
    $db->query($query);
    $query = "INSERT INTO {$tblCycleSetting}  (`value`) VALUE ({$cycle_value})";

    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}


function save_opr_pick_delivery_setting($post_data)
{
    global $db, $tblOPRSetting;
    $opr_type = $post_data['opr_type'];
    $val = $post_data['val'];

    $query = "DELETE FROM {$tblOPRSetting} WHERE opr_type='{$opr_type}'";
    $db->query($query);
    $query
        = "INSERT INTO {$tblOPRSetting}  (`opr_type`, `value`) VALUES ('{$opr_type}', {$val})";

    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function save_driver_setting($post_data)
{
    global $db, $tblDriverSetting;
    $val = $post_data['val'];

    $query = "TRUNCATE TABLE {$tblDriverSetting}";
    $db->query($query);
    $query = "INSERT INTO {$tblDriverSetting}  (`value`) VALUES ({$val})";

    $result = $db->query($query);
    if ($result) {
        echo 'Ok';
    } else {
        echo 'Fail';
    }
}

function get_andons_info_teamleader($post_data)
{
    //$query = "UPDATE {$tblConveyancePicks} SET is_completed = 0, is_help = 1, helped_user = '{$user}', helped_at = '{$today}' WHERE id = {$kanban_id}";
    global $db, $tblConveyancePicks, $tblReason;
    $today = $post_data['today'];
    // $yesterday = $post_data['yesterday'];
    $query
        = "SELECT {$tblConveyancePicks}.kanban, {$tblConveyancePicks}.cycle, {$tblConveyancePicks}.helped_at, {$tblConveyancePicks}.deliveried_at, {$tblConveyancePicks}.picked_at,  {$tblReason}.name FROM {$tblConveyancePicks} LEFT JOIN {$tblReason} ON {$tblConveyancePicks}.completed_reason = {$tblReason}.id   WHERE ({$tblConveyancePicks}.kanban_date = '{$today}') AND  {$tblConveyancePicks}.is_help =1 AND {$tblConveyancePicks}.picked_at IS NOT NULL union SELECT {$tblConveyancePicks}.kanban, {$tblConveyancePicks}.cycle, {$tblConveyancePicks}.helped_at, {$tblConveyancePicks}.deliveried_at, {$tblConveyancePicks}.picked_at,  {$tblReason}.name FROM {$tblConveyancePicks} LEFT JOIN {$tblReason} ON {$tblConveyancePicks}.delivered_reason = {$tblReason}.id   WHERE ({$tblConveyancePicks}.kanban_date = '{$today}') AND  {$tblConveyancePicks}.is_help =1 AND {$tblConveyancePicks}.deliveried_at IS NOT NULL";
    // $query = "SELECT {$tblConveyancePicks}.kanban, {$tblConveyancePicks}.cycle, {$tblConveyancePicks}.helped_at, {$tblConveyancePicks}.deliveried_at, {$tblConveyancePicks}.picked_at,  {$tblReason}.name FROM {$tblConveyancePicks} LEFT JOIN {$tblReason} ON {$tblConveyancePicks}.completed_reason = {$tblReason}.id   WHERE ({$tblConveyancePicks}.helped_at between '{$yesterday}' AND  '{$today}') AND  {$tblConveyancePicks}.is_help =1 AND {$tblConveyancePicks}.picked_at IS NOT NULL union SELECT {$tblConveyancePicks}.kanban, {$tblConveyancePicks}.cycle, {$tblConveyancePicks}.helped_at, {$tblConveyancePicks}.deliveried_at, {$tblConveyancePicks}.picked_at,  {$tblReason}.name FROM {$tblConveyancePicks} LEFT JOIN {$tblReason} ON {$tblConveyancePicks}.delivered_reason = {$tblReason}.id   WHERE ({$tblConveyancePicks}.helped_at between '{$yesterday}' AND  '{$today}') AND  {$tblConveyancePicks}.is_help =1 AND {$tblConveyancePicks}.deliveried_at IS NOT NULL";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result);
    $query = "SELECT name from {$tblReason}";
    $result = $db->query($query);
    $res2 = mysqli_fetch_all($result);
    // $data['kanban'] = array_column($res, "kanban");
    // $data['cycle'] = array_column($res, "cycle");
    // $data['helped_at'] = array_column($res, "helped_at");
    // $data['picked_at'] = array_column($res, "picked_at");
    // $data['deliveried_at'] = array_column($res, "deliveried_at");
    $data['res'] = $res;
    $data['reasons'] = $res2;
    echo json_encode($data, true);
}


function get_remaining_incomplete_pick($post_data)
{
    global $db, $tblConveyancePicks, $tblUsers;
    $today = $post_data['today'];
    $yesterday = $post_data['yesterday'];
    $query
        = "SELECT count(distinct cycle) as remaining_cycles FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_completed =0";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['remaining_cycles'] = $res['remaining_cycles'];

    // $query = "SELECT kanban, cycle, (SELECT max(cycle) from {$tblConveyancePicks}  WHERE is_help = 1 AND is_completed = 0  AND kanban_date = '{$today}') AS max_cycle from {$tblConveyancePicks} WHERE is_help = 1 AND is_completed = 0 AND kanban_date = '{$today}'";
    $query
        = "SELECT cp.kanban AS kanban, cp.cycle AS cycle, cp.helped_at AS helped_at, u.username AS username FROM {$tblConveyancePicks} AS cp INNER JOIN {$tblUsers} AS u ON cp.helped_user = u.ID AND cp.kanban_date >= '{$yesterday}' AND cp.kanban_date <= '{$today}' AND cp.is_help = 1 AND cp.is_completed =0";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // $res =  mysqli_fetch_array($result);
    $data['incomplete_kanban'] = array_column($res, "kanban");
    $data['incomplete_cycle'] = array_column($res, "cycle");
    $data['helped_time'] = array_column($res, "helped_at");
    $data['helped_user'] = array_column($res, "username");

    $query
        = "SELECT MAX(cycle) as max_cycle FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}'";
    $result = $db->query($query);
    $res = mysqli_fetch_row($result);
    $data['max_cycle'] = $res['max_cycle'];

    echo json_encode($data, true);
}

function get_remaining_incomplete_pick_team_leader($post_data)
{
    global $db, $tblConveyancePicks, $tblUsers, $tblHelpAlarm;
    $today = $post_data['today'];
    $yesterday = $post_data['yesterday'];
    $query
        = "SELECT count(distinct cycle) as remaining_cycles FROM {$tblConveyancePicks} WHERE kanban_date = '{$today}' AND is_help=1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['remaining_cycles'] = $res['remaining_cycles'];

    // $query = "SELECT cp.id,cp.is_pick, cp.is_completed,cp.is_delivered, cp.kanban AS kanban, cp.cycle AS cycle, cp.helped_at AS helped_at, u.username AS username FROM {$tblConveyancePicks} AS cp INNER JOIN {$tblUsers} AS u ON cp.helped_user = u.ID AND cp.kanban_date >= '{$yesterday}' AND cp.kanban_date <= '{$today}' AND cp.is_help = 1";
    $query
        = "SELECT cp.id,cp.is_pick, cp.is_completed,cp.is_delivered, cp.kanban AS kanban, cp.cycle AS cycle, cp.helped_at AS helped_at, u.username AS username FROM {$tblConveyancePicks} AS cp INNER JOIN {$tblUsers} AS u ON cp.helped_user = u.ID AND cp.kanban_date = DATE('{$today}') AND cp.is_help = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // $res =  mysqli_fetch_array($result);
    $data['id'] = array_column($res, "id");
    $data['is_completed'] = array_column($res, "is_completed");
    $data['is_delivered'] = array_column($res, "is_delivered");
    $data['incomplete_kanban'] = array_column($res, "kanban");
    $data['is_pick'] = array_column($res, "is_pick");
    $data['incomplete_cycle'] = array_column($res, "cycle");
    $data['helped_time'] = array_column($res, "helped_at");
    $data['helped_user'] = array_column($res, "username");

    $query
        = "SELECT MAX(cycle) as max_cycle FROM {$tblConveyancePicks} WHERE kanban_date = DATE('{$today}') AND is_help=1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['max_cycle'] = $res['max_cycle'];

    $query
        = "SELECT COUNT(*) as incomplete_pick_count FROM {$tblConveyancePicks} WHERE kanban_date = DATE('{$today}') AND is_help=1 AND is_completed = 0 AND is_pick=1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_pick_count'] = $res['incomplete_pick_count'];

    $query
        = "SELECT COUNT(*) as incomplete_delivery_count FROM {$tblConveyancePicks} WHERE kanban_date = DATE('{$today}')  AND is_help=1 AND is_delivered = 0 AND is_pick=0";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_delivery_count'] = $res['incomplete_delivery_count'];

    $query
        = "SELECT COUNT(*) as incomplete_stocking_count FROM {$tblHelpAlarm} WHERE DATE(clicked_time) = DATE('{$today}')  AND is_confirm=0 AND page ='Stocking'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_stocking_count'] = $res['incomplete_stocking_count'];

    $query
        = "SELECT COUNT(*) as incomplete_devan_count FROM {$tblHelpAlarm} WHERE DATE(clicked_time) = DATE('{$today}')  AND is_confirm=0 AND page ='Container Devan'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_devan_count'] = $res['incomplete_devan_count'];

    $query
        = "SELECT COUNT(*) as incomplete_driver_count FROM {$tblHelpAlarm} WHERE DATE(clicked_time = '{$today}')  AND is_confirm=0 AND page ='Driver'";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['incomplete_driver_count'] = $res['incomplete_driver_count'];

    $query
        = "SELECT COUNT(*) as complete_count FROM {$tblConveyancePicks} WHERE DATE(kanban_date) = DATE('{$today}')  AND is_completed = 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['complete_count'] = $res['complete_count'];

    $data['reasons'] = get_all_reason();

    echo json_encode($data, true);
}

function get_sequence_control_addon($post_data)
{
    try {
        global $db, $tblConveyancePicks;

        $today = $post_data['today'];

        $query
            = "SELECT count(*) as count, cycle,sum(is_help) as is_help, sum(is_completed) as is_completed, sum(is_delivered) as is_delivered  FROM conveyance_picks WHERE kanban_date='{$today}' GROUP BY cycle ORDER BY cycle";
        $result = $db->query($query);
        // $res = mysqli_fetch_object($result);
        $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $data["sequence"] = $res;

        // $data['count'] = $res["count"];
        // $data['cycle'] = $res["cycle"];
        // $data['is_completed'] = $res["is_completed"];
        // $data['is_delivered'] = $res["is_delivered"];
        // $data['picking_count'] = $res["count"];
        // $data['picking_count'] = $res["count"];

        // $query = "SELECT count(*) as picking_count  FROM {$tblConveyancePicks} WHERE kanban_date = '{$today}' AND is_help=1";
        // $result = $db->query($query);
        // $res = mysqli_fetch_array($result);
        // $picking_count =  $res['picking_count'];

        // $query = "SELECT count(*) as delivered_count  FROM {$tblConveyancePicks} WHERE kanban_date = '{$today}' AND is_help=1 AND is_delivered=1";
        // $result = $db->query($query);
        // $res = mysqli_fetch_array($result);
        // $delivered_count =  $res['delivered_count'];

        // $query = "SELECT count(*) as picked_count  FROM {$tblConveyancePicks} WHERE kanban_date = '{$today}' AND is_help=1 AND is_completed=1";
        // $result = $db->query($query);
        // $res = mysqli_fetch_array($result);
        // $picked_count =  $res['picked_count'];

        $cycle_setting = get_cycle_setting();

        // $data['picking_count'] = $picking_count;
        // $data['delivered_count'] = $delivered_count;
        // $data['picked_count'] = $picked_count;
        $data['cycle_setting'] = $cycle_setting;
        $data['live_build'] = get_build_amount();
        echo json_encode($data, true);
        // echo json_encode($data, true);
    } catch (Exception $e) {
        echo "error";
    }
}

function get_remaining_build_delivery($post_data)
{
    global $db, $tblTag, $tblLive, $tblCycleSetting, $tblConveyancePicks, $tblDriverSetting;
    $today = $post_data['today'];
    $yesterday = $post_data['yesterday'];

    $query
        = "SELECT count(distinct cycle) as remaining_cycles FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_delivered =0";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['remaining_cycles'] = $res['remaining_cycles'];

    $query
        = "SELECT count(distinct cycle) as remaining_cycles_pick FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_completed =0";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['remaining_cycles_pick'] = $res['remaining_cycles_pick'];

    $query
        = "SELECT count(distinct cycle) as remaining_cycles_delivery FROM {$tblConveyancePicks} WHERE kanban_date >= '{$yesterday}' AND kanban_date <= '{$today}' AND is_delivered =0";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['remaining_cycles_delivery'] = $res['remaining_cycles_delivery'];

    $query = "SELECT value FROM {$tblTag}";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $tag = $res['value'];

    $query = "SELECT value FROM {$tblCycleSetting}";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $cycle = $res['value'];
    $data['cycle'] = (int) $cycle;

    $query = "SELECT value FROM {$tblDriverSetting}";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $cycle = $res['value'];
    $data['driver'] = (int) $cycle;

    $query
        = "SELECT value FROM {$tblLive} WHERE `name` = '{$tag}' ORDER BY id DESC LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['count'] = (int) $res['value'];

    if (isset($_SESSION['driver_current_zone'])) {
        $data['driver_current_zone'] = $_SESSION['driver_current_zone'];
    } else {
        $data['driver_current_zone'] = '1';
    }

    if (isset($_SESSION['base_target'])) {
        $data['base_target'] = $_SESSION['base_target'];
    } else {
        $data['base_target'] = '';
    }
    // $data['cycle'] = (int)$res['count'] / (int)$cycle;
    // $data['cycle'] = 'CYCLE' + $data['cycle'];

    echo json_encode($data, true);
}


function get_last_upload_time_team_leader($post_data)
{
    global $db, $tblContainerDevan, $tblConveyancePicks, $tblPart2Kanban;
    $query
        = "SELECT CONCAT(`date`, ' ', `time`) AS `time` FROM {$tblContainerDevan} ORDER BY `date` DESC, `time` DESC LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['devan_schedule'] = $res['time'];

    $query
        = "SELECT imported_at FROM {$tblConveyancePicks} ORDER BY imported_at DESC LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['pick_del_plan'] = $res['imported_at'];

    $query
        = "SELECT imported_at FROM {$tblConveyancePicks} ORDER BY imported_at DESC LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['master_kanban'] = $res['imported_at'];

    echo json_encode($data, true);
}

function get_opr_settings($post_data)
{
    global $db, $tblCycleSetting, $tblOPRSetting, $tblDriverSetting;
    $data['renban_no_prefix'] = get_setting('renban_no_prefix');

    $query = "SELECT `value` FROM {$tblCycleSetting} LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['opr_cycle_settings'] = $res['value'];

    $query
        = "SELECT `value` FROM {$tblOPRSetting} WHERE opr_type = 'pick' LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['opr_pick_settings'] = $res['value'];

    $query
        = "SELECT `value` FROM {$tblOPRSetting} WHERE opr_type = 'delivery' LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['opr_del_settings'] = $res['value'];

    $query = "SELECT `value` FROM {$tblDriverSetting} LIMIT 1";
    $result = $db->query($query);
    $res = mysqli_fetch_array($result);
    $data['driver_settings'] = $res['value'];

    echo json_encode($data, true);
}

function set_driver_member($post_data)
{
    $_SESSION['driver_current_zone'] = $post_data['current_zone'];
    $_SESSION['driver_zone_time'] = $post_data['now'];
    $_SESSION['driver_zone_background'] = $post_data['zone_background'];
    $_SESSION['driver_zone_red_status'] = $post_data['zone_red_status'];
    $_SESSION['driver_zone_paused_time'] = $post_data['paused_time'];
    $_SESSION['is_initial'] = $post_data['is_initial'];
    $_SESSION['truck_step_counter'] = $post_data['truck_step_counter'];
    $_SESSION['base_target'] = $post_data['base_target'];

    $data['result'] = 'ok';
    echo json_encode($data, true);
}

function get_low_stocks($post_data)
{
    global $db, $tblScanLog, $tblParts;
    $query
        = "SELECT upper(t1.part_no) as part, SUM(t2.booked_in-t2.booked_out) AS count, t1.level_low, t1.level_medium FROM {$tblParts} AS t1 LEFT JOIN {$tblScanLog} AS t2 ON t1.part_no = t2.part GROUP BY t1.part_no";
    //$query = "SELECT upper(sl.part) as part, pt.amount, sum(sl.booked_in-sl.booked_out) as count, pt.level_low, pt.level_medium FROM {$tblScanLog} as sl INNER JOIN {$tblParts} pt ON upper(sl.part)=upper(pt.part_no) GROUP BY sl.part";
    $result = $db->query($query);
    $res_zones = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $data['low_stocks'] = $res_zones;

    echo json_encode($data, true);
}

function delete_periodData($post_data)
{
    global $dbMssql, $tblConveyancePicks;
    $periodStart = $post_data['periodStart'];
    $periodEnd = $post_data['periodEnd'];
    $query
        = "DELETE FROM {$tblConveyancePicks} WHERE kanban_date BETWEEN '{$periodStart}' AND '{$periodEnd}'";
    $result = sqlsrv_query($dbMssql, $query);
    echo 'Ok';
}

function delete_EveryData()
{
    global $dbMssql, $tblConveyancePicks;
    $query = "DELETE FROM {$tblConveyancePicks}";
    $result = sqlsrv_query($dbMssql, $query);
    $data = [];
    echo 'OK';
}

// 2023-7-20
function get_shifts()
{
    global $db;
    // Execute SQL query to fetch data from the table
    $sql = "SELECT * FROM excel_pick_import";
    $result = $db->query($sql);
    // Check if any rows were returned
    $replyData = '';
    if ($result->num_rows > 0) {
        // Display the data in an HTML table
        $replyData . "<table id='myTable'>";
        $replyData
            . "<tr><th>Cont.</th><th>Mod.</th><th>Boxes</th><th>Counter</th><th>LIVE BUILD</th><th>Load confirm</th><th>S/fill START</th><th>S/fill FINISH</th></tr>";
        while ($row = $result->fetch_assoc()) {
            if ($row["id"] % 2 === 0) {
                $replyData . "<tr><td>" . $row["Container"] . "</td><td>"
                    . $row["Module"] . "</td><td>" . $row["Qty_Boxes"]
                    . "</td><td rowspan='2'>" . $row["Qty_Boxes"]
                    . "</td><td rowspan='2'>" . $row["Qty_Boxes"]
                    . "</td><td rowspan='2'>" . "</td><td rowspan='2'>"
                    . "</td><td rowspan='2'>" . "</td></tr>";
            } else {
                $replyData . "<tr><td>" . $row["Container"] . "</td><td>"
                    . $row["Module"] . "</td><td>" . $row["Qty_Boxes"] . "</tr>";
            }
        }
        $replyData . "</table>";
    } else {
        // Display a message if no rows were returned
        $replyData . "No data found";
    }
    echo $replyData;
}

function get_users()
{
    global $dbMssql, $tblUsers;
    $query = "SELECT * FROM {$tblUsers}";
    $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
    if ($result && sqlsrv_num_rows($result) > 0) {
        while ($row = sqlsrv_fetch_object($result)) {
            $userNames[] = $row->username;
        }
        $jsonData = json_encode($userNames);
    } else {
        $jsonData = json_encode(['data' => "No data found"]);
    }
    echo $jsonData;
}

function delete_file($post_data)
{
    $fileName = $post_data['file'];
    unlink($fileName);
}

function read_excel($post_data)
{
    global $db, $dbMssql, $tblExcelPick;

    $date = $post_data['date'];

    $dayData = '';
    $nightData = '';
    
        $dayQuery = "SELECT * FROM {$tblExcelPick} 
            WHERE (SUBSTRING(Module, 1, 2) = 'HB' 
            OR SUBSTRING(Module, 1, 2) = 'HF') 
            AND shift = 1 
            AND Stocking_Date = '{$date}';";
        $nightQuery = "SELECT * FROM {$tblExcelPick} 
            WHERE (SUBSTRING(Module, 1, 2) = 'HB' 
            OR SUBSTRING(Module, 1, 2) = 'HF') 
            AND shift = 2 
            AND Stocking_Date = '{$date}';";
        $params = [];
        $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
        $dayResult = sqlsrv_query($dbMssql, $dayQuery, $params, $options);
        $nightResult = sqlsrv_query($dbMssql, $nightQuery, $params, $options);

        if ($dayResult && sqlsrv_num_rows($dayResult) > 0) {
            // Display the data in an HTML table
            $dayData = "<table id='dayShiftTB' style='width:100%'>";
            $dayData = $dayData
                . "<tr><th>Cont.</th><th>Mod.</th><th>Counter</th><th>LIVE BUILD</th><th>Load confirm</th><th>S/fill START</th><th>S/fill FINISH</th></tr>";
            $my_array = [];
            $result_array = [];
            $j = 0;
            $tmpArr = [];

            while ($rowNum = sqlsrv_fetch_array($dayResult, SQLSRV_FETCH_ASSOC)) {
                array_push($tmpArr, $rowNum);
            }

            $dayData = $dayData . "<script>var dayRowNum = " . count($tmpArr)
                . "</script>"; //get row number

            for ($i = 0; $i <= round(count($tmpArr) / 2); $i++) {
                if ($i < count($tmpArr) / 2) {
                    $dayData = $dayData . "<script> contArr_day.push('" . $tmpArr[$i
                        * 2]['Container'] . "') </script>";
                    $dayData = $dayData . "<script> modArr_day.push('" . $tmpArr[$i
                        * 2]['Module'] . "') </script>";
                    $dayData = $dayData . "<tr id='dayRow_" . $tmpArr[$i * 2]['id']
                        . "' greenFlag='0' dayrowid = '" . ($i * 2) . "'>
                        <td id='dayConID_" . $tmpArr[$i * 2]['id'] . "'>" . $tmpArr[$i
                            * 2]["Container"] . "</td>
                        <td id='dayModID_" . $tmpArr[$i * 2]['id'] . "'>" . $tmpArr[$i
                            * 2]["Module"] . "</td>
                        <td id='day_counter_" . $tmpArr[$i * 2]['id']
                        . "' style='padding:unset;'></td><td rowspan='2' id='dayLive_"
                        . $tmpArr[$i]['id'] . "' opt='dayLive' class='td-day-live'></td>
                        <td rowspan='2'  id='dayLoad_" . $tmpArr[$i]['id'] . "' style='padding:2px;'>
                            <div id='dayLCUser_" . ($i + 1) . "' name='dayLCUser'></div>
                            <div id='dayLCTime_" . ($i + 1) . "' name='dayLCTime'></div>    
                            <select disabled name='Day Load Confirm' id='dayLCSel_"
                        . ($i + 1) . "' onchange='selectDayLC(" . ($i + 1) . ")' class='form-control'>
                                <option disabled selected value=''>Select User</option>
                            </select>
                        </td>
                        <td rowspan='2' name='dayStWork'>
                            <div id='dayStUser_" . ($i + 1) . "' id='startUser'></div>
                            <div id='dayStTime_" . ($i + 1) . "'  id='startTime'></div>
                            <select disabled name='Fill Start' id='dayStSel_" . ($i
                        + 1) . "' onchange='selectDayStName(" . ($i + 1) . ")' class='form-control select-fill-start'>
                                <option disabled selected value=''>Select User</option>
                            </select>
                        </td>
                        <td rowspan='2' name='dayFnWork'>
                            <div id='dayFnUser_" . ($i + 1) . "' name='dayFnUser'></div>
                            <div id='dayFnTime_" . ($i + 1) . "' name='dayFnTime'></div>
                            <select disabled name='Day Finish' id='dayFnSel_" . ($i
                        + 1) . "' onchange='selectDayFnName(" . ($i + 1) . ")' class='form-control'>
                                <option disabled selected value=''>Select User</option>
                            </select>
                        </td>
                        </tr>";
                }

                // TODO: fix this when odd number
                if ($i + 1 <= round(count($tmpArr) / 2)) {
                    if (isset($tmpArr[$i * 2 + 1])) {
                        $dayData = $dayData . "<script> contArr_day.push('" . $tmpArr[$i
                            * 2 + 1]['Container'] . "') </script>";
                        $dayData = $dayData . "<script> modArr_day.push('" . $tmpArr[$i
                            * 2 + 1]['Module'] . "') </script>";
                        $dayData = $dayData . "<tr id='dayRow_" . $tmpArr[$i * 2
                            + 1]['id'] . "' greenFlag='0'  dayrowid = '" . ($i * 2 + 1) . "'>
                            <td id='dayConID_" . $tmpArr[$i * 2 + 1]['id'] . "'>"
                            . $tmpArr[$i * 2 + 1]["Container"] . "</td>
                            <td id='dayModID_" . $tmpArr[$i * 2 + 1]['id'] . "'>"
                            . $tmpArr[$i * 2 + 1]["Module"] . "</td>
                            <td id='day_counter_" . $tmpArr[$i * 2 + 1]['id']
                            . "' style='padding:unset;'></td></tr>";
                    }
                }
            }

            $dayData = $dayData . "</table>";
        }

        // Night Table Data
        if ($nightResult && sqlsrv_num_rows($nightResult) > 0) {
            // Display the data in an HTML table
            $nightData = "<table id='nightShiftTB' style='width:100%'>";
            $nightData = $nightData
                . "<tr><th>Cont.</th><th>Mod.</th><th>Counter</th><th>LIVE BUILD</th><th>Load confirm</th><th>S/fill START</th><th>S/fill FINISH</th></tr>";
            $my_array = [];
            $result_array = [];
            $j = 0;
            $tmpArr = [];

            while ($rowNum = sqlsrv_fetch_array($nightResult, SQLSRV_FETCH_ASSOC)) {
                array_push($tmpArr, $rowNum);
            }

            $nightData = $nightData . "<script>var nightRowNum = " . count($tmpArr)
                . "</script>"; //get row number
            for ($i = 0; $i <= floor(count($tmpArr) / 2); $i++) {
                if ($i < count($tmpArr) / 2) {
                    $nightData = $nightData . "<script> contArr_night.push('"
                        . $tmpArr[$i * 2]['Container'] . "') </script>";
                    $nightData = $nightData . "<script> modArr_night.push('"
                        . $tmpArr[$i * 2]['Module'] . "') </script>";
                    $nightData = $nightData . "<tr id='nightRow_" . $tmpArr[$i
                        * 2]['id'] . "' greenFlag='0'  nightrowid = '" . ($i * 2) . "'>
                        <td id='nightConID_" . $tmpArr[$i * 2]['id'] . "'>" . $tmpArr[$i
                            * 2]["Container"] . "</td>
                        <td id='nightModID_" . $tmpArr[$i * 2]['id'] . "'>" . $tmpArr[$i
                            * 2]["Module"] . "</td>
                        <td id='night_counter_" . $tmpArr[$i * 2]['id']
                        . "' style='padding:unset;'></td><td rowspan='2' id='nightLive_"
                        . $tmpArr[$i]['id'] . "' opt='nightLive' class='td-night-live'></td>
                        <td rowspan='2' id='nightLoad_" . $tmpArr[$i]['id'] . "' style='padding:2px;'>
                            <div id='nightLCUser_" . ($i + 1) . "' name='nightLCUser'></div>
                            <div id='nightLCTime_" . ($i + 1) . "' name='nightLCTime'></div>    
                            <select disabled name='Night Load Confirm' id='nightLCSel_"
                        . ($i + 1) . "' onchange='selectNightLC(" . ($i + 1) . ")' class='form-control'>
                                <option disabled selected value=''>Select User</option>
                            </select>
                        </td>
                        <td rowspan='2' name='nightStWork'>
                            <div id='nightStUser_" . ($i + 1) . "' name='nightStUser'></div>
                            <div id='nightStTime_" . ($i + 1) . "' name='nightStTime'></div>
                            <select disabled name='Fill Start' id='nightStSel_" . ($i
                        + 1) . "' onchange='selectNightStName(" . ($i + 1) . ")' class='form-control select-fill-start'>
                                <option disabled selected value=''>Select User</option>
                            </select>
                        </td>
                        <td rowspan='2' name='nightFnWork'>
                            <div id='nightFnUser_" . ($i + 1) . "' name='nightFnUser'></div>
                            <div id='nightFnTime_" . ($i + 1) . "' name='nightFnTime'></div>
                            <select disabled name='Night Finish' id='nightFnSel_"
                        . ($i + 1) . "' onchange='selectNightFnName(" . ($i + 1) . ")' class='form-control'>
                                <option disabled selected value=''>Select User</option>
                            </select>
                        </td>
                        </tr>";
                }

                // TODO: fix this when odd number
                if ($i + 1 <= count($tmpArr) / 2) {
                    if (isset($tmpArr[$i * 2 + 1])) {
                        $nightData = $nightData . "<script> contArr_night.push('"
                            . $tmpArr[$i * 2 + 1]['Container'] . "') </script>";
                        $nightData = $nightData . "<script> modArr_night.push('"
                            . $tmpArr[$i * 2 + 1]['Module'] . "') </script>";
                        $nightData = $nightData . "<tr id='nightRow_" . $tmpArr[$i * 2
                            + 1]['id'] . "' greenFlag='0' nightrowid='" . ($i * 2 + 1) . "'>
                            <td id='nightConID_" . $tmpArr[$i * 2 + 1]['id'] . "'>"
                            . $tmpArr[$i * 2 + 1]["Container"] . "</td>
                            <td id='nightModID_" . $tmpArr[$i * 2 + 1]['id'] . "'>"
                            . $tmpArr[$i * 2 + 1]["Module"] . "</td>
                            <td id='night_counter_" . $tmpArr[$i * 2 + 1]['id']
                            . "' style='padding:unset;'></td></tr>";
                    }
                }
            }

            $nightData = $nightData . "</table>";
        }

        // if ($dayData == "" && $nightData == "") {
        //     $new_date = date_create($date);
        //     date_add($new_date, date_interval_create_from_date_string("1 days"));
        //     $new_date = date_format($new_date, "Y-m-d");
        //     $date = $new_date;
        // }
    

    $data = [
        "stocking_date" => $date,
        "day" => $dayData,
        "night" => $nightData
    ];
    echo json_encode($data);
}

function get_active_row($post_data)
{
    global $dbMssql, $tblActiveRow;
    $params = [];
    $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
    $date = $post_data['date'];
    $query = "SELECT * FROM {$tblActiveRow} WHERE date='" . $date . "'";
    $result = sqlsrv_query($dbMssql, $query, $params, $options);

    $resData = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    echo json_encode($resData);
}

function get_built_amount($post_data)
{
    global $dbMssql, $tblBuildAmount;
    $params = [];
    $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
    $date = $post_data['date'];
    $query = "SELECT * FROM {$tblBuildAmount} WHERE date='" . $date . "'";
    $result = sqlsrv_query($dbMssql, $query, $params, $options);
    $resData = [];

    while ($rowNum = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        array_push($resData, $rowNum);
    }
    echo json_encode($resData);
}

function reset_fill_system()
{
    global $dbMssql, $tblExcelPick;
    $query = "DELETE FROM {$tblExcelPick}";
    $result = sqlsrv_query($dbMssql, $query);

    echo 'success';
}

function search_kanban($post_data)
{
    global $tblConveyancePicks, $dbMssql, $current;
    $kanban = $post_data['kanban'];
    $pick_date = convert_date_string($post_data['pick_date']);
    $cycle = $post_data['cycle'];
    //$user = $post_data['user'] ?? $_SESSION['user']['user_id'];
    if ($kanban) {
        $query
            = "SELECT * FROM {$tblConveyancePicks} WHERE kanban = '{$kanban}' AND cycle = {$cycle} AND kanban_date = '{$pick_date}' AND is_completed = 0";
        $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);

        $num_rows = sqlsrv_num_rows($result);
        if ($num_rows > 0) {
            $row = sqlsrv_fetch_object($result);
            echo json_encode($row);
        } else {
            echo "failure";
        }
    } else {
        echo "failure";
    }
}

function check_zone_finish($post_data)
{
    global $tblConveyancePicks, $dbMssql, $current;
    $kanban_id = $post_data['kanban_id'];
    $cycle = $post_data['cycle'];
    
    if (isset($kanban_id)) {
        $query
            = "SELECT * FROM {$tblConveyancePicks} WHERE id = '{$kanban_id}'";
        $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);

        $num_rows = sqlsrv_num_rows($result);
        if ($num_rows > 0) {
            $row = sqlsrv_fetch_object($result);
            $zone = $row->dolly;
            $date = $row->kanban_date->format('Y-m-d');
            $query
                = "SELECT COUNT(*) AS zone_count FROM {$tblConveyancePicks} WHERE dolly = '$zone' AND kanban_date = '{$date}' AND is_completed = 0 AND cycle = {$cycle}";
            $result = sqlsrv_query($dbMssql, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);

            $num_rows = sqlsrv_num_rows($result);
            if ($num_rows > 0) {
                $row = sqlsrv_fetch_object($result);
                echo json_encode($row);
            } else {
                echo "failure";
            }
        } else {
            echo "failure";
        }
    } else {
        echo "failure";
    }
}

function complete_list($post_data)
{
    global $tblCompletedDate, $db;
    $date = $post_data['date'];
    if (isset($post_data['method'])) {
        if ($post_data['method'] == "add") {
            $query = "SELECT * FROM {$tblCompletedDate} WHERE date = '{$date}'";
            $result = $db->query($query);
            $num_rows = mysqli_num_rows($result);
            if ($num_rows == 0) {
                $insert_query = "INSERT INTO {$tblCompletedDate} (date) VALUES ('{$date}')";
                $db->query($insert_query);
            }
            $new_date = date_create($date);
            date_add($new_date, date_interval_create_from_date_string("1 days"));
            $new_date = date_format($new_date, "Y-m-d");
            echo $new_date;
        } else {
            $query = "DELETE FROM {$tblCompletedDate} WHERE `date` = '{$date}'";
            $result = $db->query($query);
            echo $date;
        }
    } else {
        $query = "SELECT MAX(date) FROM {$tblCompletedDate}";
        $result = $db->query($query);
        if ($result->fetch_assoc()["MAX(date)"] != null) {
            $new_date = date_create($result->fetch_assoc()["MAX(date)"] ?? 'now');
            date_add($new_date, date_interval_create_from_date_string("1 days"));
            $new_date = date_format($new_date, "Y-m-d");
            return $new_date;
        } else {
            return $date;
        }
    }
}