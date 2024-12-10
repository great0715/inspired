<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Part Delivery Overview";
// $_SESSION['page'] = 'part_delivery_driver.php';
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

    @keyframes red_flash_fake {
        from {
            background-color: red;
        }

        to {
            background-color: red;
        }
    }

    @keyframes red_flash_real {
        from {
            background-color: red;
        }

        to {
            background-color: white;
        }
    }

    .content{
        padding-top:3% !important;
    }
</style>

<body class="hold-transition sidebar-collapse layout-top-nav" onload="startTime()">
    <div class="wrapper">
        <?php include("header.php"); ?>
        <?php include("menu.php"); ?>
        <div class="content-wrapper">
            <!-- <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <h1 class="m-0" style="display: inline"><?php echo $page_name; ?></h1>
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-default" style="width: 250px;" id="renban_button">Revan</button>
                        </div>
                        <div class="col-sm-5" style="text-align: right;">
                            <button class="btn btn-default" style="min-width: 200px;"><?php echo $_SESSION['user']['username'] ?></button>
                            <a href="logout.php" class="btn btn-default" style="width: 150px;">LOGOUT</a>
                            <input class="form-control" type="text" id="date" name="date" style="display: inline-block; width: 200px;" value="<?php echo date('d/m/Y'); ?>">
                            <button class="btn btn-success" id="btn_load_data">Load Data</button>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12" id="revan_label" style="background-color: black; color: yellow; display: none;">
                            <h2 style="text-align: center; padding: 15px;font-size: 3em;">REVAN</h2>
                        </div>
                        <div class="col-md-6 d-none">
                            <div class="card card-primary">
                                <div class="card-header" style="background-color: #377184;">
                                    <h3 class="card-title">PICK</h3>
                                </div>
                                <div class="card-body" id="pick_screen" style="padding: 0">
                                    <div class="row border mx-2 my-2 justify-content-center" style="border-radius: 15px;" id="pick_cycle_graph">
                                    </div>
                                    <div class="border mx-2 my-2" style="justify-content: space-between; border-radius: 15px">
                                        <div class="row justify-content-center my-2">
                                            <p class="h6">LOCATION / PICK</p>
                                        </div>
                                        <div class="row mx-2 my-2 justify-content-center" id="location_pick">
                                        </div>
                                    </div>
                                    <div class="row border mx-2 my-2" style="border-radius: 15px;">
                                        <div class="col-md-3 border-right text-center my-2 py-2">
                                            <h6 class="font-weight-bold">REMAINING CYCLES FROM LAST SHIFT</h6>
                                            <button type="button" class="btn btn-success rounded-circle mx-auto mt-3" style="width:6.5vw; height: 6.5vw;" id="remaining_cycles_pick">
                                                <p class="h1">0</p>
                                            </button>
                                        </div>
                                        <div class="col-md-5 border-right mx-0 px-0 text-center mt-2">
                                            <h6 class="font-weight-bold">INCOMPLETE KANBANS</h6>
                                            <div class="row" id="incomplete_kanbans">
                                            </div>
                                        </div>
                                        <div class="col-md-4 mt-2 text-center">
                                            <p class="h6 text-center font-weight-bold">ANDONS</p>
                                            <canvas id="Pick_Reason_Chart" style="width:100%;max-width:600px"></canvas>
                                            <p class="h6">REASONS</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div id="delivery_screen" style="width: 100vw">
                                    <div class="border mx-2 my-2" style="border-radius: 15px;">
                                        <div class="row mx-2 my-2 justify-content-center" id="delivery_svg">
                                        </div>
                                        <div class="mx-2 my-2" style="justify-content: space-between;">
                                            <div class="row justify-content-center my-2">
                                                <p class="h6">LOCATION / DELIVERY</p>
                                            </div>
                                            <div class="row mx-2 my-2 justify-content-center" id="location_delivery">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border mx-2 p-2 my-3" style="border-radius: 15px;" id="driver_member_screen">

                                        <div class="col-md-12">
                                            <div class="text-center font-weight-bold">DRIVER / LOCATION</div>
                                            <div class="row mx-2 my-2 justify-content-center" style="border-radius: 15px;">
                                                <div class="row justify-content-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1500" height="200">
                                                        <g transform="matrix(1.3333334 0 0 1.3333334 0 0)">
                                                            <text x="165" y="17" fill="black" font-size="14px" font-weight="bold" id="member_name"></text>
                                                            <text x="225" y="20" fill="WHITE" font-size="24px" font-weight="bold" id="member_status"></text>

                                                            <line x1="75" y1="65" x2="325" y2="65" style="stroke:gray;stroke-width:5" id="line1" />
                                                            <circle id="circle1" cx="50" cy="65" r="20" stroke="black" stroke-width="0" fill="gray" />
                                                            <text x="0" y="105" fill="gray" font-size="14px" font-weight="bold" id="zone1">START / ZONE 1</text>
                                                            <text x="10" y="125" fill="gray" font-size="14px" font-weight="bold" id="zone1_time"></text>
                                                            <text x="10" y="145" fill="gray" font-size="14px" font-weight="bold" id="c_b_1"></text>

                                                            <line x1="375" y1="65" x2="625" y2="65" style="stroke:gray;stroke-width:5" id="line2" />
                                                            <circle id="circle2" cx="350" cy="65" r="20" stroke="black" stroke-width="0" fill="gray" />
                                                            <text x="325" y="105" fill="gray" font-size="14px" font-weight="bold" id="zone2">ZONE 2</text>
                                                            <text x="325" y="125" fill="gray" font-size="14px" font-weight="bold" id="zone2_time">ETA: </text>
                                                            <text x="315" y="145" fill="gray" font-size="14px" font-weight="bold" id="c_b_2"></text>

                                                            <line x1="675" y1="65" x2="925" y2="65" style="stroke:gray;stroke-width:5" id="line3" />
                                                            <circle id="circle3" cx="650" cy="65" r="20" stroke="black" stroke-width="0" fill="gray" />
                                                            <text x="625" y="105" fill="gray" font-size="14px" font-weight="bold" id="zone3">ZONE 3</text>
                                                            <text x="625" y="125" fill="gray" font-size="14px" font-weight="bold" id="zone3_time">ETA: </text>
                                                            <text x="615" y="145" fill="gray" font-size="14px" font-weight="bold" id="c_b_3"></text>

                                                            <circle id="circle4" cx="950" cy="65" r="20" stroke="black" stroke-width="0" fill="gray" />
                                                            <text x="895" y="105" fill="gray" font-size="14px" font-weight="bold" id="zone4">ZONE 4 / FINISH</text>
                                                            <text x="900" y="125" fill="gray" font-size="14px" font-weight="bold" id="zone4_time">ETA: </text>
                                                            <text x="905" y="145" fill="gray" font-size="14px" font-weight="bold" id="c_b_4"></text>

                                                            <image id="truck" x="22" y="37" xlink:href="data:image/png;base64,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" preserveAspectRatio="none" width="52" height="25" />
                                                        </g>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center" style="margin-top: 150px;">
                                            <div id="build_cycle_area" class="col-md-2 border mx-0 p-2 text-center" style="margin-bottom: -20px; border-radius: 15px; background-color:white">
                                                <p class="h3 font-weight-bold" style="margin-top: -40px; min-height: 40px;" id="paused_time"></p>
                                                <p class="h6 font-weight-bold">BUILD/CYCLE</p>
                                                <p style="color:black; font-size: 70px; font-weight: bold; margin-top: 0px; margin-bottom: 5px;" id="build_cycle_count"></p>
                                                <p style="color:black; font-size: 24px; font-weight: bold; margin-bottom: 0px" id="build_cycle_cycle"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 border-right text-center my-2 d-none">
                                            <h6 class="font-weight-bold">REMAINING CYCLES FROM LAST SHIFT</h6>
                                            <button type="button" class="btn bg-success rounded-circle mx-5 mt-3" style="width:5vw; height: 5vw;" id="remaining_cycles_delivery">
                                                <h1>0</h1>
                                            </button>
                                        </div>
                                        <div class="col-md-4 mt-2 text-center d-none">
                                            <p class="h6 text-center font-weight-bold">ANDONS</p>
                                            <canvas id="Delivery_Reason_Chart" style="width:100%;max-width:600px"></canvas>
                                            <p class="h6">REASONS</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" style="margin-left: 0px !important; margin-right: 0px !important;">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header" style="background-color: #377184;">
                                    <h3 class="card-title">DEVAN</h3>
                                </div>
                                <div class="card-body" id="devan_screen" style="padding: 0">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header" style="background-color: #377184;">
                                    <h3 class="card-title">STOCKING</h3>
                                </div>
                                <div class="card-body" id="stocking_screen" style="padding: 0;">
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
                                    <div class="row border mx-2 my-2" style="justify-content: space-between; border-radius: 15px;">
                                        <!-- <div style="rotate: -90deg; width:80px; height:30px;">Amount</div> -->
                                        <div class="col-md-12">
                                            <canvas id="Part_Chart" style="width:100%;max-width:600px; max-height:200px"></canvas>
                                        </div>
                                        <!-- <p>Parts</p> -->
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

    <div class="modal fade" id="help_modal">
        <div class="modal-dialog">
            <div class="modal-content bg-danger">
                <div class="modal-header">
                    <h4 class="modal-title">ANDON HELP</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer justify-content-center">
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

    <div class="modal fade" id="confirm_user_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select User</h4>
                </div>
                <div class="modal-body">
                    <?php
                    $users = get_all_users();
                    echo '<select class="form-control" id="confirm_user_id" name="confirm_user_id">';
                    echo '<option></option>';
                    foreach ($users as $user) {
                        if ($user['type'] == 1)
                            echo '<option value="' . $user['user_id'] . '">' . $user['username'] . '</option>';
                    }
                    echo '</select>';
                    ?>
                    <input type="hidden" id="confirm_help_alarm_id">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="confirm_help_with_user" style="width: 160px;">OK</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <input type="hidden" id="part_setting_value" value="0">
    <input type="hidden" id="cycle_setting_value" value="0">


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
        $(document).ready(function() {
            $("#date").datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                    previous: 'fas fa-angle-left',
                    next: 'fas fa-angle-right',
                }
            });

            $("#renban_button").on('click', function() {
                $("#container_number_modal").modal();
            });

            // $(document).on('click', '#enter_container_number', function() {                
            //     var container_number = $("#container_number").val();
            //     if (container_number == '')
            //         return false;
            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             'action': 'update_revan_state',
            //             'container_number': container_number,
            //         },
            //     }).done(function(result) {
            //         if (result != 'Failed') {
            //             $("#container_number").val('');
            //             console.log(result);
            //         }
            //     });

            // });

            get_overview_realtime();
            setInterval(get_overview_realtime, 3 * 1000); //update every 30 seconds

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
                        var fontSize = (height / 80 / 2).toFixed(2); // was: 114
                        ctx.font = fontSize + "em sans-serif";
                        // ctx.fontSize = 100;
                        ctx.textBaseline = "middle";

                        var text = chart.options.title_text, // "75%",
                            textX = Math.round((width - ctx.measureText(text).width) / 2),
                            textY = height / 2 + (chart.titleBlock.height) + 30;

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
                console.log("get_overview_realtime");
                // console.log("calling get_overview_realtime every 10 seconds");
                // read_container_devan_member_screen('today');
                // read_stocking_overview();
                // read_remaining_incomplete();
                read_remaining_build_delivery_driver();
                // draw_pick_cycle_chart();
                draw_delivery_cycle_chart();
                // draw_delivery_reason_chart();
                // draw_pick_reason_chart();                
            }


            function read_remaining_incomplete() { // Get remaining cycles, incomplete kanbans
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
                        action: 'get_remaining_incomplete_pick',
                        today: formattedToday,
                        yesterday: formattedYesterday
                    },
                    dataType: 'JSON',
                }).done(function(response) {

                    var remaining_cycles = parseInt(response.remaining_cycles);
                    var incomplete_cycles = response.incomplete_cycle;
                    var incomplete_kanbans = response.incomplete_kanban;
                    var max_cycle = parseInt(response.max_cycle);
                    var length = incomplete_kanbans.length;
                    if (remaining_cycles > 0) {
                        $("#remaining_cycles_pick").removeClass("btn-success");
                        $("#remaining_cycles_pick").addClass("btn-danger");
                    }

                    $("#remaining_cycles_pick").html(`<p style="margin-top:-12px; font-size:5vw">${remaining_cycles}</p>`);

                    var html = '<div class="col-md-4"><h6 style="color:#2c577a"><b>KANBAN</b></h6>';
                    var kanban_html = '';
                    var cycle_html = '';
                    var count_html = '';

                    for (var i = 0; i < length; i++) {
                        kanban_html += `<p class="h5" style="color:red">${incomplete_kanbans[i]}</p>`
                        cycle_html += `<p class="h5" style="color:red">${incomplete_cycles[i]}/${max_cycle}</p>`
                    }
                    html += kanban_html + '</div>';
                    html += '<div class="col-md-4"><h6 style="color:#2c577a"><b>CYCLE</b></h6>';
                    html += cycle_html + '</div>';
                    html += `<div class="col-md-4 my-auto mx-0">
                                <button type="button" class="btn `;
                    if (length > 0)
                        html += `btn-danger`;
                    else
                        html += `btn-success`;
                    html += ` rounded-circle" style="width:6vw; height: 6vw; margin-left:-10px" id="incomplete_kanban_count"><p style="margin-top:-7px; font-size:4vw">${length}</p></button>
                            </div>`;
                    $("#incomplete_kanbans").html(html);
                });
            }

            function read_remaining_build_delivery_driver() { // Get remaining cycles, build/cycle,
                // console.log('read_remaining_build_delivery_driver()');
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
                    // console.log("read_remaining_build_delivery_driver", response);
                    var remaining_cycles = parseInt(response.remaining_cycles);
                    if (remaining_cycles == null)
                        remaining_cycles = 0;
                    var count = response.count;
                    var cycle = response.cycle;
                    var cycle_str = "CYCLE " + Math.ceil(count / cycle);

                    $("#part_setting_value").val(response.driver);
                    $("#cycle_setting_value").val(response.cycle);

                    $("#build_cycle_count").text(count);
                    $("#build_cycle_cycle").text(cycle_str);

                    // var html = `<p class="h1">${remaining_cycles}</p>`;
                    var html = `<p class="h1">${remaining_cycles}</p>`;
                    if (remaining_cycles > 0)
                        $("#remaining_cycles_delivery").addClass("bg-danger");
                    else
                        $("#remaining_cycles_delivery").addClass("bg-success");
                    $("#remaining_cycles_delivery").html(html);
                });
            }

            get_driver_member_data();

            function get_driver_member_data() {
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

                // console.log("get_driver_member_data");
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
                    var users = [];
                    for (var i = 0; i < length; i++) { // extracts session logged in driver input page from all sessions
                        var key = keys[i];
                        var user = tmpUsers[key];
                        if (user.page == "part_delivery_input.php" && user.user != undefined) {
                            var allow_push = 1;
                            for (var j = 0; j < users.length; j++)
                                if (users[j].user.username == user.user.username)
                                    allow_push = 0;
                            if (allow_push == 1)
                                users.push(user);
                        }
                    }
                    if (users.length < 1)
                        return;
                    var user = users[0];

                    $("#member_name").text(user.user.username);
                    if (user.driver_zone_red_status != undefined)
                        $("#member_status").text(`/${user.driver_zone_red_status}`);
                    $("#paused_time").text(user.driver_zone_paused_time);



                    var count = user.base_target;
                    if (count != '') {
                        count = parseInt(count);
                        var driver_part = parseInt($("#part_setting_value").val());
                        var cycle = parseInt($("#cycle_setting_value").val());
                        var cycle1 = Math.ceil(count / cycle);
                        // console.log("SSSSSSS: ", user);//count, cycle, driver_part);
                        // if($("#c_b_1").text() == '') 
                        {
                            $("#c_b_1").text(`C:${cycle1} / B:${count}`);
                            $("#c_b_2").text(`C:${cycle1} / B:${count + driver_part}`);
                            $("#c_b_3").text(`C:${cycle1} / B:${count + driver_part * 2}`);
                            $("#c_b_4").text(`C:${cycle1} / B:${count + + driver_part * 3}`);
                        }
                    }


                    for (var i = 1; i <= 4; i++) {
                        $(`#zone${i}`).attr("fill", "gray");
                        $(`#zone${i}_time`).attr("fill", "gray");
                        $(`#c_b_${i}`).attr("fill", "gray");
                        $(`#circle${i}`).attr("fill", "gray");
                        if (i < 4)
                            // $(`#line${i}`).attr("style", "stroke:#027847;stroke-width:5");
                            $(`#line${i}`).attr("style", "stroke:gray;stroke-width:5");
                    }
                    $(`#zone${user.driver_current_zone}_time`).text(user.driver_zone_time);

                    for (var i = 1; i <= user.driver_current_zone; i++) {
                        $(`#zone${i}`).attr("fill", "green");
                        $(`#zone${i}_time`).attr("fill", "green");
                        $(`#c_b_${i}`).attr("fill", "green");
                        $(`#circle${i}`).attr("fill", "green");
                        $(`#line${i}`).attr("style", "stroke:#027847;stroke-width:5");
                    }

                    if (user.is_initial != "0") { //if not zone4 & not initial
                        for (var i = 1; i <= 4; i++) {
                            $(`#zone${i}`).attr("fill", "gray");
                            // if(i > 1) 
                            {
                                $(`#zone${i}_time`).text("ETA: ");
                            }
                            $(`#zone${i}_time`).attr("fill", "gray");
                            $(`#c_b_${i}`).attr("fill", "gray");
                            $(`#circle${i}`).attr("fill", "gray");
                            if (i < 4)
                                $(`#line${i}`).attr("style", "stroke:gray;stroke-width:5");
                        }
                        $("#truck").attr("x", 22);
                        $("#member_name").attr('x', 20 + 300 * (user.driver_current_zone - 1));
                        $("#member_status").attr('x', 75 + 300 * (user.driver_current_zone - 1));
                    } else { //if zone4 or initial
                        $("#truck").attr('x', 170 + 300 * (user.driver_current_zone - 1));
                        $("#member_name").attr('x', 165 + 300 * (user.driver_current_zone - 1));
                        $("#member_status").attr('x', 225 + 300 * (user.driver_current_zone - 1));
                    }

                    for (var i = 1; i <= 4; i++) {
                        var zone_time_text = $(`#zone${i}_time`).text();
                        if (zone_time_text.toUpperCase().includes("ETA:")) {
                            var xPos = $(`#c_b_${i}`).attr("x");
                            $(`#zone${i}_time`).attr("x", xPos - 40);
                            var yPos = $(`#c_b_${i}`).attr("y");
                            $(`#zone${i}_time`).attr("y", yPos);
                            // $(`#zone${i}_time`).text("ETA: ");
                        } else {
                            var xPos = $(`#c_b_${i}`).attr("x");
                            $(`#zone${i}_time`).attr("x", xPos);
                            var yPos = $(`#c_b_${i}`).attr("y");
                            $(`#zone${i}_time`).attr("y", yPos - 20)
                        }
                    }

                    if (user.driver_zone_background != 'red') {
                        $("#driver_member_screen").attr('style', 'background-color: white;');
                        $("#build_cycle_area").attr('style', 'margin-bottom:-20px; border-radius:15px; background-color: white;');
                    } else {
                        if (user.driver_zone_red_status != "HELP ANDON") {
                            $("#driver_member_screen").attr('style', 'animation: red_flash_fake 2s infinite');
                            $("#build_cycle_area").attr('style', 'margin-bottom:-20px; border-radius:15px; animation: red_flash_fake 2s infinite');
                        } else {
                            $("#driver_member_screen").attr('style', 'animation: red_flash_real 2s infinite');
                            $("#build_cycle_area").attr('style', 'margin-bottom:-20px; border-radius:15px; animation: red_flash_real 2s infinite');
                        }
                    }
                    // if(user.driver_current_zone == 1) {

                    // }


                    // const today = new Date();
                    // const yyyy = today.getFullYear();
                    // let mm = today.getMonth() + 1; // Months start at 0!
                    // let dd = today.getDate();
                    // const formattedToday = yyyy + '-' + mm + '-' + dd;

                    // const yesterday = new Date();
                    // yesterday.setDate(today.getDate() - 1);
                    // const yyyy_yt = yesterday.getFullYear();
                    // let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
                    // let dd_yt = yesterday.getDate();
                    // const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt;

                    // $.ajax({
                    //     url: "actions.php",
                    //     method: "post",
                    //     data: {
                    //         action: 'get_remaining_build_delivery',
                    //         today: formattedToday,
                    //         yesterday: formattedYesterday
                    //     },
                    //     dataType: 'JSON',
                    // }).done(function(response) {
                    //     console.log("111111111: ", response);
                    //     var count = response.base_target;
                    //     if(count != '') {
                    //         var driver_part = response.driver;
                    //         var cycle = response.cycle;
                    //         var cycle1 = Math.ceil(count / cycle);
                    //         if($("#c_b_1").text() == '') {
                    //             $("#c_b_1").text(`C:${cycle1} / B:${count}`);
                    //             $("#c_b_2").text(`C:${cycle1} / B:${count + driver_part}`);
                    //             $("#c_b_3").text(`C:${cycle1} / B:${count + driver_part * 2}`);
                    //             $("#c_b_4").text(`C:${cycle1} / B:${count + + driver_part * 3}`);
                    //         }
                    //     }
                    // });

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
                    if (length > 4)
                        length = 4;
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


            setInterval(get_driver_member_data, 3 * 1000); // every 3seconds

        });
    </script>
</body>

</html>