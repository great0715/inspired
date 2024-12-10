<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Overview Screen";
$_SESSION['page'] = 'overview_screen.php';
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
                    <div class="p-2 border m-2" id="pick_screen" style="border-radius:15px;">
                        <div class="row justify-content-center">
                            <h3 class="font-weight-bold">PICK</h3>
                        </div>
                        <div class="row mx-2 my-2 justify-content-center" style="border-radius: 15px;" id="pick_cycle_graph">
                        </div>
                        <div class="row mx-2 my-2 justify-content-center" id="location_pick">
                        </div>


                        <div class="row border mx-2 my-2 d-none" style="border-radius: 15px;">
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


                    <div class="row">
                        <div class="col-md-12" id="revan_label" style="background-color: black; color: yellow; display: none;">
                            <h2 style="text-align: center; padding: 15px;font-size: 3em;">REVAN</h2>
                        </div>
                        <div class="col-md-6">

                            <div class="card card-primary">


                            </div>
                        </div>
                        <div class="col-md-6 d-none">
                            <div class="card card-primary">
                                <div class="card-header" style="background-color: #377184;">
                                    <h3 class="card-title">DELIVERY</h3>
                                </div>
                                <div class="card-body" id="delivery_screen" style="padding: 0">
                                    <div class="row border mx-2 my-2 justify-content-center" id="delivery_svg" style="border-radius: 15px;">

                                    </div>
                                    <div class="border mx-2 my-2" style="justify-content: space-between; border-radius: 15px">
                                        <div class="row justify-content-center my-2">
                                            <p class="h6">LOCATION / DELIVERY</p>
                                        </div>
                                        <div class="row mx-2 my-2 justify-content-center" id="location_delivery">
                                        </div>
                                    </div>
                                    <div class="row border mx-2 my-2" style="border-radius: 15px;">
                                        <div class="col-md-3 border-right text-center my-2">
                                            <h6 class="font-weight-bold">REMAINING CYCLES FROM LAST SHIFT</h6>
                                            <button type="button" class="btn bg-success rounded-circle mx-5 mt-3" style="width:5vw; height: 5vw;" id="remaining_cycles_delivery">
                                                <h1>0</h1>
                                            </button>
                                        </div>
                                        <div class="col-md-5 border-right mx-0 px-0 text-center my-2">
                                            <p class="h6 font-weight-bold">BUILD/CYCLE</p>
                                            <p style="color:green; font-size: 70px; font-weight: bold;" id="build_cycle_count"></p>
                                            <p style="color:#2c577a; font-size: 24px; font-weight: bold;" id="build_cycle_cycle"></p>
                                        </div>
                                        <div class="col-md-4 mt-2 text-center">
                                            <p class="h6 text-center font-weight-bold">ANDONS</p>
                                            <canvas id="Delivery_Reason_Chart" style="width:100%;max-width:600px"></canvas>
                                            <p class="h6">REASONS</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left: 0px !important; margin-right: 0px !important;">
                        <div class="col-md-7 border rounded row space-x-4 justify-center uppercase " id="areas">



                        </div>
                        <div class="col-md-5">
                            <div class="row border mx-2 my-2" style="justify-content: space-between; border-radius: 15px;">
                                <div class='col-md-9 row' id="stock_rectangle_chart">
                                </div>
                                <div class='col-md-3 row'>
                                    <div class="col-md-12">
                                        <canvas id="System_Fill_Chart" style="width:100%;max-width:600px; margin-top:-25px; "></canvas>
                                    </div>
                                    <div class="col-md-12">
                                        <canvas id="Part_Stocking_Chart" style="width:100%;max-width:600px; margin-top:-25px; "></canvas>
                                    </div>
                                    <div class="col-md-12">
                                        <canvas id="Free_Location_Chart" style="width:100%;max-width:600px; margin-top:-25px;"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-3 d-none">
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

                            <div class="border p-2 mx-2" id="devan_screen" style="border-radius: 15px;">
                            </div>


                            <div class="card card-primary d-none1">
                                <div class="card-body" id="stocking_screen" style="padding: 0;">


                                    <div class="row border mx-2 my-2 d-none" style="justify-content: space-between; border-radius: 15px;">
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
            setInterval(get_overview_realtime, 3 * 1000); //update every 30 seconds
            get_area_info();
            setInterval(get_area_info, 10000); //update every 10 seconds

            function get_area_info() {
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
                        yesterday: formattedYesterday,
                    },
                    dataType: 'JSON',
                }).done(function(result) {
                    // console.log("SSSSSSSSSS: ", result);
                    // result = JSON.parse(result)
                    var {
                        incomplete_pick_count,
                        incomplete_delivery_count,
                        incomplete_stocking_count,
                        incomplete_devan_count,
                        incomplete_driver_count,
                    } = result
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
                    // console.log(pick_users)pick_users = [];
                    var count_list = [incomplete_devan_count, incomplete_stocking_count, incomplete_pick_count, incomplete_delivery_count, incomplete_driver_count]
                    var users = [devan_users, stocking_users, pick_users, del_users, driver_users]
                    // console.log('=', users)
                    var titles = ['devan', 'stocking', 'pick', 'delivery', 'driver']
                    var root_html = ``;
                    for (var i = 0; i < 5; i++) {
                        // console.log(users[i].users)
                        const members = (users[i].length > 0) ? users[i] : []; //[i].user.username
                        // console.log(members)
                        var users_list_html = ``;
                        for (var j = 0; j < members.length; j++) {
                            users_list_html += `<div>${members[j].user.username}</div>`
                        }
                        var color = (count_list[i] == 0) ? 'bg-green-500' : 'bg-red-500';
                        var status = (count_list[i] == 0) ? 'good' : 'bad';
                        var emoji = (count_list[i] == 0) ? 'smile' : 'sad';
                        var html = `
                            <div class=" text-white my-12">
                                <div class="h-100 w-36 xl:w-48 ${color} text-center flex-col flex justify-between py-12 relative" style="border-radius:25px;">
                                <div class="absolute flex justify-center items-center left-1/2 translate-x-[-50%]  top-0 w-20 h-20 border-4 rounded-full translate-y-[-50%] ${color}">
                                    <div class="text-6xl font-bold">${count_list[i]}</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-semibold">${titles[i]}</div>
                                    <div class="text-xs">Members</div>
                                    <div class="text-2xl font-semibold text-black  text-gray-700">${users_list_html}</div>
                                </div>
                                <div class='overflow-hidden'>
                                    <div>condition</div>
                                    <div class="text-black text-4xl font-bold">${status}</div>
                                </div>
                                <div class="absolute flex justify-center items-center left-1/2 translate-x-[-50%]  bottom-0 w-20 h-20 border-4 rounded-full translate-y-[50%] bg-[url('assets/img/${emoji}.png')] bg-contain">
                                </div>
                                </div>
                            </div>
                        `
                        root_html += html;
                    }
                    $('#areas').html(root_html)

                });
            }

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
                            textY = height / 2 + (chart.titleBlock.height) + 40;

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
                            textY = height / 2 + (chart.titleBlock.height) + 25;

                        ctx.fillText(text, textX, textY);
                        // ctx.fillStyle = 'rgba(44, 87, 122, 1)';
                        ctx.save();
                    }
                }
            });

            function get_overview_realtime() {
                console.log("calling get_overview_realtime every 10 seconds");
                read_container_devan_member_screen('today');
                read_stocking_overview();
                read_remaining_incomplete();
                read_remaining_build_delivery();
                draw_pick_cycle_chart();
                draw_delivery_cycle_chart();
                draw_delivery_reason_chart();
                draw_pick_reason_chart();
            }

            function draw_part_chart() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_part_chat_value",
                        // page: 'Stocking'
                    },
                    dataType: 'HTML',
                }).done(function(response) {
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
                        $('#stock_rectangle_chart').html(html)
                    });


                    // new Chart("Part_Chart", {
                    // type: "bar",
                    // data: {
                    //     labels: xValues,
                    //     datasets: [{
                    //         backgroundColor: barColors,
                    //         data: yValues
                    //     }]
                    // },
                    // options: {
                    //     responsive: true,
                    //     // maintainAspectRatio: true,
                    //     // aspectRatio: 2,
                    //     animation: {
                    //         duration: 0
                    //     },
                    //     legend: {display: false},
                    //     title: {
                    //         display: false,
                    //         text: ""
                    //     },
                    //     scales:{
                    //         xAxes: [{
                    //             display: true //this will remove all the x-axis grid lines
                    //         }],
                    //         yAxes: [{
                    //             display: true //this will remove all the x-axis grid lines
                    //         }]
                    //     }
                    // }
                    // });
                });


            }

            function draw_delivery_reason_chart() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_delivery_reason_chat_value",
                        // page: 'Stocking'
                    },
                    dataType: 'HTML',
                }).done(function(response) {
                    var result = JSON.parse(response);
                    // console.log("Reason: ", result);
                    var xValues = []; //"AB", "BH", "BK", "BL", "BN", "FJ", "KN", "KQ"];
                    var yValues = [];
                    var barColors = []; //"red", "green","blue","orange","brown", "green","blue","orange"];

                    var reasons = result.reason;
                    var values = result.val;
                    var nIndex = 0;
                    for (var i = 0; i < values.length; i++) {
                        var tmp = parseInt(values[i]);
                        if (tmp > 0) {
                            xValues[nIndex] = reasons[i];
                            yValues[nIndex] = tmp;
                            if (tmp <= 2)
                                barColors[nIndex] = "#DD0000";
                            else if (tmp <= 5)
                                barColors[nIndex] = "yellow";
                            else
                                barColors[nIndex] = "green";
                            nIndex++;
                        }
                    }

                    xValues.push("0");
                    yValues.push(0);
                    barColors.push("white");

                    new Chart("Delivery_Reason_Chart", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            animation: {
                                duration: 0
                            },
                            legend: {
                                display: false
                            },
                            title: {
                                display: false,
                                text: ""
                            },
                            scales: {
                                xAxes: [{
                                    display: false //this will remove all the x-axis grid lines
                                }],
                                yAxes: [{
                                    display: true //this will remove all the x-axis grid lines
                                }]
                            }
                        }
                    });
                });
            }

            function draw_pick_reason_chart() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_pick_reason_chat_value",
                        // page: 'Stocking'
                    },
                    dataType: 'HTML',
                }).done(function(response) {
                    var result = JSON.parse(response);
                    // console.log("Reason: ", result);
                    var xValues = []; //"AB", "BH", "BK", "BL", "BN", "FJ", "KN", "KQ"];
                    var yValues = [];
                    var barColors = []; //"red", "green","blue","orange","brown", "green","blue","orange"];

                    var reasons = result.reason;
                    var values = result.val;
                    var nIndex = 0;
                    for (var i = 0; i < values.length; i++) {
                        var tmp = parseInt(values[i]);
                        if (tmp > 0) {
                            xValues[nIndex] = reasons[i];
                            yValues[nIndex] = tmp;
                            if (tmp <= 2)
                                barColors[nIndex] = "#DD0000";
                            else if (tmp <= 5)
                                barColors[nIndex] = "yellow";
                            else
                                barColors[nIndex] = "green";
                            nIndex++;
                        }
                    }

                    xValues.push("0");
                    yValues.push(0);
                    barColors.push("white");

                    new Chart("Pick_Reason_Chart", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            animation: {
                                duration: 0
                            },
                            legend: {
                                display: false
                            },
                            title: {
                                display: false,
                                text: ""
                            },
                            scales: {
                                xAxes: [{
                                    display: false //this will remove all the x-axis grid lines
                                }],
                                yAxes: [{
                                    display: true //this will remove all the x-axis grid lines
                                }]
                            }
                        }
                    });

                    //                     var xValues = ["Italy ", "France", "Spain", "USA", "Argentina"];
                    // var yValues = [55, 49, 44, 24, 15];
                    // var barColors = ["red", "green","blue","orange","brown"];

                    // new Chart("Pick_Reason_Chart", {
                    //   type: "bar",
                    //   data: {
                    //     labels: xValues,
                    //     datasets: [{
                    //       backgroundColor: barColors,
                    //       data: yValues
                    //     }]
                    //   },
                    //   options: {
                    //     legend: {display: false},
                    //     title: {
                    //       display: false,
                    //       text: "World Wine Production 2018"
                    //     }
                    //   }
                    // });

                });
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
                    if (remaining_cycles == null)
                        remaining_cycles = 0;
                    var count = response.count;
                    var cycle = response.cycle;
                    var cycle_str = "CYCLE" + Math.ceil(count / cycle);
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

            function draw_pick_cycle_chart() { //Draw Pick, Cycle Chart
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
                    console.log("draw_pick_cycle_chart: ", result);
                    var tmpUsers = result.users;
                    var keys = Object.keys(tmpUsers);
                    var length = keys.length;
                    var users = [];
                    for (var i = 0; i < length; i++) { // extracts session logged in conveyance_pick page from all sessions
                        var key = keys[i];
                        var user = tmpUsers[key];
                        if (user.page == "conveyance_pick.php" && user.user != undefined) {
                            var allow_push = 1;
                            for (var j = 0; j < users.length; j++)
                                if (users[j].user.username == user.user.username)
                                    allow_push = 0;
                            if (allow_push == 1)
                                users.push(user);
                        }
                    }

                    // console.log("draw_pick_cycle_chart: users: ", users);
                    // if(length < 1)
                    // alert('There is no user logged in Pick');
                    $("#pick_cycle_graph").html('');
                    $("#location_pick").html('');

                    length = users.length;
                    if (length > 4)
                        length = 4;
                    else if (length < 1)
                        return;
                    // console.log("users: ", users);
                    var overview_pick_zone = [];
                    var overview_pick_kanban = [];
                    var overview_pick_user = [];
                    var overview_pick_pick = [];
                    var overview_pick_cycle = [];
                    var overview_pick_OPR = [];
                    var pick_charts = [];
                    var cycle_charts = [];
                    var html = '';
                    var location_pick_html = '';
                    for (var i = 0; i < length; i++) { // build html for pick & cycle
                        // var key = keys[i];
                        // var user = users[key];                        
                        var user = users[i];
                        overview_pick_zone[i] = user.overview_pick_zone;
                        overview_pick_kanban[i] = user.overview_pick_kanban;
                        overview_pick_user[i] = user.overview_pick_user;
                        overview_pick_pick[i] = user.overview_pick_pick;
                        overview_pick_cycle[i] = user.overview_pick_cycle;
                        overview_pick_OPR[i] = user.overview_pick_OPR;
                        pick_charts[i] = `Pick_Chart${i}`;
                        cycle_charts[i] = `Cycle_Chart${i}`;
                        var col_html = `<div class="col-md-3 my-0">
                                            <div class="row mx-auto my-1 justify-content-center">
                                                <p class="mx-2 my-auto h5" style="text-transform: uppercase;">${overview_pick_zone[i]}</p>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <canvas id="${pick_charts[i]}"></canvas>
                                                </div>
                                                <div class="col-md-6 `
                        if (i < length - 1)
                            col_html += `border-right`;
                        col_html += `">
                                                    <canvas id="${cycle_charts[i]}"></canvas>
                                                </div>                                                
                                            </div>
                                            <div class="row mx-auto my-1 justify-content-center">
                                                <div class="row mx-1 my-0" style="background-color: green;">
                                                    <p class="mx-0 my-3 h5" style="color: white; transform: rotate(-90deg)">OPR
                                                    <p class="h5 my-3 mx-0 text-white">${overview_pick_OPR[i]}%</p>
                                                </div>
                                                <p class="mx-2 my-auto h4 font-weight-bold" style="text-transform: uppercase;">${overview_pick_user[i]}</p>
                                            </div>
                                        </div>`;
                        var col_location_html = `<div class="col-md-3 text-center `;
                        if (i < length - 1)
                            col_location_html += `border-right`;
                        col_location_html += `">
                                                <div class="row text-center justify-content-center">
                                                    <p class="h2 font-weight-bold">${overview_pick_kanban[i]}</p>
                                                    <p class="h2 font-weight-bold">&nbsp;/&nbsp;</p>
                                                    <p class="h2 font-weight-bold" style="text-transform: uppercase;">${overview_pick_zone[i]}</p>
                                                </div>
                                            </div>`;
                        html += col_html;
                        location_pick_html += col_location_html;
                    }
                    $("#pick_cycle_graph").html(html);
                    $("#location_pick").html(location_pick_html);

                    // draw chart
                    for (var i = 0; i < length; i++) {
                        var pick_status = overview_pick_pick[i];
                        var a = parseInt(pick_status.split("/")[0]);
                        var b = parseInt(pick_status.split("/")[1]);
                        var xValues = ["A", "Total"];
                        var yValues = [a, b - a];
                        var barColors = ["#2c577a", "#E0E0E0"];

                        new Chart(pick_charts[i], { // Pick chart
                            type: "doughnut",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    label: '',
                                    backgroundColor: barColors,
                                    data: yValues
                                }]
                            },
                            options: {
                                animation: {
                                    duration: 0
                                },
                                rotation: 1.5 * Math.PI,
                                // circumference: 1 * Math.PI,
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: false,
                                    text: "Dolly1"
                                },
                                responsive: true,
                                cutoutPercentage: 70,
                                "pick_text1": "PICK",
                                "pick_text2": pick_status,
                            },
                        });
                    }

                    for (var i = 0; i < length; i++) {
                        var pick_status = overview_pick_cycle[i];
                        var a = parseInt(pick_status.split("/")[0]);
                        var b = parseInt(pick_status.split("/")[1]);
                        var xValues = ["A", "Total"];
                        var yValues = [a, b - a];
                        var barColors = ["#2c577a", "#E0E0E0"];

                        new Chart(cycle_charts[i], { // Cycle chart
                            type: "doughnut",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    label: 'abcde',
                                    backgroundColor: barColors,
                                    data: yValues
                                }]
                            },
                            options: {
                                responsive: true,
                                cutoutPercentage: 70,
                                animation: {
                                    duration: 0
                                },
                                rotation: 1.5 * Math.PI,
                                // circumference: 1 * Math.PI,
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: false,
                                    text: "Dolly1"
                                },
                                "pick_text1": "CYCLE",
                                "pick_text2": pick_status,
                            },
                        });
                    }

                });
            }

            function draw_system_fill_chart() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_system_fill_percentage",
                        // page: 'Stocking'
                    },
                    dataType: 'HTML',
                }).done(function(result) {
                    var res = JSON.parse(result);
                    var xValues = ["Filled", "Total"];
                    var total = (res.total);
                    var filled = (res.filled);
                    var yValues = [filled, total - filled];
                    var percentage = parseFloat(filled / total * 100).toFixed(2);
                    var filled_color = "";
                    if (percentage <= 2)
                        filled_color = "red";
                    else if (percentage > 2 && percentage <= 5)
                        filled_color = "green";
                    else
                        filled_color = "yellow";
                    // var barColors = [filled_color, "#E0E0E0"];
                    var barColors = ["gray", "#E0E0E0"];

                    new Chart("System_Fill_Chart", {
                        type: "doughnut",
                        data: {
                            labels: xValues,
                            datasets: [{
                                label: 'SF',
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            animation: {
                                duration: 0
                            },
                            rotation: 1 * Math.PI,
                            circumference: 1 * Math.PI,
                            legend: {
                                display: false
                            },

                            title: {
                                display: true,
                                // text: "System Fill",
                                position: 'bottom',
                                font: {
                                    size: 24
                                },
                            },

                            "centertext": percentage.toString() + "%",
                            "title_text": "SYSTEM FILL",
                        },
                    });
                });
            }

            function draw_part_stocking_chart() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_part_stocking_percentage",
                        // page: 'Stocking'
                    },
                    dataType: 'HTML',
                }).done(function(result) {
                    var res = JSON.parse(result);
                    var xValues = ["Filled", "Total"];
                    var total = (res.total);
                    var filled = (res.filled);
                    var yValues = [filled, total - filled];
                    var percentage = parseFloat(filled / total * 100).toFixed(2);
                    var filled_color = "";
                    if (percentage <= 2)
                        filled_color = "red";
                    else if (percentage > 2 && percentage <= 5)
                        filled_color = "green";
                    else
                        filled_color = "yellow";
                    // var barColors = [filled_color, "#E0E0E0"];
                    var barColors = ["gray", "#E0E0E0"];

                    new Chart("Part_Stocking_Chart", {
                        type: "doughnut",
                        data: {
                            labels: xValues,
                            datasets: [{
                                label: 'PS',
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            animation: {
                                duration: 0
                            },
                            rotation: 1 * Math.PI,
                            circumference: 1 * Math.PI,
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                // text: "Part Stocking",
                                position: 'bottom',
                            },
                            "centertext": parseFloat(filled / total * 100).toFixed(2).toString() + "%",
                            "title_text": "PART STOCKING",
                        },
                    });
                });

            }

            function draw_free_location_chart() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "get_free_location_percentage",
                        // page: 'Stocking'
                    },
                    dataType: 'HTML',
                }).done(function(result) {
                    var res = JSON.parse(result);
                    var xValues = ["Filled", "Total"];
                    var total = (res.total);
                    var filled = (res.filled);
                    var yValues = [filled, total - filled];
                    var percentage = parseFloat(filled / total * 100).toFixed(2);
                    var filled_color = "";
                    if (percentage <= 2)
                        filled_color = "red";
                    else if (percentage > 2 && percentage <= 5)
                        filled_color = "green";
                    else
                        filled_color = "yellow";
                    // var barColors = [filled_color, "#E0E0E0"];
                    var barColors = ["gray", "#E0E0E0"];

                    new Chart("Free_Location_Chart", {
                        type: "doughnut",
                        data: {
                            labels: xValues,
                            datasets: [{
                                label: 'FL',
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            animation: {
                                duration: 0
                            },
                            rotation: 1 * Math.PI,
                            circumference: 1 * Math.PI,
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                // text: "Free Location",
                                position: 'bottom',
                            },
                            "centertext": parseFloat(filled / total * 100).toFixed(2).toString() + "%",
                            "title_text": "FREE LOCATION",
                        },
                    });
                });

            }

            $("#btn_load_data").on('click', function() {
                var date = $("#date").val();
                if (date == '') {
                    $("#date").focus();
                    return false;
                }
                read_container_devan_member_screen(date);
            });

            function read_stocking_overview() {
                draw_system_fill_chart();
                draw_part_stocking_chart();
                draw_free_location_chart();
                draw_part_chart();
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

            function read_container_devan_member_screen(date) {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        'action': 'get_live_deban_overview1',
                        'date': date
                    },
                    dataType: 'HTML',
                }).done(function(html) {
                    $("#devan_screen").html(html);
                    // $("#devan_screen").addClass("bg-red");
                    //     $.ajax({
                    //         url: "actions.php",
                    //         method: "post",
                    //         data: {
                    //             action:"get_help_alarm"
                    //         },
                    //         dataType:'HTML',
                    //     }).done(function (html) {
                    //         if(html != 'NO HELP') {
                    //             // $("#help_modal").find('.modal-body').html(html);
                    //             // $("#help_modal").modal();
                    //             $("#devan_screen").addClass("bg-red");
                    //         }
                    //     });
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

        });
    </script>
</body>

</html>