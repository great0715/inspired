<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Conveyance Delivery";
$_SESSION['page'] = 'conveyance_delivery.php';
login_check();
require_once("assets.php");
?>
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
<style>
    .font-2 {
        font-size: 2rem;
    }

    .aa {
        font-size: 25px;
    }

    .font-3 {
        font-size: 3rem;
    }

    .font-5 {
        font-size: 5rem;
    }

    .action {
        height: 64px;
        color: #FFF;
        text-transform: uppercase;
        font-size: 30px;
        font-weight: 600;
        width: 200px;
    }

    .kanban {
        height: 480px;
        text-transform: uppercase;
    }

    .start-kanban {
        background-color: #005669;
        color: #FFF;
        height: auto;
        text-transform: uppercase;
        text-align: center;
    }

    div#current_address {
        font-size: xx-large;
        background: black;
    }

    div#current_addressdel {
        font-size: xx-large;
        background: black;
    }

    div#current_kanban {
        background: black;
        font-size: 60px;
    }

    div#current_kanban_part_number {
        margin: -17px 0px 0px 0px;
        background: black;
    }

    select#cycle_select {
        font-size: 36px;
    }

    .kanban_input {
        background-color: #ff0000;
        border-radius: 8px;
        color: #ffffff;
        height: 45px;
        text-align: center;
        font-size: 30px;
        text-transform: uppercase;
    }

    .kanban_input:focus {
        background-color: #4D737C;
        border-color: #4D737C;
    }

    #start_button {
        width: 180px;
        height: 180px;
        font-size: 40px;
        text-transform: uppercase;
        border-radius: 90px;
    }

    .red-kanban {
        background-color: #FF0000;
        color: #FFF;
    }

    .green-kanban {
        background-color: #3AAD00;
        color: #A0D785
    }

    .blue-kanban {
        background-color: #005669;
        color: #FFF;
    }

    .grey-kanban {
        background-color: #E6E7E6;
        color: #FFF;
    }

    .action {
        height: 64px;
        color: #FFF;
        text-transform: uppercase;
        font-size: 30px;
        font-weight: 600;
        width: 150px;
    }

    .logout-box {
        background-color: #1697FF;
    }

    .help-box {
        background-color: #FFAE00;
    }

    .item-div {
        width: 100%;
        padding: 10px;
        text-align: center
    }

    .date-string {
        color: #b8b7b7;
        font-size: 26px;
    }

    .pick-list {
        font-size: 22px;
        font-weight: 600;
    }

    .completed-kanban {
        color: green;
        text-decoration: line-through;
        font-size: 20px;
    }

    .uncompleted-kanban {
        color: #000;
        font-size: 20px;
    }

    .helped-kanban {
        color: red;
        font-size: 20px;
    }

    .content-header {
        padding: 15px 0.5rem;
        display: none;
    }

    .p-2 {
        padding: 0.5rem !important;
        width: 49%;
    }

    .select-kanban {
        cursor: pointer;
    }

    .bg-green #btn_finish {
        background-color: #00FF51 !important;
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
            <div class="content-header">
                <div class="container-fluid">
                    <div id="locktarget" style="display: none">Target</div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h1 class="m-0" style="display: inline"><?php echo $page_name; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <div class="row m-0 p-0 start-kanban" id="start_area" style="display: none;">
                        <div class="col-md-2" style="padding-top: 20px;">
                            <div id="date_picker" class="text-center" style="width: 100%; font-size: 24px; font-weight: 600">
                                <input class="form-control pick_date" type="text" style="display: inline-block; width: 200px;" value="<?php echo date('d/m/Y'); ?>">
                            </div>
                        </div>
                        <div class="col-md-8" style="padding-top: 20px;">
                            <input type="text" class="form-control kanban_input" id="start_kanban_input" placeholder="INPUT">
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-12 align-items-center" style="display: flex; min-height: 200px;">
                            <h1 style="text-align: center; width: 100%;">Please start delivery</h1>
                        </div>
                        <div class="col-md-12" style="min-height: 250px;">
                            <button class="btn btn-success" id="start_button">Start</button>
                        </div>
                    </div>

                    <div class="row m-0 p-0" id="delivery_area" style="display: inline-block;">
                        <div class="col-md-12">
                            <input type="hidden" id="current_kanban_id" value="">
                            <div id="locktarget" style="display: none">Target</div>
                            <div class="row m-0 p-0 start-kanban finish-box" id="kanban_area">
                                <div class="col-md-3 align-items-center" style="display: flex">
                                    <div id="date_picker" class="text-center" style="width: 100%; font-size: 24px; font-weight: 600">
                                        <input class="form-control pick_date" type="text" id="pick_date" name="pick_date" style="display: inline-block; width: 200px;" value="<?php echo date('d/m/Y'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 text-center">
                                    <div class="item-div" id="input_div">
                                        <input type="text" class="form-control kanban_input" id="kanban_input" name="kanban_input" autofocus placeholder="kanban">
                                    </div>
                                </div>
                                <div class="col-md-3 align-items-center" style="display: flex">
                                    <button class="btn text-center" id="button_dolly" style="margin: 0 auto; width: 150px; background-color: #3B23A7; color: #FFF;">Dolly</button>
                                </div>

                                <div class="col-md-6">
                                    <div class="font-2" style="color: #fff; font-size:10px; background: #3b3b3b; margin-top: 20px;">Kanban</div>
                                    <div class="font-5" id="current_kanban"></div>
                                    <div class="" id="current_kanban_part_number"></div>
                                    <input type="hidden" id="current_kanban_part_num" />
                                </div>
                                <div class="col-md-6">
                                    <div class="font-2" style="color: #fff; font-size: 10px; background: #3b3b3b; margin-top: 15px;">Address</div>
                                    <div class="font-5" id="current_address"></div>
                                </div>

                                <div class="col-md-6 p-2">
                                    <button class="btn bg-red action" id="andon_help">Help</button>
                                </div>
                                <div class="col-md-6 p-2">
                                    <a class="btn action bg-blue" id="btn_finish" style="background-color:#00FF51; padding-top: 10px;">Finish</a>
                                </div>

                                <div class="col-md-2" style="background-color: #fff;"></div>
                                <div class="col-md-8">
                                    <div class="font-3" style="font-size: 22px;">
                                        <span class="aa" style="color: white; font-size: 22px">Delivery List:</span>
                                        <span id="pick_status"> 0/0</span>
                                        <span id="cycle_span" style="display: none;">
                                            <span class="aa" style="color: white; font-size: 22px">Cycle:</span>
                                            <select name="cycle_select" id="cycle_select">
                                            </select>
                                        </span>
                                        <span id="zone_span" style="display: none;">
                                            <span class="" style="color: black;">Zones:</span>
                                            <select name="zone_select" id="zone_select">
                                            </select>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2" style="background-color: #fff; border-right: 0"></div>
                            </div>
                            <div class="row" id="pick_list">
                                <div class="col-md-6" style="min-height: 360px;"></div>
                                <div class="col-md-6" style="min-height: 360px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row m-0 p-0 finish-box">
                        <div class="col-md-12 align-items-center" style="display: flex; height: 40px; text-align: center">
                            <div class="text-center" style="width: 100%; font-size: 24px; font-weight: 600;">
                                <a href="logout.php" style="color: #FFF;"><?php echo $_SESSION['user']['username'] ?>: Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <?php include("footer.php"); ?>
    </div>

    <input type="hidden" id="kanban_id" value="0">
    <input type="hidden" id="is_help" value="0">
    <input type="hidden" id="status" value="delivery">
    <input type="hidden" id="input_type" value="kanban">

    <div class="modal fade" id="confirm_user_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Select User</h4>
                </div>
                <div class="modal-body">
                    <?php
                    $users = get_all_users();
                    $reasons = get_all_reason();
                    echo '<select class="form-control" id="confirm_user_id" name="confirm_user_id">';
                    echo '<option></option>';
                    foreach ($users as $user) {
                        if ($user['type'] == 1)
                            echo '<option value="' . $user['user_id'] . '">' . $user['username'] . '</option>';
                    }
                    echo '</select>';
                    echo '<br>';
                    echo '<input type="checkbox" id="deliver_skip" name="deliver_skip">';
                    echo '<label for="deliver_skip"> &nbsp; Deliver / Skip</label><br>';
                    echo '<div class="reason" style="display:none">';
                    echo '<label> Reason</label><br>';
                    echo '<select class="form-control" id="confirm_reason" name="confirm_reason">';
                    foreach ($reasons as $item) {
                        echo '<option value="' . $item->id . '">' . $item->name . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
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
    <script>
        $(document).ready(function() {
            $(document).keypress(function(event) {
                // console.log(event.key);
                $("#kanban_input").focus();
            });

            $(document).on('click', "#deliver_skip", function() {
                // alert("hello");
                // var checked = $(this).is(':checked');
                // if(checked) {
                //     $(".reason").show();
                // } else {
                //     $(".reason").hide();
                // }
                toggleReason();
            });

            $(".pick_date").datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                    previous: 'fas fa-angle-left',
                    next: 'fas fa-angle-right',
                }
            });

            /*
            Lock input
            */
            var locktarget = document.querySelector('#locktarget');
            var pointerlockchangeIsFiredonRequest = false;
            var posX = posY = 0;
            var event_counter = 0;
            var request_counter = 0;

            $(document).on('click', "#input_div", function() {
                LockTarget();
            });

            document.addEventListener("pointerlockchange", function() {
                event_counter++;
                if (event_counter === 1) {
                    pointerlockchangeIsFiredonRequest = true;
                    runRequestPointerLockTest();
                } else if (event_counter === 2) {
                    runExitPointerLockTest();
                } else if (event_counter === 3) {
                    runReEnterPointerLockTest()
                } else if (event_counter > 104) {
                    runRepeatLockPointerTest();
                }
            });

            function toggleReason() {
                var checked = $("#deliver_skip").is(':checked');
                if (checked) {
                    $(".reason").show();
                    playAudio('assets/audio/Deliver_skip_activated.wav');
                } else {
                    $(".reason").hide();
                }
            }

            function runRequestPointerLockTest() {
                posX = window.screenX;
                posY = window.screenY;
            }

            function runExitPointerLockTest() {
                locktarget.requestPointerLock(); // To re-enter pointer lock
            }

            function runReEnterPointerLockTest() {
                /*reenterPointerLockTest.step(function() {
                 assert_true(document.pointerLockElement === locktarget, "Pointer is locked again without engagement gesture");
                 });

                 lock_log.innerHTML = "Status: Exited pointer lock; Please click the 'Repeat Lock' button and exit the lock.";

                 reenterPointerLockTest.done();*/
            }

            function runRepeatLockPointerTest() {
                repeatLockPointerTest.step(function() {
                    assert_equals(request_counter + 5, event_counter, "Each requestPointerLock() will fire a pointerlockchange event");
                });

                lock_log.innerHTML = "Status: Test over.";

                repeatLockPointerTest.done();
            }

            function LockTarget() {
                locktarget.requestPointerLock();
            }


            $("#start_button").on('click', function() {
                $("#start_area").hide();
                $("#delivery_area").show();
                read_kanban_box();
            });

            $(document).on('keypress', "#start_kanban_input", function(e) {
                if (e.keyCode == 13) {
                    var value = $(this).val();
                    if (value != "") {
                        $("#start_area").hide();
                        $("#delivery_area").show();
                        $(".logout-box").css('background-color', '#053C48');
                        $(".finish-box").css('background-color', '#053C48');
                        read_kanban_box();
                    } else {
                        return false;
                    }
                }
            });



            $('#pick_date').on('dp.change', function(e) {
                $("#cycle_span").css("display", "none")
                $("#cycle_select").empty()
                $("#zone_span").css("display", "none")
                $("#zone_select").empty()
                read_kanban_box();
            })

            function triggerPickDate() {
                $("#pick_date").val(moment().format('D/M/YYYY'));
                $("#pick_date").trigger("dp.change");
            }

            triggerPickDate();

            function read_kanban_box() {
                var pick_date = $("#pick_date").val();
                var kanban_id = $("#kanban_id").val();
                var status = $("#status").val();
                var cycle = $("#cycle_select").val() ? $("#cycle_select").val() : -1;
                var zone = $("#zone_select").val() ? $("#zone_select").val() : -1;
                var zone_text = $("#zone_select").val() ? $("#zone_select").val() : "All";
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'read_kanban_box',
                        pick_date: pick_date,
                        kanban_id: kanban_id,
                        status: status,
                        cycle: cycle,
                        zone: zone_text
                    },
                    dataType: 'JSON',
                }).done(function(result) {
                    // if(result.is_completed == "1")
                    // alert("completed");
                    var zones = result.zones;
                    html = '<option value=0 selected>All</option>';
                    for (var i = 0; i < zones.length; i++) {
                        html += `<option value="${zones[i]['dolly']}" ${zone == zones[i]['dolly'] ? "selected" : ""}>${zones[i]['dolly']}</option>`
                    }
                    html += `<option value="Help" ${zone == "Help" ? "selected" : ""}>Help</option>`;
                    $("#zone_span").css("display", "inline-block")
                    $("#zone_select").html(html)

                    // alert(result.cur_cycle)
                    if (result.max_cycle && result.max_cycle != -1) {
                        if (!result.cur_cycle)
                            result.cur_cycle = result.max_cycle
                        var html = "";
                        for (var i = 1; i <= result.max_cycle; i++) {
                            html += `<option value="${i}" ${result.cur_cycle == i ? "selected" : ""}>${i}/${result.max_cycle}</option>`
                        }
                        $("#cycle_span").css("display", "inline-block")
                        $("#cycle_select").html(html)
                    }
                    if (result.error) {
                        alert(result.error);
                        read_pick_list();
                        check_finish();
                        $("#pick_status").text(result.pick_status);
                        return false;
                    } else {
                        console.log(result);
                        $("#current_kanban_id").val(result.kanban_id);
                        if (result.part_number)
                            var current_kanban_html =
                                '<span style="color: #fa0000; font-size: 15px;">Part No:</span><span style="color: #FFF; font-size: 30px;">' +
                                result.part_number + '</span>'
                        else
                            var current_kanban_html =
                                '<span style="color: #fa0000; font-size: 15px;">Part No:</span><span style="color: #FFF; font-size: 30px;"></span>'
                        if (result.is_delivered == 1) {
                            $("#current_kanban").text("Zone Complete - Scan Next Zone");
                            $("#current_kanban_part_number").hide();
                            $("#current_address").text("");
                            $("#current_addressdel").text("");
                        } else {
                            $("#current_kanban").text(result.kanban);
                            $("#current_kanban_part_number").show();
                            $("#current_kanban_part_number").html(current_kanban_html);
                            $("#current_address").text(result.address);
                            $("#current_addressdel").text(result.delivery_address);
                        }
                        $("#current_kanban_part_num").val(result.part_number);
                        $("#button_dolly").text(result.dolly);
                        $("#button_dolly").css('background-color', result.dolly_color);
                        $("#pick_status").text(result.pick_status);
                        $("#is_help").val(result.is_help);
                        // console.log("BBBBB: ", result.is_help, result.is_completed)
                        if (result.is_help == 1 && result.is_delivered == 0) {
                            // alert("is_help")
                            $("#is_help").val(result.is_help);
                            $(".finish-box").removeClass('blue-kanban');
                            $(".finish-box").removeClass('green-kanban');
                            $(".finish-box").removeClass('bg-green');
                            $(".finish-box").addClass('red-kanban');
                        }

                        if (result.is_help == 0 && result.is_delivered == 0) {
                            $(".finish-box").removeClass('green-kanban');
                            $(".finish-box").removeClass('red-kanban');
                            $(".finish-box").removeClass('bg-green');
                            $(".finish-box").addClass('blue-kanban');
                        }

                        if (result.is_delivered == 1) {
                            $(".finish-box").removeClass('blue-kanban');
                            $(".finish-box").removeClass('red-kanban');
                            $(".finish-box").addClass('green-kanban');
                        }
                        check_finish();
                        read_pick_list();
                    }
                });
            }

            //Kanban List
            function read_pick_list() {
                var pick_date = $("#pick_date").val();
                var status = $("#status").val();
                var cycle = $("#cycle_select").val()
                var zone = $("#zone_select").val()
                var current_kanban_id = $("#current_kanban_id").val();
                console.log("BBBBBB: ", cycle, zone, current_kanban_id);
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'read_pick_list',
                        pick_date: pick_date,
                        status: status,
                        current_kanban_id: current_kanban_id,
                        zone: zone,
                        cycle: cycle
                    },
                    dataType: 'HTML',
                }).done(function(result) {
                    $("#pick_list").html(result);
                });
            }

            function toNext() {
                var cycle = $("#cycle_select").val()
                $("#cycle_select").val(parseInt(cycle) + 1)
                read_kanban_box()
            }

            function playAudio(file) {
                // alert("playAudio");
                var audio = new Audio(file);
                audio.play();
            }

            function check_finish() {
                var status = $("#status").val();
                var pick_date = $("#pick_date ").val();
                var cycle = $("#cycle_select").val() ? $("#cycle_select").val() : -1
                var zone = $("#zone_select").val() ? $("#zone_select").val() : -1

                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'check_pick_finish',
                        status: status,
                        pick_date: pick_date,
                        cycle: cycle
                    },
                }).done(function(result) {
                    if (result == 'success') {
                        $("#btn_finish").attr("href", "###")
                        $("#btn_finish").one("click", toNext)
                        $(".finish-box").addClass('bg-green');
                    }

                    if (result == 'finish' || result == "not") {
                        if (result == 'finish') {
                            $(".finish-box").addClass('bg-green');
                        }
                        $("#btn_finish").attr('href', 'logout.php');
                    }

                    if (result == 'in_progress') {
                        $("#btn_finish").attr('href', '#');
                        $("#btn_finish").removeClass('bg-green');
                        $("#btn_finish").addClass('bg-blue');
                    }

                    if (result == 'in_help') {
                        $("#btn_finish").attr('href', '#');
                        $("#btn_finish").addClass('bg-blue');
                        $("#btn_finish").removeClass('bg-green');
                    }
                });
            }

            var help_pressed = false;
            $(document).on('click', '#andon_help', function() {
                if ($("#current_kanban_id").length == 0 || $("#current_kanban_id").val() == '')
                    return false;
                if (!help_pressed) {
                    playAudio('assets/audio/Andon_help_called_awaiting_team_leader.wav');
                    help_pressed = true;
                }
                var kanban_id = $("#current_kanban_id").val();
                var is_help = $("#is_help").val();

                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                let hr = today.getHours();
                let min = today.getMinutes();
                let sec = today.getSeconds();
                const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;

                if (is_help == 0) {
                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            kanban_id: kanban_id,
                            action: "conveyance_andon_help",
                            status: 'delivery',
                            today: formattedToday
                        },
                    }).done(function(result) {
                        if (result == 'ok') {
                            read_pick_list();
                            read_kanban_box()
                        } else {
                            alert('Help failed');
                        }
                    });
                } else {
                    $("#deliver_skip").prop("checked", false);
                    toggleReason();
                    playAudio('assets/audio/Please_select_reason_for_andon.wav');
                    $("#confirm_user_modal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            });
            $(document).on('keypress', "#kanban_input", function(e) {
                if (e.keyCode == 13) {
                    $("#kanban_id").val(0);
                    var value = $(this).val().toUpperCase();
                    var input_type = $("#input_type").val();
                    var kanban_no = $("#current_kanban").text();
                    var location = $("#current_address").text();
                    var part_num = $("#current_kanban_part_num").val();

                    if (input_type == 'kanban') {
                        if (value == kanban_no || value == part_num) {
                            $("#input_type").val('location');
                            $("#kanban_input").val('');
                            $("#kanban_input").attr('placeholder', 'Address or Part No');
                            $("#kanban_input").focus();
                            return false;
                        } else {
                            value1 = value
                            value1 = value1.replace(/-/g, "");
                            if (value1 == part_num || value1 == kanban_no) {
                                $("#input_type").val('location');
                                $("#kanban_input").val('');
                                $("#kanban_input").attr('placeholder', 'Address or Part No');
                                $("#kanban_input").focus();
                                return false;
                            } else {
                                if (value1.search(part_num) != -1 || value1.search(kanban_no) != -1) {
                                    $("#input_type").val('location');
                                    $("#kanban_input").val('');
                                    $("#kanban_input").attr('placeholder', 'Address or Part No');
                                    $("#kanban_input").focus();
                                    return false;
                                } else {
                                    value1 = value
                                    value1 = value1.replace(/-/g, "");
                                    value1 = value1 == 'ALL' ? '0' : value1;
                                    let selectElement = document.querySelectorAll('[name=zone_select]');
                                    let optionValues = [...selectElement[0].options].map(o => o.value.toUpperCase());
                                    let optionValues_old = [...selectElement[0].options].map(o => o.value);
                                    if (optionValues.includes(value1)) {
                                        const search_func = (element) => element == value1;
                                        var found_index = optionValues.findIndex(search_func);
                                        $("#zone_select").val(optionValues_old[found_index]);
                                        read_kanban_box();
                                        $("#kanban_input").val('');
                                    } else {
                                        var pick_date = $("#pick_date").val();
                                        var cycle = $("#cycle_select").val();
                                        var status = $("#status").val();
                                        $.ajax({
                                            url: "actions.php",
                                            method: "post",
                                            data: {
                                                action: 'get_kanban_id_by_name',
                                                pick_date: pick_date,
                                                kanban: value1,
                                                cycle: cycle,
                                                status: status
                                            },
                                            dataType: 'HTML',
                                        }).done(function(result) {
                                            var kanban_id = parseInt(result);
                                            $("#kanban_id").val(kanban_id);
                                            $("#current_kanban_id").val(kanban_id);
                                            read_kanban_box();
                                            $("#kanban_input").val('');
                                            $("#kanban_input").focus();
                                        });
                                        return false;
                                    }

                                }
                            }
                        }
                    }

                    if (input_type == 'location') {
                        if (value != location && value != part_num) {
                            // alert("2");
                            playAudio('assets/audio/Can’t_deliver_Incorrect_delivery_address_please_try_again.wav');
                            alert('Error Wrong location');
                            $("#kanban_input").val('');
                            $("#kanban_input").focus();
                            return false;
                        } else {
                            var kanban_id = $("#current_kanban_id").val();
                            $.ajax({
                                url: "actions.php",
                                method: "post",
                                data: {
                                    input_type: input_type,
                                    kanban_id: kanban_id,
                                    action: "conveyance_delivery",
                                    reason: 0
                                },
                            }).done(function(result) {
                                if (result == 'ok') {
                                    $("#kanban_input").val('');
                                    $("#kanban_input").focus();
                                    $("#kanban_input").attr('placeholder', 'Kanban');
                                    $("#input_type").val('kanban');
                                    $(".finish-box").addClass("green-kanban").removeClass("blue-kanban").removeClass("red-kanban")
                                    playAudio('assets/audio/kanban_complete.wav');
                                    read_kanban_box();
                                } else {
                                    // alert("3");
                                    playAudio('assets/audio/Incorrect_kanban_entered_please_try_again.wav');
                                    alert('Incorrect kanban or address(Part number)');
                                }
                            });
                        }
                    }
                }
            });

            $(document).on('click', '#btn_finish', function() {
                var href = $(this).attr('href');
                if (href == '#') {
                    playAudio('assets/audio/Kanbans_still_outstanding_can’t_finish_cycle.wav');
                    alert('You can not finish before delivery all kanban');
                    return false;
                }
                playAudio('assets/audio/Cycle_complete.wav');
            });

            $("#confirm_help_with_user").on('click', function() {
                if ($("#current_kanban_id").length == 0 || $("#current_kanban_id").val() == '')
                    return false;
                var kanban_id = $("#current_kanban_id").val();
                var confirm_user_id = $("#confirm_user_id").val();
                var deliver_skip = $('#deliver_skip').is(':checked') ? 1 : 0;
                var reason_val = $("#confirm_reason").val();
                var reason = deliver_skip == 1 ? reason_val : 0;
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                let hr = today.getHours();
                let min = today.getMinutes();
                let seconds = today.getSeconds();
                const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ':' + min + ':' + seconds;
                // alert(deliver_skip)
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "confirm_conveyance_andon_help",
                        confirm_user_id: confirm_user_id,
                        is_delivered: deliver_skip,
                        reason: reason,
                        kanban_id: kanban_id,
                        today: formattedToday,
                    },
                    dataType: 'HTML',
                }).done(function(html) {
                    console.log("DDDDDDDDD: ", html);
                    $("#confirm_user_modal").modal('hide');
                    $(".help-box").removeClass('bg-red');
                    $("#btn_finish").removeClass('bg-red');
                    $(".finish-box").removeClass('red-kanban');
                    $("#kanban_area").removeClass('red-kanban');
                    $("#is_help").val('0');
                    // read_pick_list();
                    $("#kanban_id").val(0);
                    if (deliver_skip == 1) {
                        playAudio('assets/audio/kanban_complete.wav');
                    }
                    read_kanban_box();
                    check_finish();
                });
            });

            $(document).on('click', '.select-kanban', function() {
                var kanban_id = $(this).attr('data-kanban');
                // console.log('kanban_id = ', kanban_id);
                $("#kanban_id").val(kanban_id);
                $("#current_kanban_id").val(kanban_id);
                read_kanban_box();
            });

            $("#cycle_select").on("change", function() {
                read_kanban_box()
            })

            $("#zone_select").on("change", function() {
                $("#kanban_id").val(0);
                read_kanban_box();
                playAudio('assets/audio/Zone_selected.wav');
            })

        });
    </script>
</body>

</html>