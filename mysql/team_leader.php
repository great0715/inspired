<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Team Leader";
$_SESSION['page'] = 'team_leader.php';
// login_check();
require_once("assets.php");
$shift_inf = get_current_shift();
$booked_in_out = get_booked_in_out('Stocking', $shift_inf['shift'], $shift_inf['date']);
?>
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
<style>
    .bootstrap-datetimepicker-widget table thead th {
        background: #fff;
    }

    .bootstrap-datetimepicker-widget tbody tr:last-child th,
    .bootstrap-datetimepicker-widget tbody tr:last-child>td {
        border-bottom: 0;
    }

    .content {
        padding-top: 3% !important;
    }
</style>

<body class="hold-transition sidebar-collapse layout-top-nav" onload="startTime()">
    <div class="wrapper">
        <?php include("header.php"); ?>
        <?php include("menu.php"); ?>
        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row px-0 mx-auto my-2">
                                <div class="border p-2 m-auto" style="justify-content: space-around; border-radius: 15px;">
                                    <div class="text-center">
                                        <p class="h4 font-weight-bold">DEVAN SCHEDULE</p>
                                        <p class="h6 font-weight-bold">Last Uploaded</p>
                                        <p class="h6 font-weight-bold" id="upload_time_devan_schedule"></p>
                                        <label class="btn btn-primary" style="margin-top: 7px; font-weight: normal;">
                                            Import Data
                                            <input type="file" hidden="" id="file_devan_schedule" name="file_devan_schedule">
                                        </label>
                                    </div>
                                </div>
                                <div class="border p-2 m-auto" style="justify-content: space-around; border-radius: 15px;">
                                    <div class="text-center">
                                        <p class="h4 font-weight-bold">PICK / DEL PLAN</p>
                                        <p class="h6 font-weight-bold">Last Uploaded</p>
                                        <p class="h6 font-weight-bold" id="upload_time_pick_del_plan"></p>
                                        <label class="btn btn-primary" style="margin-top: 7px; font-weight: normal;">
                                            Import Data
                                            <input type="file" hidden="" id="file_pick_del_plan" name="file_pick_del_plan">
                                        </label>
                                    </div>
                                </div>
                                <div class="border p-2 m-auto" style="justify-content: space-around; border-radius: 15px;">
                                    <div class="text-center">
                                        <p class="h4 font-weight-bold">MASTER KANBAN</p>
                                        <p class="h6 font-weight-bold">Last Uploaded</p>
                                        <p class="h6 font-weight-bold" id="upload_time_master_kanban"></p>
                                        <label class="btn btn-primary" style="margin-top: 7px; font-weight: normal;">
                                            Import Data
                                            <input type="file" hidden="" id="file_master_kanban" name="file_master_kanban">
                                        </label>
                                    </div>
                                </div>

                                <div class="border p-2 m-auto text-center" style="justify-content: space-around; border-radius: 15px;">
                                    <p class="h6 font-weight-bold mt-1">BUILD / CYCLE</p>
                                    <p style="color:green; font-size: 7vh; font-weight: bold; margin-top: -10px; margin-bottom: -10px;" id="build_cycle_count"></p>
                                    <p style="color:#2c577a; font-size: 24px; font-weight: bold;" id="build_cycle_cycle"></p>
                                </div>
                            </div>

                            <div class="mx-auto my-2">
                                <div class="text-center">
                                    <h3 class="font-weight-bold">ANDON / INCOMPLETE KANBANS</h3>
                                </div>
                                <div class="row my-2 mx-auto relative" id="status_history_bar">
                                </div>
                                <div class="row mt-2 mb-8 mx-auto" id='time-bar'>

                                </div>

                                <div class="border px-0 mx-auto text-center my-2" id="andon_incomplete_kanbans" style="border-radius: 15px;">
                                    <p class="h6 text-left m-2 h-12"></p>
                                    <div class="row" style="justify-content:space-around;" id="#">
                                        <div id="incomplete_pick">
                                        </div>
                                        <div id="incomplete_delivery">
                                        </div>
                                        <div id="incomplete_stocking">
                                        </div>
                                        <div id="incomplete_devan">
                                        </div>
                                        <div id="incomplete_driver">
                                        </div>
                                    </div>
                                    <div class="row my-3" id="incomplete_kanbans">
                                    </div>

                                </div>
                            </div>

                            <div class="row mx-auto my-2">
                                <button type="button" class="btn btn-primary rounded mx-auto mt-3" style="width: 10vw" id="btn_seq_control_andon">
                                    <p class="h6">SEQUENCE CONTROL ANDON</p>
                                </button>
                                <button type="button" class="btn btn-primary rounded mx-auto mt-3" style="width: 10vw" id="btn_pick_stock_devan_overview">
                                    <p class="h6">PICK, STOCK DEVAN OVERVIEW</p>
                                </button>
                                <button type="button" class="btn btn-primary rounded mx-auto mt-3" style="width: 10vw" id="btn_del_driver_overview">
                                    <p class="h6">DELIVERY & DRIVER OVERVIEW</p>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <div class="row border p-2 my-2 mx-auto" style="justify-content: space-around; border-radius: 15px;">
                                    <div class="bg-primary text-center p-2 m-auto" style="align-self: center;">
                                        <p class="h6 font-weight-bold">RENBAN</p>
                                        <p class="h6 font-weight-bold">NO PROFIX:</p>
                                        <input type="text" class="form-control" id="renban_no_prefix" name="renban_no_prefix" style="width:100px" value="<?php echo get_setting('renban_no_prefix') ?>">
                                    </div>
                                    <div class="bg-primary text-center p-2 m-auto" style="align-self: center;">
                                        <p class="h6 font-weight-bold">CYCLE</p>
                                        <p class="h6 font-weight-bold">SETTINGS</p>
                                        <input type="text" class="form-control" id="opr_cycle_settings" name="opr_cycle_settings" style="width:100px">
                                    </div>
                                    <div class="bg-primary text-center p-2 m-auto" style="align-self: center;">
                                        <p class="h6 font-weight-bold">OPR PICK</p>
                                        <p class="h6 font-weight-bold">SETTINGS</p>
                                        <input type="text" class="form-control" id="opr_pick_settings" name="opr_pick_settings" style="width:100px">
                                    </div>
                                    <div class="bg-primary text-center p-2 m-auto" style="align-self: center;">
                                        <p class="h6 font-weight-bold">OPR DEL</p>
                                        <p class="h6 font-weight-bold">SETTINGS</p>
                                        <input type="text" class="form-control" id="opr_del_settings" name="opr_del_settings" style="width:100px">
                                    </div>
                                </div>
                                <div class="border text-center mx-auto my-2" style="border-radius: 15px;">
                                    <div class="text-center my-auto py-2">
                                        <h6 class="font-weight-bold">CYCLES FROM LAST SHIFT</h6>
                                    </div>
                                    <div class="row text-center m-2 py-2">
                                        <div class="text-center mx-auto">
                                            <p class="h6 font-weight-bold">PICK</p>
                                            <button type="button" class="btn rounded-circle mx-5 mt-3" style="width:6.5vw; height: 6.5vw;" id="remaining_cycles_pick">
                                                <p class="h1" style="font-size: 5vw;">0</p>
                                            </button>
                                        </div>
                                        <div class="text-center mx-auto">
                                            <p class="h6 font-weight-bold">DELIVERY</p>
                                            <button type="button" class="btn bg-success rounded-circle mx-5 mt-3" style="width:6.5vw; height: 6.5vw;" id="remaining_cycles_delivery">
                                                <p class="h1" style="font-size: 5vw;">0</p>
                                            </button>
                                        </div>
                                        <div class="text-center mx-auto">
                                            <p class="h6 font-weight-bold">DRIVER</p>
                                            <button type="button" class="btn bg-success rounded-circle mx-5 mt-3" style="width:6.5vw; height: 6.5vw;" id="remaining_cycles_driver">
                                                <p class="h1" style="font-size: 5vw;">0</p>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="border text-center mx-auto my-2" style="border-radius: 15px;">
                                    <div class="text-center my-auto py-2">
                                        <h5 class="font-weight-bold">LOW STOCK</h5>
                                    </div>
                                    <div class="row text-center  m-2 py-2" id="low_stocks">
                                    </div>
                                </div>

                                <div class="border text-center mx-auto my-2" style="border-radius: 15px;">
                                    <div class="text-center my-auto py-2">
                                        <h5 class="font-weight-bold">ACTIVE MEMBERS</h5>
                                    </div>
                                    <div class="row text-center justify-content-center m-2 py-2" id="active_members">
                                        <div class="text-center p-1 mx-auto" id="pick_members">
                                        </div>
                                        <div class="text-center p-1 mx-auto" id="del_members">
                                        </div>
                                        <div class="text-center p-1 mx-auto" id="stocking_members">
                                        </div>
                                        <div class="text-center p-1 mx-auto" id="devan_members">
                                        </div>
                                        <div class="text-center p-1 mx-auto" id="driver_members">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <?php include("footer.php"); ?>
    </div>
    <input type="hidden" id="session_user_name" value="<?php echo $_SESSION['user']['username']; ?>">

    <div class="modal fade" id="seq_control_andon_modal">
        <div class="modal-dialog" style="max-width: 90%; max-height: 100vh;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="text-transform: uppercase;"></h4>
                </div>
                <div class="modal-body">
                    <iframe src="sequence_control_andon.php" style="width: 100%; height: 70vh;">
                    </iframe>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="btn_view_seq_control_andon_page" data-dismiss="modal" style="width: 160px;">View Page</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="overview1_modal">
        <div class="modal-dialog" style="max-width: 90%; max-height: 100vh;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="text-transform: uppercase;"></h4>
                </div>
                <div class="modal-body">
                    <iframe src="overview_screen.php" style="width: 100%; height: 80vh;">
                    </iframe>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="btn_overview1_page" data-dismiss="modal" style="width: 160px;">View Page</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="overview2_modal">
        <div class="modal-dialog" style="max-width: 90%; max-height: 100vh;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="text-transform: uppercase;"></h4>
                </div>
                <div class="modal-body">
                    <iframe src="part_delivery_driver.php" style="width: 100%; height: 70vh;">
                    </iframe>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="btn_overview2_page" data-dismiss="modal" style="width: 160px;">View Page</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="help_modal">
        <div class="modal-dialog">
            <div class="modal-content bg-danger">
                <div class="modal-header">
                    <h4 class="modal-title">ANDON HELP</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="row modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="confirm_help" data-dismiss="modal" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="container_number_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Container Number</h4>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-group">
                            <label class="">Container Number</label>
                            <input type="text" class="form-control" id="container_number" name="container_number">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="enter_container_number" data-dismiss="modal" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="status_history_modal">
        <div class="modal-dialog" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Status History</h4>
                </div>
                <div class="modal-body">
                    <div class="row relative" id="status_history_modal_bar">

                    </div>
                    <div class="relative mt-2 mb-8 mx-auto" id='time-bar-modal'>

                    </div>
                    <div class="row">
                        <div class="col-md-4 mx-auto">
                            <div class="row">
                                <button type="button" class="btn btn-primary mx-auto" id="#">All</button>
                                <button type="button" class="btn btn-default mx-auto" id="#">Staffed</button>
                            </div>
                            <canvas id="chart-modal" style="width:100%;max-width:300px;margin:auto;"></canvas>
                        </div>
                        <div class="col-md-8 mx-auto">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <table class="table table-striped table-borderless uppercase">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">fault</th>
                                                        <th scope="col">start</th>
                                                        <th scope="col">end</th>
                                                        <th scope="col">duration</th>
                                                        <th scope="col">reason</th>
                                                    </tr>
                                                </thead>
                                                <tbody id='andon-duration'>

                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="status_history_modal_ok" data-dismiss="modal" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>



    <div class="modal fade" id="andon_incomplete_kanbans_modal">
        <div class="modal-dialog" style="max-width: 95%; ">
            <div class="modal-content bg-black">
                <div class="modal-header px-auto mx-auto border-0">
                    <h4 class="modal-title">ANDON / INCOMPLETE KANBANS</h4>
                </div>
                <div class="modal-body">
                    <div class="border px-0 mx-0 text-center" id="andon_incomplete_kanbans" style="border-radius: 15px;">
                        <div class="row mt-4" style="justify-content:center;">
                            <p class="h4 mx-2">SELECT TL:</p>
                            <?php
                            echo '<select id="select_tl">';
                            $users = get_all_users();
                            echo '<option/>';
                            foreach ($users as $user) {
                                if ($user['type'] == 1)
                                    echo '<option value="' . $user['user_id'] . '">' . $user['username'] . '</option>';
                            }
                            echo '</select>';
                            ?>
                        </div>
                        <div class="row mt-4" style="justify-content:space-around;" id="incomplete_kanbans_modal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="incomplete_kanbans_modal_ok" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="uploads_files_modal">
        <div class="modal-dialog" style="max-width: 100%; ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">ANDON / INCOMPLETE KANBANS</h4>
                </div>
                <div class="modal-body">
                    <div class="border px-0 mx-0" id="uploads_files" style="border-radius: 15px;">
                        <div class="row border-bottom py-2 mx-2" style="justify-content: space-around;">
                            <div style="align-self: center;">
                                <p class="h4 font-weight-bold">DEVAN SCHEDULE</p>
                                <button type="button" class="btn btn-primary mx-auto" id="#">
                                    <p class="h6 font-weight-bold">Import Data</p>
                                </button>
                            </div>
                            <div style="align-self: center;">
                                <p class="h6 font-weight-bold">Last Uploaded</p>
                                <p class="h6 font-weight-bold">01/01/2023 01:00</p>
                            </div>
                            <div style="align-self: center;">
                                <p class="h6 font-weight-bold">Last Uploaded</p>
                                <p class="h6 font-weight-bold">01/01/2023 01:00</p>
                            </div>
                        </div>
                        <div class="row border-bottom py-2 mx-2" style="justify-content: space-around;">
                            <div style="align-self: center;">
                                <p class="h4 font-weight-bold">PICK / DEL PLAN</p>
                                <button type="button" class="btn btn-primary mx-auto" id="#">
                                    <p class="h6 font-weight-bold">Import Data</p>
                                </button>
                            </div>
                            <div style="align-self: center;">
                                <p class="h6 font-weight-bold">Last Uploaded</p>
                                <p class="h6 font-weight-bold">01/01/2023 01:00</p>
                            </div>
                            <div style="align-self: center;">
                                <p class="h6 font-weight-bold">Last Uploaded</p>
                                <p class="h6 font-weight-bold">01/01/2023 01:00</p>
                            </div>
                        </div>
                        <div class="row border-bottom py-2 mx-2" style="justify-content: space-around;">
                            <div style="align-self: center;">
                                <p class="h4 font-weight-bold">MASTER KANBAN</p>
                                <button type="button" class="btn btn-primary mx-auto" id="#">
                                    <p class="h6 font-weight-bold">Import Data</p>
                                </button>
                            </div>
                            <div style="align-self: center;">
                                <p class="h6 font-weight-bold">Last Uploaded</p>
                                <p class="h6 font-weight-bold">01/01/2023 01:00</p>
                            </div>
                            <div style="align-self: center;">
                                <p class="h6 font-weight-bold">Last Uploaded</p>
                                <p class="h6 font-weight-bold">01/01/2023 01:00</p>
                            </div>
                        </div>
                        <div class="row border-bottom py-2 mx-2" style="justify-content: space-around;">
                            <div class="bg-primary px-2 text-center" style="align-self: center;">
                                <p class="h6 font-weight-bold">RENBAN</p>
                                <p class="h6 font-weight-bold">NO PROFIX:</p>
                                <p class="h3 font-weight-bold">EM097</p>
                            </div>
                            <div class="bg-primary px-2 text-center" style="align-self: center;">
                                <p class="h6 font-weight-bold">CYCLE</p>
                                <p class="h6 font-weight-bold">SETTINGS</p>
                                <p class="h3 font-weight-bold">15</p>
                            </div>
                            <div class="bg-primary px-2 text-center" style="align-self: center;">
                                <p class="h6 font-weight-bold">OPR PICK</p>
                                <p class="h6 font-weight-bold">SETTINGS</p>
                                <p class="h3 font-weight-bold">30</p>
                            </div>
                            <div class="bg-primary px-2 text-center" style="align-self: center;">
                                <p class="h6 font-weight-bold">OPR DEL</p>
                                <p class="h6 font-weight-bold">SETTINGS</p>
                                <p class="h3 font-weight-bold">30</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="confirm_help_with_user" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="devan_stocking_build_cycle_modal">
        <div class="modal-dialog" style="max-width: 100%; ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">ANDON / INCOMPLETE KANBANS</h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="margin-left: 0px !important; margin-right: 0px !important;">
                        <div class="col-md-4">
                            <div>
                                <div>
                                    <h3>DEVAN</h3>
                                </div>
                                <div class="card-body" id="devan_screen_modal" style="padding: 0">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <h3>STOCKING</h3>
                            </div>
                            <div class="row border mx-2 my-2" style="justify-content: space-between; border-radius: 15px;">
                                <div class="col-md-3">
                                    <canvas id="System_Fill_Chart" style="width:100%;max-width:600px; margin-top:-25px; "></canvas>
                                </div>
                                <div class="col-md-3">
                                    <canvas id="Part_Stocking_Chart" style="width:100%;max-width:600px; margin-top:-25px; "></canvas>
                                </div>
                                <div class="col-md-3">
                                    <canvas id="Free_Location_Chart" style="width:100%;max-width:600px; margin-top:-25px;"></canvas>
                                </div>

                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p style="color:#377184; text-transform: uppercase;"> <b>Members: </b> </p>
                                        </div>
                                        <div class="col-md-6" id="stocking_users">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="cold-md-6 mr-4">
                                            <p style="text-align: center;"> <b>Booked IN</b></p>
                                            <p style="text-align: center; font-size: 36px"> <?php echo $booked_in_out['booked_in']; ?> </p>
                                        </div>
                                        <div class="cold-md-6">
                                            <p style="text-align: center;"> <b>Booked OUT</b></p>
                                            <p style="text-align: center; font-size:36px"> <?php echo $booked_in_out['booked_out']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div>
                                <div>
                                    <h3>&nbsp</h3>
                                </div>
                                <div class="card-body" id="#" style="padding: 0">
                                    <div class="col-md-12 mx-0 px-0 text-center my-2 border" style="border-radius: 15px;">
                                        <p class="h6 font-weight-bold mt-1">BUILD/CYCLE</p>
                                        <p style="color:green; font-size: 90px; font-weight: bold;" id="build_cycle_count"></p>
                                        <p style="color:#2c577a; font-size: 24px; font-weight: bold;" id="build_cycle_cycle"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="confirm_help_with_user" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="loading_window">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p class="h1">Importing data ...</p>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/js/adminlte.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script> -->
    <!-- <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> -->
    <script src="assets/js/Chart.min.js"></script>


    <script>
        function convert_uk_format(datetimestr) {
            // const datetime = new Date(datetimestr)
            // const year = datetime.getFullYear();
            // const month = datetime.getMonth();
            // const day = datetime.getDay();
            // const hour = datetime.getHours();
            // const min = datetime.getMinutes();
            // const sec = datetime.getSeconds();
            // const result = `${day}/${month}/${year} ${hour}:${min}:${sec}`;
            var m = new Date(datetimestr);
            var result = +m.getUTCDate() + "/" + (m.getUTCMonth() + 1) + "/" + m.getUTCFullYear() + " " + m.getUTCHours() + ":" + m.getUTCMinutes() + ":" + m.getUTCSeconds();
            console.log(datetimestr, result)
            return result;
        }
        $(document).ready(function() {

            //get_andons_info();

            function get_andons_info() {
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                let hr = today.getHours();
                let min = today.getMinutes();
                let sec = today.getSeconds();
                const formattedToday = yyyy + '-' + mm + '-' + dd;

                // const yesterday = new Date();
                // yesterday.setDate(today.getDate() - 1);
                // const yyyy_yt = yesterday.getFullYear();
                // let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
                // let dd_yt = yesterday.getDate();
                // const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt + ' ' + hr + ":" + min + ":" + sec;

                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_andons_info_teamleader",
                        page: 'team leader',
                        today: formattedToday,
                        // yesterday: formattedYesterday,

                    },
                    dataType: 'HTML',
                }).done(function(result) {
                    console.log("->>", result)
                    result = JSON.parse(result)
                    const reasons = result.reasons.concat(null);
                    result = result.res;
                    const pivot = ~~((new Date()).setUTCHours(0, 0, 0, 0) / 1000)
                    // console.log("--->", result, pivot)
                    let andons = result.map(v => {
                        return {
                            kanban: v[0],
                            startD: convert_uk_format(new Date(v[2])),
                            start: Math.floor((new Date(v[2])).getTime() / 1000 - pivot),
                            end: Math.floor((new Date((v[3]) ? new Date(v[3]) : new Date(v[4]))).getTime() / 1000 - pivot),
                            endD: convert_uk_format(v[3] || v[4]),
                            pending: (v[3] == null) && (v[4] == null),
                            reason: v[5]
                        }
                    })
                    andons = andons.map(v => {
                        let [newstart, newend] = [v.start, v.end];
                        const scale = 1800
                        if (v.end - v.start < scale) {
                            if (v.end - scale > pivot) {
                                newend += 1800 - (v.end - v.start)
                            } else {
                                newstart = v.end - scale;
                            }
                        }
                        return {
                            ...v,
                            newstart: newstart,
                            newend: newend,
                        }
                    })
                    andons = andons.filter(v => !v.pending)

                    console.log('andons=', andons)
                    let html = `<div style='width: 100%; height: 5vh; background-color:green'></div>`
                    const seconds_in_day = 3600 * 24;
                    andons.map((andon, ind) => {
                        let {
                            newstart,
                            newend
                        } = andon
                        if (newstart < 0) newstart = 0
                        if (newend > seconds_in_day) newend = seconds_in_day;
                        const left = newstart / seconds_in_day * 100
                        const width = (newend - newstart) / seconds_in_day * 100
                        html += `<div style='width: ${width}%; height: 5vh; background-color:red; position: absolute; left: ${left}%; top: 0px;'></div>`
                    })
                    $('#status_history_bar').html(html)
                    $('#status_history_modal_bar').html(html)
                    let yValues = [andons.reduce((a, b) => a + b.end - b.start, 0) + 1800, seconds_in_day]
                    if (andons.length == 0) yValues = [0, seconds_in_day]
                    console.log(">>", yValues, seconds_in_day, andons)
                    var barColors = [
                        "red",
                        "green",
                    ];

                    new Chart("chart-modal", {
                        type: "pie",
                        data: {
                            labels: ['', ''],
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: false,
                                text: ""
                            },
                            tooltips: {
                                enabled: false
                            },
                            responsive: true,
                            plugins: {
                                tooltip: {
                                    enabled: false // <-- this option disables tooltips
                                }
                            }
                        }
                    });
                    const reason_result = andons.map(v => {
                        let duration = v.end - v.start;
                        let formatString = ''
                        const hr = ~~(duration / 3600);
                        duration = duration % 3600
                        if (hr > 0) formatString += hr + ": ";
                        const min = ~~(duration / 60);
                        duration = duration % 60
                        if ((hr > 0 || min > 0)) formatString += min + ": ";
                        const sec = ~~(duration);
                        duration = duration % 60
                        formatString += sec;
                        return {
                            ...v,
                            reason: v.reason,
                            formatString,
                        }
                    })
                    console.log(reason_result)
                    html = ``;
                    reason_result.map(v => {
                        html += `
                        <tr>
                            <td>${v.kanban}</td>
                            <td>${v.startD}</td>
                            <td>${v.endD}</td>
                            <td>${v.formatString}</td>
                            <td>${v.reason || ""}</td>
                        </tr>
                        `
                    })
                    // alert(html)
                    $('#andon-duration').html(html)
                    let current_hr = 24
                    let hrs_list = [];
                    for (var i = 0; i < 7; i++) {
                        hrs_list.push(`${current_hr}:00`)
                        current_hr -= 4;

                    }
                    hrs_list.reverse();
                    html = ``;
                    hrs_list.map((v, ind) => {
                        html += `<div class="absolute" style="left: ${16*ind}%">${v}</div>`
                    })
                    $('#time-bar').html(html)
                    $('#time-bar-modal').html(html)
                    // console.log(andons)
                });
            }

            $("#date").datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                    previous: 'fas fa-angle-left',
                    next: 'fas fa-angle-right',
                }
            });

            $(document).on('keypress', "#renban_no_prefix", function(e) {
                if (e.keyCode == 13) {
                    var set_value = $(this).val();
                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            action: 'save_setting',
                            set_type: 'renban_no_prefix',
                            set_value: set_value,
                        },
                    }).done(function(result) {
                        if (result == "Ok")
                            alert('Saved successfully');
                        else
                            alert('Save failed');
                    });
                }
            });

            $(document).on('keypress', "#opr_cycle_settings", function(e) {
                if (e.keyCode == 13) {
                    var cycle_value = $(this).val();
                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            action: 'save_cycle_setting',
                            cycle_value: cycle_value
                        },
                    }).done(function(result) {
                        if (result == 'Ok') {
                            alert("Saved successfully")
                        } else {
                            alert("Failed to save")
                        }
                    });
                }
            });

            $(document).on('keypress', "#opr_pick_settings", function(e) {
                if (e.keyCode == 13) {
                    var opr_pick_value = $(this).val();
                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            action: 'save_opr_pick_delivery_setting',
                            opr_type: 'pick',
                            val: opr_pick_value
                        },
                    }).done(function(result) {
                        if (result == 'Ok') {
                            alert("Saved successfully")
                        } else {
                            alert("Failed to save")
                        }
                    });
                }
            });

            $(document).on('keypress', "#opr_del_settings", function(e) {
                if (e.keyCode == 13) {
                    var opr_deliver_value = $(this).val();
                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            action: 'save_opr_pick_delivery_setting',
                            opr_type: 'delivery',
                            val: opr_deliver_value
                        },
                    }).done(function(result) {
                        if (result == 'Ok') {
                            alert("Saved successfully")
                        } else {
                            alert("Failed to save")
                        }
                    });
                }
            });

            $("#status_history_bar").on('click', function() {
                $("#status_history_modal").modal();
            });

            $("#renban_button").on('click', function() {
                $("#container_number_modal").modal();
            });

            $("#btn_seq_control_andon").on('click', function() {
                $("#seq_control_andon_modal").modal();
            });

            $("#btn_pick_stock_devan_overview").on('click', function() {
                $("#overview1_modal").modal();
            });

            $("#btn_del_driver_overview").on('click', function() {
                $("#overview2_modal").modal();
            });

            $("#devan_stocking_build_cycle").on('click', function() {
                // $("#devan_stocking_build_cycle_modal").modal({
                //         // backdrop: 'static',
                //         keyboard: false
                // });
            });

            $("#andon_incomplete_kanbans").on('click', function() {
                $("#andon_incomplete_kanbans_modal").modal({
                    // backdrop: 'static',
                    keyboard: false
                });
            });

            $("#uploads_files").on('click', function() {
                // $("#uploads_files_modal").modal({
                //         // backdrop: 'static',
                //         keyboard: false
                // });
            });

            $("#btn_view_seq_control_andon_page").on('click', function() {
                window.location.replace("sequence_control_andon.php");
            });

            $("#btn_overview1_page").on('click', function() {
                window.location.replace("overview_screen.php");
            });

            $("#btn_overview2_page").on('click', function() {
                window.location.replace("part_delivery_driver.php");
            });

            $(document).on('click', '#enter_container_number', function() {
                var container_number = $("#container_number").val();
                if (container_number == '')
                    return false;
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        'action': 'update_revan_state',
                        'container_number': container_number,
                    },
                }).done(function(result) {
                    if (result != 'Failed') {
                        $("#container_number").val('');
                        console.log(result);
                    }
                });

            });

            get_overview_realtime();
            // custom interval
            setInterval(get_overview_realtime, 5000); //update every 30 seconds

            Chart.pluginService.register({
                beforeDraw: function(chart) {
                    if (chart.options.centertext) {
                        var width = chart.chart.width,
                            height = chart.chart.height,
                            ctx = chart.chart.ctx;

                        ctx.restore();
                        var fontSize = (height / 80 / 2).toFixed(2); // was: 114
                        ctx.font = fontSize + "em sans-serif";
                        ctx.textBaseline = "middle";

                        var text = chart.options.centertext, // "75%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2 + (chart.titleBlock.height);

                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                    if (chart.options.title_text) {
                        var width = chart.chart.width,
                            height = chart.chart.height,
                            ctx = chart.chart.ctx;

                        ctx.restore();
                        var fontSize = (height / 80 / 2.2).toFixed(2); // was: 114
                        ctx.font = fontSize + "em sans-serif";
                        // ctx.fontSize = 100;
                        ctx.textBaseline = "middle";

                        var text = chart.options.title_text, // "75%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2 + (chart.titleBlock.height) + 20;

                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                    if (chart.options.pick_text1) {
                        var width = chart.chart.width,
                            height = chart.chart.height,
                            ctx = chart.chart.ctx;

                        ctx.restore();
                        var fontSize = (height / 150).toFixed(2) * 1.5; // was: 114
                        ctx.font = "bold " + fontSize + "em sans-serif";
                        ctx.textColor = "red";
                        // ctx.fontSize = 100;
                        ctx.textBaseline = "middle";

                        var text = chart.options.pick_text1, // "75%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2 + (chart.titleBlock.height) - 10;

                        ctx.fillStyle = 'rgba(44, 87, 122, 1)';
                        ctx.fillText(text, textX, textY);
                        // 2c577a

                        ctx.save();
                    }
                    if (chart.options.pick_text2) {
                        var width = chart.chart.width,
                            height = chart.chart.height,
                            ctx = chart.chart.ctx;

                        ctx.restore();
                        var fontSize = (height / 80 / 2).toFixed(2) * 1.5; // was: 114
                        ctx.font = "bold " + fontSize + "em sans-serif";
                        // ctx.fontSize = 100;
                        ctx.textBaseline = "middle";

                        var text = chart.options.pick_text2, // "75%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2 + (chart.titleBlock.height) + 10;

                        ctx.fillText(text, textX, textY);
                        // ctx.fillStyle = 'rgba(44, 87, 122, 1)';
                        ctx.save();
                    }
                }
            });

            function get_overview_realtime() {
                // read_stocking_overview();
                get_active_members();
                get_last_upload_time();
                get_opr_settings();
                get_low_stocks();
                get_active_members();

                read_remaining_incomplete(true);
                read_remaining_build_delivery();
                // draw_pick_cycle_chart();
                // draw_delivery_cycle_chart();
            }

            // function draw_part_chart() {
            //     return;
            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             action: "get_part_chat_value",
            //             // page: 'Stocking'
            //         },
            //         dataType: 'HTML',
            //     }).done(function(response) {
            //         var result = JSON.parse(response);
            //         var xValues = [];
            //         var yValues = [];
            //         var barColors = [];//"red", "green","blue","orange","brown", "green","blue","orange"];

            //         var parts = result.part;
            //         var values = result.val;
            //         var nIndex = 0;
            //         for(var i=0; i<values.length; i++) {
            //             var tmp = parseInt(values[i]);
            //             if(tmp > 0) {
            //                 xValues[nIndex] = parts[i].toUpperCase();
            //                 yValues[nIndex] = tmp;
            //                 if(tmp <= 2)
            //                     barColors[nIndex] = "#DD0000";
            //                 else if(tmp <= 5)
            //                     barColors[nIndex] = "yellow";
            //                 else
            //                     barColors[nIndex] = "green";
            //                 nIndex++;
            //             }
            //         }

            //         // new Chart("Part_Chart", {
            //         // type: "bar",
            //         // data: {
            //         //     labels: xValues,
            //         //     datasets: [{
            //         //         backgroundColor: barColors,
            //         //         data: yValues
            //         //     }]
            //         // },
            //         // options: {
            //         //     responsive: true,
            //         //     // maintainAspectRatio: true,
            //         //     // aspectRatio: 2,
            //         //     animation: {
            //         //         duration: 0
            //         //     },
            //         //     legend: {display: false},
            //         //     title: {
            //         //         display: false,
            //         //         text: ""
            //         //     },
            //         //     scales:{
            //         //         xAxes: [{
            //         //             display: true //this will remove all the x-axis grid lines
            //         //         }],
            //         //         yAxes: [{
            //         //             display: true //this will remove all the x-axis grid lines
            //         //         }]
            //         //     }
            //         // }
            //         // });
            //     });


            // }

            $(document).on('click', '.btn_save_reason', function() {
                var id = $(this).val();
                console.log(id);

                var [id, is_pick] = id.split(' ')
                var reason_select_id = `reason_select_${id}`;
                var reason = $(`#${reason_select_id}`).val();
                var del_skip_status = $(`#del_skip_status_${id}`).is(":checked");
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                let hr = today.getHours();
                let min = today.getMinutes();
                let sec = today.getSeconds();
                const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;

                console.log(id, reason);
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        kanban_id: id,
                        action: "conveyance_pick",
                        reason: reason,
                        today: formattedToday,
                        is_pick,
                    },
                }).done(function(result) {
                    if (result == 'ok') {
                        alert('Complete successfully');
                        read_remaining_incomplete();
                    } else {
                        alert('Failed');
                    }
                });
            });

            function read_remaining_incomplete(refreshing = false) { // Get remaining cycles, incomplete kanbans
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                let hr = today.getHours();
                let min = today.getMinutes();
                let sec = today.getSeconds();
                const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;

                const yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);
                const yyyy_yt = yesterday.getFullYear();
                let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
                let dd_yt = yesterday.getDate();
                const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt;


                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'get_remaining_incomplete_pick_team_leader',
                        today: formattedToday,
                        yesterday: formattedYesterday
                    },
                    dataType: 'JSON',
                }).done(function(response) {
                    var ids = response.id;
                    var is_completed = response.is_completed;
                    var is_delivered = response.is_delivered;

                    var reasons = response.reasons;
                    var remaining_cycles = parseInt(response.remaining_cycles);
                    var incomplete_cycles = response.incomplete_cycle;
                    var incomplete_kanbans = response.incomplete_kanban;
                    var helped_time = response.helped_time;
                    var is_pick = response.is_pick;
                    var helped_user = response.helped_user;
                    var max_cycle = parseInt(response.max_cycle);
                    var length = incomplete_kanbans.length;
                    // if (remaining_cycles > 0) {
                    //     $("#remaining_cycles_pick").removeClass("btn-success");
                    //     $("#remaining_cycles_pick").addClass("btn-danger");
                    // }

                    // $("#remaining_cycles_pick").html(`<p style="margin-top:-12px; font-size:5vw">${remaining_cycles}</p>`);

                    var html = '<div class="mx-auto"><h6 style="color:#2c577a"><b>KANBAN</b></h6>';
                    var kanban_html = '';
                    var cycle_html = '';
                    var process_html = '';
                    var time_html = '';
                    var member_html = '';
                    var reason_html = '';
                    var tmp_reason_html = '';
                    var del_skip_html = '';
                    var save_button_html = '';
                    var count_html = '';

                    console.log("===", is_pick)
                    for (var i = 0; i < length; i++) {
                        const flag = (is_pick[i] == "1") ? is_completed[i] : is_delivered[i]
                        if (flag == 0) {
                            kanban_html += `<p class="h5" style="color:red">${incomplete_kanbans[i]}</p>`
                            cycle_html += `<p class="h5" style="color:red">${incomplete_cycles[i]}/${max_cycle}</p>`
                            process_html += `<p class="h5" style="color:red">${(is_pick[i] == "1")? "PICK":"DELIVERY"}</p>`
                            if (helped_time[i] != null && helped_time[i] != undefined)
                                time_html += `<p class="h5" style="color:red">${helped_time[i].substr(11, 5)}</p>`
                            else
                                time_html += `<p class="h5" style="color:red">&nbsp</p>`
                            if (helped_user[i] != null && helped_user[i] != undefined)
                                member_html += `<p class="h5" style="color:red">${helped_user[i]}</p>`
                            else
                                member_html += `<p class="h5" style="color:red">&nbsp</p>`
                        } else {
                            kanban_html += `<p class="h5" style="color:green">${incomplete_kanbans[i]}</p>`
                            cycle_html += `<p class="h5" style="color:green">${incomplete_cycles[i]}/${max_cycle}</p>`
                            process_html += `<p class="h5" style="color:green">${(is_pick[i] =="1")? "PICK":"DELIVERY"}</p>`
                            if (helped_time[i] != null && helped_time[i] != undefined)
                                time_html += `<p class="h5" style="color:green">${helped_time[i].substr(11, 5)}</p>`
                            else
                                time_html += `<p class="h5" style="color:green">&nbsp</p>`
                            if (helped_user[i] != null && helped_user[i] != undefined)
                                member_html += `<p class="h5" style="color:green">${helped_user[i]}</p>`
                            else
                                member_html += `<p class="h5" style="color:green">&nbsp</p>`
                        }

                        // build reason select option
                        tmp_reason_html = `<select id="reason_select_${ids[i]}" style="height: 25px; margin:3px">`;
                        tmp_reason_html += `<option value="0"></option>`
                        for (var j = 0; j < reasons.length; j++) {

                            tmp_reason_html += `<option value=${reasons[j].id}>${reasons[j].name}</option>`
                        }
                        tmp_reason_html += '</select>';
                        reason_html += tmp_reason_html;
                        // build del/skip html
                        del_skip_html += `<input type="checkbox" id="del_skip_status_${ids[i]}" style="width:25px; height: 25px; margin:3px">`;
                        save_button_html += `<button class="btn btn-success btn_save_reason" style="height:25px; margin:3px" value="${ids[i]} ${is_pick[i]}" ><p style="margin-top: -5px">Save</p></button>`;
                    }
                    html += kanban_html + '</div>';
                    html += '<div class="mx-auto"><h6 style="color:#2c577a"><b>CYCLE</b></h6>';
                    html += cycle_html + '</div>';
                    html += '<div class="mx-auto"><h6 style="color:#2c577a"><b>PROCESS</b></h6>';
                    html += process_html + '</div>';
                    html += '<div class="mx-auto"><h6 style="color:#2c577a"><b>TIME</b></h6>';
                    html += time_html + '</div>';
                    html += '<div class="mx-auto"><h6 style="color:#2c577a"><b>MEMBER</b></h6>';
                    html += member_html + '</div>';
                    $("#incomplete_kanbans").html(html);

                    html += '<div class="mx-auto" style="display:flex; flex-direction: column;"><h6 style="color:#2c577a"><b>REASON</b></h6>';
                    html += reason_html + '</div>';
                    html += '<div class="mx-auto" style="display:flex; flex-direction: column; align-items:center;"><h6 style="color:#2c577a"><b>DEL / SKIP</b></h6>';
                    html += del_skip_html + '</div>';
                    html += '<div class="mx-auto" style="display:flex; flex-direction: column; align-items:center;"><h6 style="color:#2c577a"><b>&nbsp</b></h6>';
                    html += save_button_html + '</div>';
                    $("#incomplete_kanbans_modal").html(html);

                    var pick_html = `<p class="h6 font-weight-bold">PICK</p>
                            <button type="button" class="btn `;
                    if (response.incomplete_pick_count > 0)
                        pick_html += `btn-danger`;
                    else
                        pick_html += `btn-success`;
                    pick_html += ` rounded-circle mx-auto mt-3" style="width:5vw; height: 5vw;" id="#"><p class="h1 font-weight-bold" style="margin-top: -5px; font-size: 4vw;">${response.incomplete_pick_count}</p></button>`;
                    $("#incomplete_pick").html(pick_html);

                    var delivery_html = `<p class="h6 font-weight-bold">DELIVERY</p>
                            <button type="button" class="btn `;
                    if (response.incomplete_delivery_count > 0)
                        delivery_html += `btn-danger`;
                    else
                        delivery_html += `btn-success`;
                    delivery_html += ` rounded-circle mx-auto mt-3" style="width:5vw; height: 5vw;" id="#"><p class="h1 font-weight-bold" style="margin-top: -5px; font-size: 4vw;">${response.incomplete_delivery_count}</p></button>`;
                    $("#incomplete_delivery").html(delivery_html);

                    var stocking_html = `<p class="h6 font-weight-bold">STOCKING</p>
                            <button type="button" class="btn `;
                    if (response.incomplete_stocking_count > 0)
                        stocking_html += `btn-danger`;
                    else
                        stocking_html += `btn-success`;
                    stocking_html += ` rounded-circle mx-auto mt-3" style="width:5vw; height: 5vw;" id="#"><p class="h1 font-weight-bold" style="margin-top: -5px; font-size: 4vw;">${response.incomplete_stocking_count}</p></button>`;
                    $("#incomplete_stocking").html(stocking_html);

                    var devan_html = `<p class="h6 font-weight-bold">DEVAN</p>
                            <button type="button" class="btn `;
                    if (response.incomplete_devan_count > 0)
                        devan_html += `btn-danger`;
                    else
                        devan_html += `btn-success`;
                    devan_html += ` rounded-circle mx-auto mt-3" style="width:5vw; height: 5vw;" id="#"><p class="h1 font-weight-bold" style="margin-top: -5px; font-size: 4vw;">${response.incomplete_devan_count}</p></button>`;
                    $("#incomplete_devan").html(devan_html);

                    var driver_html = `<p class="h6 font-weight-bold">DRIVER</p>
                            <button type="button" class="btn `;
                    if (response.incomplete_driver_count > 0)
                        driver_html += `btn-danger`;
                    else
                        driver_html += `btn-success`;
                    driver_html += ` rounded-circle mx-auto mt-3" style="width:5vw; height: 5vw;" id="#"><p class="h1 font-weight-bold" style="margin-top: -5px; font-size: 4vw;">${response.incomplete_driver_count}</p></button>`;
                    $("#incomplete_driver").html(driver_html);


                    // var complete_html = `<p class="h6 font-weight-bold">DRIVER</p>
                    //         <button type="button" class="btn `;
                    // if (response.complete_count <= 0)
                    //     complete_html += `btn-danger`;
                    // else
                    //     complete_html += `btn-success`;
                    // complete_html += ` rounded-circle mx-auto mt-3" style="width:5vw; height: 5vw;" id="#"><p class="h1 font-weight-bold" style="margin-top: -5px; font-size: 4vw;">${response.complete_count}</p></button>`;
                    // $("#complete_kanban").html(complete_html);
                });
            }

            function read_remaining_build_delivery() { // Get remaining cycles, build/cycle,
                // alert('read_remaining_build_delivery');
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                const formattedToday = yyyy + '-' + mm + '-' + dd;

                const yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);
                const yyyy_yt = yesterday.getFullYear();
                let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
                let dd_yt = yesterday.getDate();
                const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt;

                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'get_remaining_build_delivery',
                        today: formattedToday,
                        yesterday: formattedYesterday
                    },
                    dataType: 'JSON',
                }).done(function(response) {
                    // console.log("read_remaining_build_delivery", response);
                    var remaining_cycles = parseInt(response.remaining_cycles);
                    var remaining_cycles_pick = parseInt(response.remaining_cycles_pick);
                    if (remaining_cycles == null)
                        remaining_cycles = 0;
                    var count = response.count;
                    var cycle = response.cycle;
                    var cycle_str = "CYCLE" + Math.ceil(count / cycle);
                    $("#build_cycle_count").text(count);
                    $("#build_cycle_cycle").text(cycle_str);

                    // var html = `<p class="h1">${remaining_cycles}</p>`;
                    var html = `<p class="h1" style="font-size:5vw">${remaining_cycles}</p>`;
                    if (remaining_cycles > 0)
                        $("#remaining_cycles_delivery").addClass("bg-danger");
                    else
                        $("#remaining_cycles_delivery").addClass("bg-success");
                    $("#remaining_cycles_delivery").html(html);

                    html = `<p class="h1" style="font-size:5vw">${remaining_cycles_pick}</p>`;
                    if (remaining_cycles_pick > 0)
                        $("#remaining_cycles_pick").addClass("bg-danger");
                    else
                        $("#remaining_cycles_pick").addClass("bg-success");
                    $("#remaining_cycles_pick").html(html);
                });
            }

            function draw_delivery_cycle_chart() {
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                const formattedToday = yyyy + '-' + mm + '-' + dd;

                const yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);
                const yyyy_yt = yesterday.getFullYear();
                let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
                let dd_yt = yesterday.getDate();
                const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt;

                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'get_all_session_json',
                        today: formattedToday,
                        yesterday: formattedYesterday
                    },
                    dataType: 'JSON',
                }).done(function(result) {
                    // console.log("draw_delivery_cycle_chart", result);
                    var tmpUsers = result.users;
                    var keys = Object.keys(tmpUsers);
                    var length = keys.length;
                    var users = [];
                    for (var i = 0; i < length; i++) { // extracts session logged in conveyance_pick page from all sessions
                        var key = keys[i];
                        var user = tmpUsers[key];
                        if (user.page == "conveyance_delivery.php" && user.user != undefined) {
                            var allow_push = 1;
                            for (var j = 0; j < users.length; j++)
                                if (users[j].user.username == user.user.username)
                                    allow_push = 0;
                            if (allow_push == 1)
                                users.push(user);
                        }
                    }

                    // console.log("draw_delivery_cycle_chart: users: ", users);
                    // console.log("Delivery: ", users);
                    // if(length < 1)
                    // alert('There is no user logged in Pick');
                    $("#location_delivery").html('');
                    $("#delivery_svg").html('');

                    length = users.length;
                    if (length > 3)
                        length = 3;
                    else if (length < 1)
                        return;
                    // console.log("users: ", users);
                    var overview_delivery_zone = [];
                    var overview_delivery_kanban = [];
                    var overview_delivery_address = [];
                    var overview_delivery_user = [];
                    var overview_delivery_pick = [];
                    var overview_delivery_cycle = [];
                    var overview_delivery_OPR = [];
                    var html = '';
                    var location_pick_html = '';
                    var svg_html = '';
                    for (var i = 0; i < length; i++) { // build html for pick & cycle
                        // var key = keys[i];
                        // var user = users[key];                        
                        var user = users[i];
                        overview_delivery_zone[i] = user.overview_delivery_zone;
                        overview_delivery_kanban[i] = user.overview_delivery_kanban;
                        if (user.overview_delivery_address != undefined)
                            overview_delivery_address[i] = user.overview_delivery_address;
                        else
                            overview_delivery_address[i] = '';
                        overview_delivery_user[i] = user.overview_delivery_user;
                        overview_delivery_pick[i] = user.overview_delivery_pick;
                        var delivery_total = parseInt(overview_delivery_pick[i].split("/")[1]);
                        var delivery_delivered = parseInt(overview_delivery_pick[i].split("/")[0]);
                        var delivery_percentage = Math.ceil(delivery_delivered / delivery_total * 100);
                        // console.log(delivery_delivered, delivery_total, delivery_percentage);
                        overview_delivery_cycle[i] = user.overview_delivery_cycle;
                        overview_delivery_OPR[i] = user.overview_delivery_OPR;
                        var deliver_rect_width = Math.round(81.5 * (100 - delivery_percentage) / 100);
                        delivery_percentage = (100 - delivery_percentage) + "%";
                        var col_html = `<div class="col-md-4 mx-0 my-2 `;
                        if (i < length - 1)
                            col_html += `border-right`
                        col_html += `">
                                            <div class="row justify-content-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="body_2" width="210" height="100" class="ml-5">
                                                    <g transform="matrix(1.3333334 0 0 1.3333334 0 0)">
                                                        <image  x="0" y="0" width="150" height="75" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANMAAABkCAYAAAAcyAUUAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAACJVJREFUeJztnU1S20gYhh9SWUnlQjdAOcF46xXOCYATxJwg0QmAEwhOYHIC4AQ2Ky9xToBygtGUy95mFt0KQpb1a2OD36dqaoKk/lHre/trfd1qH8yC3h/ER+OqE04umyaeBb0ucAH0gWnBpdNOOAmalvPR+AzQCScH266IWA+zoHfZMr0HjIAr4Kbk8uEs6D12wsl9mzI/Cp+3XQGxcwwwHue67MJZ0LsHuoDEhMQkljkGHipe+98mKwJ/h5xe6lD276pE9j864WTctl55SEwii0/58O4tCTHiie3fEfC7QT7f7P89K9ApcNMJJ7dtK5ggMYks3U313C0I1l2nWdA7Bb7Pgt4FcNYJJ0WBlkp8al8t8VGYBb0+xdG7D0MnnNx3wslXjBcezYLeoG2eEpNIkwx/9gYbaAkwkcnTNnlJTCLNMfBr25V4a+x70zlGUN2m+UhMIo3PnnmmBCuoG2DYNA+JSaTZxeDDm5GsGmk68S0xCWC/gg8lnAMXs6Dn100oMYkEHzupuc/YEPkVZn6rFhKTSPDZw+DDCq6BvvXWlZGYRMIx8kwAdMJJjAlGXNRJJzGJNNG2K7BDXAPdOqFyiUkkdJGY/pLyTt+rppGYRILXCSfRtiuxY9wCg6qRPYlJYI0lLrtu37Cdyy3mG69SJCYBe7zyoQI/efl8oxCJSYgCkhUhVRbBSkwCyjdO2XdugJOyiyQmkbDxT9DfMfeYQETh5/ISkwA43HYFdhkbiJgChUM9iUmAmWMab7sSO85PSoZ6EpMQ1bjHvFuuRGIS0GzrrL3CDvWioqiexCRgzz8KrMEYsyA4F4lJiOo8UBCEkJiEqIj13v6qtXoS056jz9VrszIQITEJ0CLXOvxixXuTxCREPcaYebklJCahsHgN7HtTN29pkcQkusDjtivxzhiT450kJiHqMyUnCCExCVGfX8A/2YMSkxD1iTBfJ79CYhLaL68mSRAie1xiEiAxNSHK7vgqMQnRjIjMtILEJEQzHskM9SQmIZoRA0fpAxKT8NHavCZMyUT0JCbh298kEvWI0DBPiPbYz9gVgBBiTcTpn5yRmIRozpSUd5KYhGiHn/xDYhKiOY9ITAKgbO9sUQ+Jab/RtsjtmJLaD0JiEqI5rya7JSYhmhOjdyYh2mNXjvjJ3xKTEGtCYhKiHVGyCkJiEqIdEXYVhMQkxJqQmIRoR4T9FENiEqIdv9EwT4j1IjEJ0Y4Iu7urxLTf9NGeeW2J0DBPWH5vuwIfBYlJiHZEKJonRHvSG6tITEKsCYlJiPbEs6DnS0xCtGcKSExi5+nzTsL3n7ddAbERjmdB77LKdbT7cegIOGmRvpBkwxf7kr/LxMgziZZMgf4GdzkaAPcbynud/AJ8eaaPyWMnnFyWXVTRe62kE06ms6B3DzzNgt4UY1Tr4Agzd+MDX9eU58aRZxKt6ISTc+CMdsPFLL+BAPjyjn6h41CeSbTGGvx7MfpNMAYu5JmEWBMSkxDtUTRPiHWQ7J8nMQmxJiQmcbjtCrx3ZkHPB62A+KgczYJev8p1mEnXR/SL6234DtwezILen23XRKydiOrr2SJS+2WLRkyBq21XQgghhBBCCCF2mQPH9fqYDSG6qeO3i3kc1cnIcb0ucJo6NAami3msKNEKHNdL2r2L3ZQDExC4r9tujuud8voZjilpf8f1/FT5CfeLeVx7nV1O+dPFPN765xPWvn1egiwx5h6jmvlk7XuKuce/+XwCRpjQXsIR8OS4XlijoKHNJ5mzOAJC4K5OhfeQLqaNjlPHvgHP1jhLcVzPc1zvCdPe6fYfAj9Kkg9suoRD4M5xvSdrPFXK7zqu95yTz9Aa8rYZ8foDxmNM+w6qJLbtO8I8p6R9DzH3O8he/K/todLHuvZ42cPAcb3QNr6XOd63lRArWNVGjusN8p5LznWebfthzrlLx/UuS9L/WJF2aMsv/OjPlp9rJ47rjXZBTI7rLU39pOy7tMOy9zHMse+l9v2EcVeveiHr5s+Ai6IGtb3XADjLGU702e9l+VWIyLQ9wGIe3wK3vO7t8/gBxIt5fJ5z7h/K55rGmOeULf/cnlsSWoYQGC/m8XXOOb8k7ZuRtWFr3+eUtK/1Xj4Q5Nj3EdmJbquw3EZzXC9x+UuCSqn7ckXap6qudJ9xXO85b0hV1Ovb84Xey54rHapVKH9Qt3ybdicWA1jPMig4t8r2+0XeK6/dDmxjPAFfsuqzIhpieq9b4AHzonyC8UhXi3l8mVPQyjzFa+y7qZfnXezDusN4mJ+8rFb4hvFo53kv+dZ4Lhbz+Msayh9hRhh55X/NC1bYDuB4MY/PysrfNLYuJ4t5vPT5u7XvEcamb7BbdmHeq04xHuk2J10fGGbb98CeHAGPecJIJT7hZUjySEHEz3G9OyBazOOg6EbFq45nlWF6mAd7wkvE7wHT/rkdlQ0IXOUZQsPyBxgD8zBDm+T5L5Vvr3/GDP3HZeVvmir1sZ3PMS9D0wcKIn6FenFcz3dc70/VCE4RKfe/qR1rPhx2qJ07nG6QV2ije9sqf7hrgScbaHle0/2V55USQWNBpd6j+k3z2FeKxu818qgUBdxg+cN1Ge26sXVr1WHY9q3mdFIh0dqCSj3IQZOK7jvOS5h71OSBt3l2mfKXwsAV07Yq/y1ICcpvkDYR0qBJossqjWqHiHfySO3JGOWgYpqB9QajJkaSU/7I5tdvUP7OeaQs1q7/bWjfhR3FwYoMupgYfBezo+YDZj4hTp3vYl6KTzGRvrxYvGiAY8KxyRzIDWbZytieS5YgJW0PFYMNNcr/AVzwEkUcJ8EJ52UJUhLxWnv5m2aFfUepe+xjghFJG19j7rHQvnPFlCk0CYP2U6dizKTeyqiOaI8VVRJFXVp3h4kobWz9m/WOSfl+Tvk/m6zj2xVsxzDAdAxdXq+PnFLTvv8Hho/PaQQtgJ4AAAAASUVORK5CYII=" preserveAspectRatio="none" />
                                                        <rect x="1" y="1" width="${deliver_rect_width}" height="60.3" style="fill:#ed7d0c; stroke-width:0;stroke:rgb(0,0,255)"/>
                                                        <text x="8" y="40" fill="black" font-size="28px" font-weight="bold">${delivery_percentage}</text>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="row mx-auto my-1 justify-content-center">
                                                <div class="row mx-1 my-0" style="background-color: green;">
                                                    <p class="mx-0 my-auto" style="color: white; transform: rotate(-90deg)">OPR
                                                    <p class="h2 my-auto mx-0 text-white font-weight-bold">${overview_delivery_OPR[i]}%</p>
                                                </div>
                                                <p class="mx-2 my-auto h4 font-weight-bold" style="text-transform: uppercase;">${overview_delivery_user[i]}</p>
                                            </div>
                                        </div>`;
                        svg_html += col_html;
                        var col_location_html = `<div class="col-md-4 text-center `;
                        if (i < length - 1)
                            col_location_html += `border-right`;
                        col_location_html += `">
                                                    <p class="h1 font-weight-bold" style="text-transform: uppercase;">${overview_delivery_zone[i]}</p>
                                                    <p class="h6" style="text-transform: uppercase;">Address</p>
                                                    <p class="h2 font-weight-bold">${overview_delivery_address[i]}</p>
                                                </div>`;
                        location_pick_html += col_location_html;
                    }

                    $("#location_delivery").html(location_pick_html);
                    $("#delivery_svg").html(svg_html);
                });
            }

            // function draw_pick_cycle_chart(){ //Draw Pick, Cycle Chart
            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             action: 'get_all_session_json',
            //         },
            //         dataType: 'JSON',
            //     }).done(function(result) {
            //         console.log("draw_pick_cycle_chart: ", result);
            //         var tmpUsers = result.users;
            //         var keys = Object.keys(tmpUsers);
            //         var length = keys.length;
            //         var users = [];
            //         for(var i=0; i<length; i++) { // extracts session logged in conveyance_pick page from all sessions
            //             var key = keys[i];
            //             var user = tmpUsers[key];
            //             if(user.page == "conveyance_pick.php" && user.user != undefined) {
            //                 var allow_push = 1;
            //                 for(var j=0; j<users.length; j++)
            //                     if(users[j].user.username == user.user.username)
            //                         allow_push = 0;
            //                 if(allow_push == 1)
            //                     users.push(user);
            //             }
            //         }

            //         // console.log("draw_pick_cycle_chart: users: ", users);
            //         // if(length < 1)
            //             // alert('There is no user logged in Pick');
            //         $("#pick_cycle_graph").html('');
            //         $("#location_pick").html('');

            //         length = users.length;
            //         if(length >3) 
            //             length=3;
            //         else if(length < 1)
            //             return;
            //         // console.log("users: ", users);
            //         var overview_pick_zone = [];
            //         var overview_pick_kanban = [];
            //         var overview_pick_user = [];
            //         var overview_pick_pick =[];
            //         var overview_pick_cycle =[];
            //         var overview_pick_OPR =[];
            //         var pick_charts = [];
            //         var cycle_charts = [];
            //         var html='';
            //         var location_pick_html = '';
            //         for(var i=0; i<length; i++) { // build html for pick & cycle
            //             // var key = keys[i];
            //             // var user = users[key];                        
            //             var user = users[i];
            //             overview_pick_zone[i] = user.overview_pick_zone;
            //             overview_pick_kanban[i] = user.overview_pick_kanban;
            //             overview_pick_user[i] = user.overview_pick_user;
            //             overview_pick_pick[i] = user.overview_pick_pick;
            //             overview_pick_cycle[i] = user.overview_pick_cycle;
            //             overview_pick_OPR[i] = user.overview_pick_OPR;
            //             pick_charts[i] = `Pick_Chart${i}`;
            //             cycle_charts[i] = `Cycle_Chart${i}`;
            //             var col_html = `<div class="col-md-4 my-0">
            //                                 <div class="row mx-auto my-1 justify-content-center">
            //                                     <p class="mx-2 my-auto h5" style="text-transform: uppercase;">${overview_pick_zone[i]}</p>
            //                                 </div>
            //                                 <div class="row">
            //                                     <div class="col-md-6">
            //                                         <canvas id="${pick_charts[i]}"></canvas>
            //                                     </div>
            //                                     <div class="col-md-6 `
            //                                     if(i<length - 1)
            //                                         col_html += `border-right`;
            //             col_html +=             `">
            //                                         <canvas id="${cycle_charts[i]}"></canvas>
            //                                     </div>                                                
            //                                 </div>
            //                                 <div class="row mx-auto my-1 justify-content-center">
            //                                     <div class="row mx-1 my-0" style="background-color: green;">
            //                                         <p class="mx-0 my-3 h5" style="color: white; transform: rotate(-90deg)">OPR
            //                                         <p class="h5 my-3 mx-0 text-white">${overview_pick_OPR[i]}%</p>
            //                                     </div>
            //                                     <p class="mx-2 my-auto h4 font-weight-bold" style="text-transform: uppercase;">${overview_pick_user[i]}</p>
            //                                 </div>
            //                             </div>`;
            //             var col_location_html = `<div class="col-md-4 text-center `;
            //                                         if(i<length - 1)
            //                                             col_location_html += `border-right`;
            //             col_location_html +=        `">
            //                                         <p class="h1 font-weight-bold" style="text-transform: uppercase;">${overview_pick_zone[i]}</p>
            //                                         <p class="h6">KANBAN</p>
            //                                         <p class="h2 font-weight-bold">${overview_pick_kanban[i]}</p>
            //                                     </div>`;
            //             html += col_html;
            //             location_pick_html += col_location_html;
            //         }
            //         $("#pick_cycle_graph").html(html);
            //         $("#location_pick").html(location_pick_html);

            //         // draw chart
            //         for(var i=0;i<length; i++) {
            //             var pick_status = overview_pick_pick[i];
            //             var a = parseInt(pick_status.split("/")[0]);
            //             var b = parseInt(pick_status.split("/")[1]);
            //             var xValues = ["A", "Total"];
            //             var yValues = [a, b-a];
            //             var barColors = ["#2c577a", "#E0E0E0"];

            //             new Chart(pick_charts[i], { // Pick chart
            //             type: "doughnut",
            //             data: {
            //                 labels: xValues,
            //                 datasets: [{
            //                     label: '',
            //                 backgroundColor: barColors,
            //                 data: yValues
            //                 }]
            //             },
            //             options: {
            //                 animation: {
            //                     duration: 0
            //                 },
            //                 rotation: 1.5 * Math.PI,
            //                 // circumference: 1 * Math.PI,
            //                 legend: {
            //                     display: false
            //                 },
            //                 title: {
            //                     display: false,                    
            //                     text: "Dolly1"
            //                 },
            //                 responsive: true,
            //                 cutoutPercentage: 70,
            //                 "pick_text1": "PICK",
            //                 "pick_text2": pick_status,
            //             },                
            //             });
            //         }

            //         for(var i=0;i<length; i++) {
            //             var pick_status = overview_pick_cycle[i];
            //             var a = parseInt(pick_status.split("/")[0]);
            //             var b = parseInt(pick_status.split("/")[1]);
            //             var xValues = ["A", "Total"];
            //             var yValues = [a, b-a];
            //             var barColors = ["#2c577a", "#E0E0E0"];

            //             new Chart(cycle_charts[i], { // Cycle chart
            //             type: "doughnut",
            //             data: {
            //                 labels: xValues,
            //                 datasets: [{
            //                     label: 'abcde',
            //                 backgroundColor: barColors,
            //                 data: yValues
            //                 }]
            //             },
            //             options: {
            //                 responsive: true,
            //                 cutoutPercentage: 70,
            //                 animation: {
            //                     duration: 0
            //                 },
            //                 rotation: 1.5 * Math.PI,
            //                 // circumference: 1 * Math.PI,
            //                 legend: {
            //                     display: false
            //                 },
            //                 title: {
            //                     display: false,                    
            //                     text: "Dolly1"
            //                 },
            //                 "pick_text1": "CYCLE",
            //                 "pick_text2": pick_status,
            //             },                
            //             });
            //         }

            //     });
            // }

            // function draw_system_fill_chart(){
            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             action: "get_system_fill_percentage",
            //             // page: 'Stocking'
            //         },
            //         dataType: 'HTML',
            //     }).done(function(result) {
            //         var res = JSON.parse(result);
            //         var xValues = ["Filled", "Total"];
            //         var total = (res.total);
            //         var filled = (res.filled);                    
            //         var yValues = [filled, total-filled];
            //         var percentage = parseFloat(filled/total*100).toFixed(2);                    
            //         var filled_color = "";
            //         if(percentage <= 2)
            //             filled_color = "red";
            //         else if(percentage > 2 && percentage <= 5)
            //             filled_color = "green";
            //         else
            //             filled_color = "yellow";
            //         // var barColors = [filled_color, "#E0E0E0"];
            //         var barColors = ["gray", "#E0E0E0"];

            //         new Chart("System_Fill_Chart", {
            //         type: "doughnut",
            //         data: {
            //             labels: xValues,
            //             datasets: [{
            //                 label: 'SF',
            //             backgroundColor: barColors,
            //             data: yValues
            //             }]
            //         },
            //         options: {
            //             animation: {
            //                 duration: 0
            //             },
            //             rotation: 1 * Math.PI,
            //             circumference: 1 * Math.PI,
            //             legend: {
            //                 display: false
            //             },

            //             title: {
            //                 display: true,                    
            //                 // text: "System Fill",
            //                 position: 'bottom',
            //                 font: {
            //                     size: 24
            //                 },
            //             },

            //             "centertext": percentage.toString() + "%",
            //             "title_text": "SYSTEM FILL",
            //         },
            //         });
            //     });
            // }

            // function draw_part_stocking_chart(){
            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             action: "get_part_stocking_percentage",
            //             // page: 'Stocking'
            //         },
            //         dataType: 'HTML',
            //     }).done(function(result) {
            //         var res = JSON.parse(result);
            //         var xValues = ["Filled", "Total"];
            //         var total = (res.total);
            //         var filled = (res.filled);                    
            //         var yValues = [filled, total-filled];
            //         var percentage = parseFloat(filled/total*100).toFixed(2);                    
            //         var filled_color = "";
            //         if(percentage <= 2)
            //             filled_color = "red";
            //         else if(percentage > 2 && percentage <= 5)
            //             filled_color = "green";
            //         else
            //             filled_color = "yellow";
            //         // var barColors = [filled_color, "#E0E0E0"];
            //         var barColors = ["gray", "#E0E0E0"];

            //         new Chart("Part_Stocking_Chart", {
            //         type: "doughnut",
            //         data: {
            //             labels: xValues,
            //             datasets: [{
            //                 label: 'PS',
            //             backgroundColor: barColors,
            //             data: yValues
            //             }]
            //         },
            //         options: {
            //             animation: {
            //                 duration: 0
            //             },
            //             rotation: 1 * Math.PI,
            //             circumference: 1 * Math.PI,
            //             legend: {
            //                 display: false
            //             },
            //             title: {
            //                 display: true,                    
            //                 // text: "Part Stocking",
            //                 position: 'bottom',
            //             },
            //             "centertext": parseFloat(filled/total*100).toFixed(2).toString() + "%",
            //             "title_text": "PART STOCKING",
            //         },
            //         });
            //     });           

            // }

            // function draw_free_location_chart(){
            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             action: "get_free_location_percentage",
            //             // page: 'Stocking'
            //         },
            //         dataType: 'HTML',
            //     }).done(function(result) {
            //         var res = JSON.parse(result);
            //         var xValues = ["Filled", "Total"];
            //         var total = (res.total);
            //         var filled = (res.filled);                    
            //         var yValues = [filled, total-filled];
            //         var percentage = parseFloat(filled/total*100).toFixed(2);                    
            //         var filled_color = "";
            //         if(percentage <= 2)
            //             filled_color = "red";
            //         else if(percentage > 2 && percentage <= 5)
            //             filled_color = "green";
            //         else
            //             filled_color = "yellow";
            //         // var barColors = [filled_color, "#E0E0E0"];
            //         var barColors = ["gray", "#E0E0E0"];

            //         new Chart("Free_Location_Chart", {
            //         type: "doughnut",
            //         data: {
            //             labels: xValues,
            //             datasets: [{
            //                 label: 'FL',
            //             backgroundColor: barColors,
            //             data: yValues
            //             }]
            //         },
            //         options: {
            //             animation: {
            //                 duration: 0
            //             },
            //             rotation: 1 * Math.PI,
            //             circumference: 1 * Math.PI,
            //             legend: {
            //                 display: false
            //             },
            //             title: {
            //                 display: true,
            //                 // text: "Free Location",
            //                 position: 'bottom',
            //             },
            //             "centertext": parseFloat(filled/total*100).toFixed(2).toString() + "%",
            //             "title_text": "FREE LOCATION",
            //         },
            //         });
            //     });           

            // }                        

            $("#btn_load_data").on('click', function() {
                var date = $("#date").val();
                if (date == '') {
                    $("#date").focus();
                    return false;
                }
                read_container_devan_member_screen(date);
            });

            function read_stocking_overview() {
                // draw_system_fill_chart();
                // draw_part_stocking_chart();
                // draw_free_location_chart();
                // draw_part_chart();
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        'action': 'get_live_stocking_overview',
                        // 'date': date
                    },
                    dataType: 'HTML',
                }).done(function(response) {
                    var data = JSON.parse(response);
                    var users = data["sessions"];
                    var keys = Object.keys(users);
                    var member_list = [];
                    if (keys.length < 1)
                        return;
                    for (var i = 0; i < keys.length; i++) {
                        if (users[keys[i]].page != undefined && users[keys[i]].page.includes("stocking"))
                            member_list.push(users[keys[i]].user.username);
                    }
                    $("#stocking_users").html("<p> <b>" + member_list.join(", ") + "</b></p>");
                    if (data["stocking_alarm"]) {
                        $("#stocking_screen").attr("style", "background-color:red; padding:0");
                    } else {
                        $("#stocking_screen").attr("style", "background-color:white; padding:0");
                    }

                    // $("#stocking_screen").html(html);
                });
            }

            $(document).on('click', '#btn_chk_container_renban', function() {
                var expected_container_renban = $(this).val();
                var container_renban = $("#container_renban").val();
                if (container_renban == "") {
                    $("#container_renban").focus();
                    return false;
                }

                if (expected_container_renban.toLowerCase() == container_renban.toLowerCase()) {
                    $("#devan_screen").find('.row').css('background-color', 'green');
                    $("#renban_button").removeClass('btn-default');
                    $("#renban_button").addClass('btn-success');
                    $("#btn_complete").prop('disabled', false);
                    $("#btn_complete").attr('data-renban', 'revan');
                } else {
                    $("#container_renban").focus();
                    alert('Container Renban is wrong!');
                    $("#devan_screen").find('.row').css('background-color', 'red');
                    $("#renban_button").removeClass('btn-success');
                    $("#renban_button").addClass('btn-default');
                    $("#btn_complete").prop('disabled', true);
                    $("#btn_complete").attr('data-renban', 'check');
                }
            });

            $(document).on('click', '#btn_complete', function() {
                var row_id = $(this).val();
                var renban = $(this).attr('data-renban');
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        'action': 'complete_container_devan',
                        'row_id': row_id,
                        'renban': renban,
                    },
                }).done(function(result) {
                    if (renban == 'revan') {
                        if ($("#btn_chk_container_renban").data("revan") == 'scheduled') {
                            $("#btn_chk_container_renban").data("revan", "")
                            $("#btn_complete").attr('data-renban', 'confirm');
                            $("#container_renban").prop('readonly', true);
                            $("#btn_chk_container_renban").prop('disabled', true);
                            $("#revan_label").show();
                            $("#renban_no").text(result);
                        } else {
                            $("#btn_chk_container_renban").data("revan", "")
                            $("#btn_complete").attr('data-renban', 'confirm');
                            $("#container_renban").prop('readonly', true);
                            $("#btn_chk_container_renban").prop('disabled', true);
                            $("#renban_no").text(result);
                            $.ajax({
                                url: "actions.php",
                                method: "post",
                                data: {
                                    'action': 'complete_container_devan',
                                    'row_id': row_id,
                                    'renban': "confirm"
                                },
                            }).done(function(result) {
                                var date = $("#date").val();
                                $("#revan_label").hide();
                                read_container_devan_member_screen(date);
                            })
                        }
                    } else {
                        var date = $("#date").val();
                        $("#revan_label").hide();
                        read_container_devan_member_screen(date);
                    }

                });
            });

            /*
            Help Button
             */
            $(document).on('click', '.devan-help', function() {
                $(this).removeClass('bg-yellow');
                $(this).addClass('bg-red');
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "set_help_alarm",
                        page: 'Container Devan'
                    },
                    dataType: 'HTML',
                }).done(function(html) {
                    $("#help_modal").find('.modal-body').html(html);
                    $("#help_modal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                });

            });

            $("#confirm_help").on('click', function() {
                $("#confirm_help_alarm_id").val($("#help_alarm_id").val());
                $("#confirm_user_modal").modal({
                    backdrop: 'static',
                    keyboard: false
                })
            });

            $("#confirm_help_with_user").on('click', function() {
                if ($("#confirm_user_id").val() == '') {
                    $("#confirm_user_id").focus();
                    return false;
                }
                var confirm_user_id = $("#confirm_user_id").val();
                var alarm_id = $("#confirm_help_alarm_id").val();
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "confirm_help_alarm",
                        confirm_user_id: confirm_user_id,
                        alarm_id: alarm_id
                    },
                    dataType: 'HTML',
                }).done(function(html) {
                    $("#confirm_user_modal").modal('hide');
                    $(".devan-help").removeClass('bg-red');
                    $(".devan-help").addClass('bg-yellow');
                });
            });

            $("#incomplete_kanbans_modal_ok").on('click', function() {
                $("#andon_incomplete_kanbans_modal").modal('hide');
            });

            function get_last_upload_time() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_last_upload_time_team_leader",
                    },
                    dataType: 'HTML',
                }).done(function(response) {
                    var data = JSON.parse(response);
                    $("#upload_time_devan_schedule").text(data.devan_schedule);
                    $("#upload_time_pick_del_plan").text(data.pick_del_plan);
                    $("#upload_time_master_kanban").text(data.pick_del_plan);
                });
            }

            $("#file_devan_schedule").on('change', function() {
                $("#loading_window").modal();
                var file_data = $(this).prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                $.ajax({
                    url: 'container_devan_import.php',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(result) {
                        $("#loading_window").modal('hide');
                        if (result == "Success") {
                            alert('Imported successfully');
                            get_last_upload_time();
                        } else {
                            alert('Import failed');
                        }
                        $(this).val("");
                    }
                });
            });

            $("#file_pick_del_plan").on('change', function() {
                $("#loading_window").modal();
                var file_data = $(this).prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                $.ajax({
                    url: 'conveyance_pick_import.php',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(result) {
                        $("#loading_window").modal('hide');
                        if (result == "Success") {
                            alert('Imported successfully');
                            get_last_upload_time();
                        } else {
                            alert('Import failed');
                        }
                        $(this).val("");
                    }
                });
            });

            $("#file_master_kanban").on('change', function() {
                $("#loading_window").modal();
                var file_data = $(this).prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                form_data.append('target', 'part2kanban');
                $.ajax({
                    url: 'import_csv.php',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(result) {
                        $("#loading_window").modal("hide");
                        console.log(result);
                        if (result == "Success") {
                            alert('Imported successfully');
                            get_last_upload_time();
                        } else {
                            alert('Import failed');
                        }
                        $(this).val("");
                    }
                });
            });



            function get_opr_settings() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        'action': 'get_opr_settings',
                    },
                }).done(function(response) {
                    var data = JSON.parse(response);
                    $("#opr_cycle_settings").val(data.opr_cycle_settings);
                    $("#opr_pick_settings").val(data.opr_pick_settings);
                    $("#opr_del_settings").val(data.opr_del_settings);
                });
            }



            function get_low_stocks() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        'action': 'get_low_stocks',
                    },
                }).done(function(response) {
                    var data = JSON.parse(response).low_stocks;
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        if (row.count == undefined)
                            row.count = 0;
                        // console.log("DDDDD:", row.count, row.level_medium, row.level_low);
                        row.count = parseInt(row.count);
                        row.level_medium = parseInt(row.level_medium);
                        row.level_low = parseInt(row.level_low);
                        if (row.count <= row.level_medium) {
                            if (row.count <= row.level_low) { //medium, yellow
                                html += `<div class="text-center bg-danger p-1 m-1" style="width: 4vw; height: 4vw;">
                                        <p class="h3 font-weight-bold">${row.part}</p>
                                        <p class="h4 font-weight-bold">${row.count}</p>
                                        </div>`;
                            } else { // low, red
                                html += `<div class="text-center bg-yellow p-1 m-1" style="width: 4vw; height: 4vw;">
                                        <p class="h3 font-weight-bold">${row.part}</p>
                                        <p class="h4 font-weight-bold">${row.count}</p>
                                        </div>`;
                            }
                        }
                    }
                    $("#low_stocks").html(html);
                });
            }


            function get_active_members() {
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                const formattedToday = yyyy + '-' + mm + '-' + dd;

                const yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);
                const yyyy_yt = yesterday.getFullYear();
                let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
                let dd_yt = yesterday.getDate();
                const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt;

                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'get_all_session_json',
                        today: formattedToday,
                        yesterday: formattedYesterday
                    },
                    dataType: 'JSON',
                }).done(function(result) {
                    var tmpUsers = result.users;
                    var keys = Object.keys(tmpUsers);
                    var length = keys.length;
                    var pick_users = [];
                    var del_users = [];
                    var stocking_users = [];
                    var devan_users = [];
                    var driver_users = [];
                    for (var i = 0; i < length; i++) { // extracts session logged in page from all sessions                        
                        var key = keys[i];
                        var user = tmpUsers[key];
                        user['key'] = key;
                        if (user.user != undefined && user.page == "conveyance_pick.php") {
                            var allow_push = 1;
                            for (var j = 0; j < pick_users.length; j++)
                                if (pick_users[j].user.username == user.user.username) //check duplicated user
                                    allow_push = 0;
                            if (allow_push == 1)
                                pick_users.push(user);
                        } else if (user.user != undefined && user.page == "conveyance_delivery.php") {
                            var allow_push = 1;
                            for (var j = 0; j < del_users.length; j++)
                                if (del_users[j].user.username == user.user.username) //check duplicated user
                                    allow_push = 0;
                            if (allow_push == 1)
                                del_users.push(user);
                        } else if (user.user != undefined && user.page.includes("stocking")) {
                            var allow_push = 1;
                            for (var j = 0; j < stocking_users.length; j++)
                                if (stocking_users[j].user.username == user.user.username) //check duplicated user
                                    allow_push = 0;
                            if (allow_push == 1)
                                stocking_users.push(user);
                        } else if (user.user != undefined && user.page.includes("devan")) {
                            var allow_push = 1;
                            for (var j = 0; j < devan_users.length; j++)
                                if (devan_users[j].user.username == user.user.username) //check duplicated user
                                    allow_push = 0;
                            if (allow_push == 1)
                                devan_users.push(user);
                        } else if (user.user != undefined && user.page.includes("driver")) {
                            var allow_push = 1;
                            for (var j = 0; j < driver_users.length; j++)
                                if (driver_users[j].user.username == user.user.username) //check duplicated user
                                    allow_push = 0;
                            if (allow_push == 1)
                                driver_users.push(user);
                        }
                    }

                    // console.log("FFFFFFF: ", pick_users);
                    var pick_html = '<p class="h5 font-weight-bold text-gray">PICK</p>';
                    for (var i = 0; i < pick_users.length; i++) {
                        pick_html += `<div class="row">
                                            <p class="h6 font-weight-bold mx-2">${pick_users[i].user.username}</p> <button type="button" class="btn btn-danger close_user" style="width:20px; height:20px" value="${pick_users[i].key}"><p style="margin-left:-5px; margin-top:-7px">X</p></button>
                                        </div>`
                    }
                    $("#pick_members").html(pick_html);

                    var del_html = '<p class="h5 font-weight-bold text-gray">DELIVERY</p>';
                    for (var i = 0; i < del_users.length; i++) {
                        del_html += `<div class="row">
                                            <p class="h6 font-weight-bold mx-2">${del_users[i].user.username}</p> <button type="button" class="btn btn-danger close_user" style="width:20px; height:20px" value="${del_users[i].key}"><p style="margin-left:-5px; margin-top:-7px">X</p></button>
                                        </div>`
                    }
                    $("#del_members").html(del_html);

                    var stocking_html = '<p class="h5 font-weight-bold text-gray">STOCKING</p>';
                    for (var i = 0; i < stocking_users.length; i++) {
                        stocking_html += `<div class="row">
                                            <p class="h6 font-weight-bold mx-2">${stocking_users[i].user.username}</p> <button type="button" class="btn btn-danger close_user" style="width:20px; height:20px" value="${stocking_users[i].key}"><p style="margin-left:-5px; margin-top:-7px">X</p></button>
                                        </div>`
                    }
                    $("#stocking_members").html(stocking_html);

                    var devan_html = '<p class="h5 font-weight-bold text-gray">DEVAN</p>';
                    for (var i = 0; i < devan_users.length; i++) {
                        devan_html += `<div class="row">
                                            <p class="h6 font-weight-bold mx-2">${devan_users[i].user.username}</p> <button type="button" class="btn btn-danger close_user" style="width:20px; height:20px" value="${devan_users[i].key}"><p style="margin-left:-5px; margin-top:-7px">X</p></button>
                                        </div>`
                    }
                    $("#devan_members").html(devan_html);

                    var driver_html = '<p class="h5 font-weight-bold text-gray">DRIVER</p>';
                    for (var i = 0; i < driver_users.length; i++) {
                        driver_html += `<div class="row">
                                            <p class="h6 font-weight-bold mx-2">${driver_users[i].user.username}</p> <button type="button" class="btn btn-danger close_user" style="width:20px; height:20px" value="${driver_users[i].key}"><p style="margin-left:-5px; margin-top:-7px">X</p></button>
                                        </div>`
                    }
                    $("#driver_members").html(driver_html);

                });
            }

            $(document).on('click', '.close_user', function() {
                // alert($(this).val());
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'close_session',
                        session_name: $(this).val()
                    },
                    dataType: 'JSON',
                }).done(function(result) {

                });



            });

        });
    </script>
</body>

</html>