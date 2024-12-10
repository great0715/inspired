<?php

require_once("config.php");
require_once("functions.php");
$page_name = "Conveyance Pick";
$_SESSION['page'] = 'conveyance_pick.php';
login_check();
require_once("assets.php");
?>
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
<style>
    .bg-green #btn_finish {
        background-color: #00FF51 !important;
    }

    .finish-box {
        background-color: #1697FF;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
    }

    .content-header {
        padding: 15px 0.5rem;
        display: none;
    }

    .kanban_input {

        background-color: #ff0000;
        border-radius: 8px;
        color: #ffffff;
        height: 35px;
        text-align: center;
        font-size: 30px;
        text-transform: uppercase;
    }
a#btn_finish {
    display: none;
}
    div#current_address {
        font-size: xx-large;
        background: black;
    }
	
	button#andon_help {
    margin: 3px -232px 1px 1px;
    width: 200%;
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

    div#zone_error_msg {
        display: none;
        background: black;
        color: red;
    }

    select#cycle_select {
        font-size: 36px;
    }

    .p-2 {
        padding: 0.5rem !important;
        width: 50%;
    }

    .font-2 {
        font-size: 2rem;
    }

    .font-3 {
        font-size: 3rem;
    }

    .font-5 {
        font-size: 5rem;
    }

    .col-md-12.align-items-center {
        position: fixed;
        bottom: 0px;
        background: #1797ff;
        width: 92%;
    }

    .action {
        height: 64px;
        color: #FFF;
        text-transform: uppercase;
        font-size: 30px;
        font-weight: 600;
        width: 150px;
    }

    .blue-kanban {
        background-color: #1697FF;
        color: #FFF;
    }

    .red-kanban {
        background-color: #FF0000;
        color: #FFF;
    }

    .green-kanban {
        background-color: #3AAD00;
        color: #A0D785
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

    .kanban {
        height: 480px;
        text-transform: uppercase;
    }

    .kanban_input:focus {
        background-color: #58B4FF;
    }

    .item-div {
        width: 100%;
        padding: 10px;
        text-align: center
    }

    .date-string {
        color: #000;
        font-size: 26px;
    }

    .pick-list {
        font-size: 40px;
        font-weight: 600;
    }

    .select-kanban {
        cursor: pointer;
    }

    .cycle {
        font-size: 15px;
    }

    .content {
        padding-top: 3% !important;
    }
</style>

<body class="hold-transition sidebar-collapse layout-top-nav" onload="startTime()">
    <div class="wrapper">
        <?php
        include("header.php"); ?>
        <?php
        include("menu.php"); ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h1 class="m-0" style="display: inline"><?php
                                                                    echo $page_name; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <input type="hidden" id="current_kanban_id" value="">
                    <div id="locktarget" style="display: none">Target</div>
                    <div class="row m-0 p-0 finish-box" id="kanban_area">
                        <div class="col-md-3 align-items-center" style="display: flex">
                            <div id="date_picker" class="text-center" style="width: 100%; font-size: 24px; font-weight: 600">
                                <input class="form-control" type="text" id="pick_date" name="pick_date" style="display: inline-block; width: 200px;" value="<?php
                                                                                                                                                            echo date('d/m/Y'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="item-div" id="input_div">
                                <input type="text" class="form-control kanban_input" id="kanban_input" name="kanban_input" autofocus placeholder="INPUT">
                            </div>
                        </div>
                        <div class="col-md-3 align-items-center" style="display: flex">
                            <button class="btn text-center" id="button_dolly" style="margin: 0 auto;  width: 100%; font-weight: 800; font-size: 32px; text-transform: uppercase; background-color: #3B23A7; color: #FFF;">
                                Dolly
                            </button>
                        </div>


                        <div class="col-md-6">
                            <div class="font-2" style="color: #fff; font-size: 10px; background: #3b3b3b;">
                                Kanban:
                            </div>
                            <div>
                                <div class="font-5" id="current_kanban"></div>
                                <div class="" id="current_kanban_part_number"></div>
                            </div>
                            <div id="zone_error_msg">
                                Zone cycle complete
                                <br>
                                Please scan next zone
                            </div>
                            <input type="hidden" id="current_kanban_part_num" />
                        </div>
                        <div class="col-md-6">
                            <div class="font-2" style="color: #fff; font-size: 10px; background: #3b3b3b; margin-top: 15px;">
                                Pick
                                Address:
                            </div>
                            <div class="font-5" id="current_address"></div>
                            <div class="font-2" style="color: #fff; font-size: 10px; background: #3b3b3b; margin-top: 15px">
                                Delivery
                                Address:
                            </div>
                            <div class="font-5" id="current_addressdel"></div>
                        </div>

                        <div class="col-md-6 p-2">
                            <button class="btn bg-red action" id="andon_help">Help
                            </button>
                        </div>
                        

                       
                        <div class="col-md-12">
                            <div class="font-3" style="font-size: 22px;">
                                <span class="" style="color: #000;">Pick List:</span>
                                <span id="pick_status"> 0/0</span>
                                <span id="cycle_span" style="display: none;">
                                    <span class="" style="color: black;">Cycle:</span>
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
                    <div class="row m-0 p-0 finish-box">
                        <div class="col-md-12 align-items-center" style="display: flex; height: 40px; text-align: center">
                            <div class="text-center" style="width: 100%; font-size: 24px; font-weight: 600;">
                                <a href="logout.php" style="color: #FFF;"><?php
                                                                            echo $_SESSION['user']['username'] ?>:
                                    Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <?php
        include("footer.php"); ?>
    </div>
    <input type="hidden" id="kanban_id" value="0">
    <input type="hidden" id="is_help" value="0">
    <input type="hidden" id="status" value="pick">
    <input type="hidden" id="barcode" value="ALL">

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
                        if ($user['type'] == 1) {
                            echo '<option value="' . $user['user_id'] . '">'
                                . $user['username'] . '</option>';
                        }
                    }
                    echo '</select>';
                    echo '<br>';
                    echo '<input type="checkbox" id="complete_skip" name="complete_skip">';
                    echo '<label for="complete_skip"> &nbsp; Complete / Skip</label><br>';
                    echo '<div class="reason" style="display:none">';
                    echo '<label> Reason</label><br>';
                    echo '<select class="form-control" id="confirm_reason" name="confirm_reason">';
                    foreach ($reasons as $item) {
                        echo '<option value="' . $item->id . '">' . $item->name
                            . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    ?>
                    <input type="hidden" id="confirm_help_alarm_id">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" id="confirm_help_with_user" style="width: 160px;">OK
                    </button>
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

            $(document).on('click', "#complete_skip", function() {
                toggleReason();
            });

            $("#pick_date").datetimepicker({
                format: 'DD/MM/YYYY',
                icons: {
                    previous: 'fas fa-angle-left',
                    next: 'fas fa-angle-right',
                }
            });

            read_kanban_box();
            read_pick_list();

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
                var checked = $("#complete_skip").is(':checked');
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
                    assert_equals(request_counter + 5, event_counter,
                        "Each requestPointerLock() will fire a pointerlockchange event");
                });

                lock_log.innerHTML = "Status: Test over.";

                repeatLockPointerTest.done();
            }

            function LockTarget() {
                locktarget.requestPointerLock();
            }


            //Read kanban box
            function read_kanban_box() {
                var pick_date = $("#pick_date").val();
                var kanban_id = $("#kanban_id").val();
                var status = $("#status").val();
                var cycle = $("#cycle_select").val() ? $("#cycle_select").val() : -1;
                var zone = $("#zone_select").val() ? $("#zone_select").val() : -1;
                var zone_text = $("#zone_select").val() ? $("#zone_select").val() : "All";
                // $("#current_kanban").text("");
                // $("#current_kanban_part_number").hide();

                // alert(kanban_id)
                // alert(cycle);

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
                    console.log(result);
                    var zones = result.zones;
                    html = '<option value=0 selected>All</option>';
                    for (var i = 0; i < zones.length; i++) {
                        html +=
                            `<option value="${zones[i]['dolly']}" ${zone == zones[i]['dolly'] ? "selected" : ""}>${zones[i]['dolly']}</option>`
                    }
                    html += `<option value="Help" ${zone == "Help" ? "selected" : ""}>Help</option>`;
                    $("#zone_span").css("display", "inline-block")
                    $("#zone_select").html(html)

                    if (result.max_cycle && result.max_cycle != -1) {
                        if (!result.cur_cycle)
                            result.cur_cycle = result.max_cycle
                        var html = "";
                        for (var i = 1; i <= result.max_cycle; i++) {
                            html +=
                                `<option value="${i}" ${result.cur_cycle == i ? "selected" : ""}>${i}/${result.max_cycle}</option>`
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
                        $("#current_kanban_id").val(result.kanban_id);
                        if (result.part_number)
                            var current_kanban_html =
                                '<span style="color: #fa0000; font-size: 15px;">Part No:</span><span style="color: #FFF; font-size: 30px;">' +
                                result.part_number + '</span>'
                        else
                            var current_kanban_html =
                                '<span style="color: #fa0000; font-size: 15px;">Part No:</span><span style="color: #FFF; font-size: 30px;"></span>'
                        if (result.is_completed == 1) {
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
                        if (result.is_help == 1 && result.is_completed == 0) {
                            // alert("is_help")
                            $("#is_help").val(result.is_help);
                            $(".finish-box").removeClass('blue-kanban');
                            $(".finish-box").removeClass('green-kanban');
                            $(".finish-box").removeClass('bg-green');
                            $(".finish-box").addClass('red-kanban');
                        }

                        if (result.is_help == 0 && result.is_completed == 0) {
                            $(".finish-box").removeClass('green-kanban');
                            $(".finish-box").removeClass('red-kanban');
                            $(".finish-box").removeClass('bg-green');
                            $(".finish-box").addClass('blue-kanban');
                        }

                        // if (result.is_help == 0 && result.is_completed == 1) {
                        if (result.is_completed == 1) {
                            $(".finish-box").removeClass('blue-kanban');
                            $(".finish-box").removeClass('red-kanban');
                            // $(".finish-box").removeClass('bg-green');
                            $(".finish-box").addClass('green-kanban');
                        }
                        check_finish();
                        read_pick_list();
                    }
                });
            }

            function check_zone_finish(kanban_id) {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'check_zone_finish',
                        kanban_id: kanban_id,
                        cycle: $("#cycle_select").val() ? $("#cycle_select").val() : -1
                    },
                    dataType: 'JSON',
                    cache: false
                }).done(function(result) {
                    let zone_count = parseInt(result.zone_count);
                    if (zone_count === 0) {
                        $("#zone_error_msg").show();
                    } else {
                        $("#zone_error_msg").hide();
                    }
                    setTimeout(function() {
                        $("#zone_error_msg").hide();
                    }, 5000)
                });
            }

            //Kanban List
            function read_pick_list() {
                var pick_date = $("#pick_date").val();
                var cycle = $("#cycle_select").val() || 1;
                var zone = $("#zone_select").val()
                var current_kanban_id = $("#current_kanban_id").val();
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'read_pick_list',
                        pick_date: pick_date,
                        status: 'pick',
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
                $(".finish-box").removeClass('bg-green');
                var cycle = $("#cycle_select").val();
                var current_zone = $('#zone_select option:selected').val();
                var zone = $('#zone_select option:selected').next().val();
                if (current_zone == 0 || zone === undefined) {
                    $("#cycle_select").val(parseInt(cycle) + 1);
                    $('#zone_select option:selected').val(0);
                    read_kanban_box();
                } else {
                    $('#zone_select option:selected').next().attr('selected', 'selected');
                    read_kanban_box();
                }
            }

            function toNextCycle() {
                $(".finish-box").removeClass('bg-green');
                var cycle = $("#cycle_select").val();
                $("#cycle_select").val(parseInt(cycle) + 1);
                read_kanban_box();
            }

            function playAudio(file) {
                // alert("playAudio");
                var audio = new Audio(file);
                audio.play();
            }

            //Check finish button
            function check_finish() {
                var pick_date = $("#pick_date").val();
                var status = $("#status").val();
                var cycle = $("#cycle_select").val() ? $("#cycle_select").val() : -1
                var zone = $("#zone_select").val() != 0 ? $("#zone_select").val() : -1
                
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'check_pick_finish',
                        pick_date: pick_date,
                        status: status,
                        cycle: cycle,
                        zone: zone
                    },
                }).done(function(result) {
                    if (result.includes('success')) {
                        $("#btn_finish").attr("href", "###")
                        $("#btn_finish").one("click", toNext)
                        $(".finish-box").addClass('bg-green');
                    }

                    if (result.includes('finish')) {
                        $(".finish-box").addClass('bg-green');
                        $("#btn_finish").attr('href', 'logout.php');
                        $(".finish-box").addClass('bg-green');
                    }

                    if (result.includes('in_progress')) {
                        $("#btn_finish").attr('href', '#');
                        $("#btn_finish").removeClass('bg-green');
                        $("#btn_finish").addClass('bg-blue');
                    }

                    if (result.includes('in_help')) {
                        $("#btn_finish").attr('href', '#');
                        $("#btn_finish").addClass('bg-blue');
                        $("#btn_finish").removeClass('bg-green');
                    }
                });
            }

            async function check_finish_item(cycle, zone) {
                var pick_date = $("#pick_date").val();
                var status = $("#status").val();
                // console.log("status: >>>", status);
                const res = await $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'check_pick_finish',
                        pick_date: pick_date,
                        status: status,
                        cycle: cycle,
                        zone: zone
                    },
                });
                if (res.includes('success')) {
                    return true;
                } else {
                    return false;
                }
            }

            function conveyance_pick(data = {}) {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: data,
                    cache: false
                }).done(function(result) {
                    $("#kanban_input").val('');
                    $("#kanban_input").focus();
                    if (result == 'ok') {
                        read_kanban_box();
                        $(".finish-box").addClass("green-kanban").removeClass("blue-kanban").removeClass(
                            "red-kanban");
                        playAudio('assets/audio/Kanban_success.wav');
                    } else {
                        playAudio('assets/audio/Incorrect_kanban_entered_please_try_again.wav');
                    }
                    check_zone_finish(data.kanban_id);
                });
            }

            $('#pick_date').on('dp.change', function(e) {
                $("#cycle_span").css("display", "none")
                $("#cycle_select").empty()
                $("#zone_span").css("display", "none")
                $("#zone_select").empty()
                read_kanban_box();
            });

            var help_pressed = false;
            $(document).on('click', '#andon_help', function() {
                if ($("#current_kanban_id").length == 0 || $("#current_kanban_id").val() == '')
                    return false;
                if (!help_pressed) {
                    playAudio('assets/audio/Andon_help_called_awaiting_team_leader.wav');
                    help_pressed = true;
                }
				
				help_pressed = false;

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
                            status: 'pick',
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
                    $("#complete_skip").prop("checked", false);
                    toggleReason();
                    playAudio('assets/audio/Please_select_reason_for_andon.wav');
                    $("#confirm_user_modal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                }

                //$("#kanban_input").val($("#current_kanban").text())
            });

            $(document).on('keypress', "#kanban_input", async function(e) {
                if (e.keyCode == 13) {
                    var value = $(this).val().toUpperCase();
                    // console.log("input value =>>>", value);
                    $("#kanban_id").val(0);
                    var kanban_no = $("#current_kanban").text();
                    var address = $("#current_address").text();
                    var part_num = $("#current_kanban_part_num").val();
                    var user = "<?php echo $_SESSION['user']['user_id']; ?>";
                    var is_pick = "1";

                    const today = new Date();
                    const yyyy = today.getFullYear();
                    let mm = today.getMonth() + 1; // Months start at 0!
                    let dd = today.getDate();
                    let hr = today.getHours();
                    let min = today.getMinutes();
                    let sec = today.getSeconds();
                    const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;
                    if (kanban_no != '' || address != '') {
                        var kanban_id = $("#current_kanban_id").val();
                        if (value == kanban_no || value == address) {
                            // console.log("test1");
                            conveyance_pick({
                                kanban_id: kanban_id,
                                action: "conveyance_pick",
                                user: user,
                                today: formattedToday,
                                reason: 0,
                                is_pick
                            });
                        } else {
                            // console.log("test2");
                            const today = new Date();
                            const yyyy = today.getFullYear();
                            let mm = today.getMonth() + 1; // Months start at 0!
                            let dd = today.getDate();
                            let hr = today.getHours();
                            let min = today.getMinutes();
                            let sec = today.getSeconds();
                            const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;

                            value1 = value
                            value1 = value1.replace(/-/g, "");

                            if (value1 == part_num || value1 == kanban_no) {
                                // console.log("test3");
                                conveyance_pick({
                                    kanban_id: kanban_id,
                                    action: "conveyance_pick",
                                    user: user,
                                    today: formattedToday,
                                    reason: 0,
                                    is_pick
                                });
                            } else {
                                // console.log("test4");
                                const today = new Date();
                                const yyyy = today.getFullYear();
                                let mm = today.getMonth() + 1; // Months start at 0!
                                let dd = today.getDate();
                                let hr = today.getHours();
                                let min = today.getMinutes();
                                let sec = today.getSeconds();
                                const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;

                                if (value1.search(part_num) != -1 || value1.search(kanban_no) != -1) {
                                    // alert("222")
                                    // console.log("test5");
                                    conveyance_pick({
                                        kanban_id: kanban_id,
                                        action: "conveyance_pick",
                                        today: formattedToday,
                                        user: user,
                                        reason: 0,
                                        is_pick
                                    });
                                } else {
                                    // console.log("test6");
                                    value1 = value1 == 'ALL' ? '0' : value1;
                                    let selectElement = document.querySelectorAll('[name=zone_select]');
                                    let optionValues = [...selectElement[0].options].map(o => o.value.toUpperCase());
                                    let optionValues_old = [...selectElement[0].options].map(o => o.value);
                                    if (optionValues.includes(value1)) {
                                        const search_func = (element) => element == value1;
                                        var found_index = optionValues.findIndex(search_func);
                                        $("#zone_select").val(optionValues_old[found_index]);
                                        // read_kanban_box();
                                        $("#kanban_input").val('');
                                        playAudio('assets/audio/Zone_selected.wav');
                                    } else {
                                        // console.log("check ajax =>>>>", "check");
                                        $.ajax({
                                            url: "actions.php",
                                            method: "post",
                                            cache: false,
                                            data: {
                                                action: "search_kanban",
                                                kanban: value,
                                                pick_date: $("#pick_date").val(),
                                                cycle: $("#cycle_select").val() ? $("#cycle_select").val() : -1
                                            },
                                        }).done(function(result) {
                                            $("#kanban_input").val('');
                                            $("#kanban_input").focus();
                                            if (result !== 'failure') {
                                                const data = JSON.parse(result);
                                                let kanban_id = data.id;
                                                $("#cycle_select").val(parseInt(data.cycle));
                                                $('#zone_select option:selected').val(data.dolly);
                                                $("#button_dolly").text(data.dolly);
                                                read_kanban_box();
                                                if (!parseInt(data.is_completed)) {
                                                    conveyance_pick({
                                                        kanban_id: kanban_id,
                                                        action: "conveyance_pick",
                                                        user: user,
                                                        today: formattedToday,
                                                        reason: 0,
                                                        is_pick: is_pick,
                                                    });
                                                }
                                            } else {
                                                playAudio('assets/audio/Incorrect_kanban_entered_please_try_again.wav');
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    }
                    // check finish
                    if (found_index) {
                        var cycle = $("#cycle_select").val() ? $("#cycle_select").val() : -1;
                        var zone = $("#zone_select").val() != 0 ? $("#zone_select").val() : -1;
                        var check_cycle = cycle - 1;
                        var before_cycle = false;
                        var j = 1;
                        while (j <= check_cycle) {
                            const res = await check_finish_item(j, zone);
                            if (res) {
                                j++;
                            } else {
                                $(".finish-box").removeClass('bg-green');
                                before_cycle = true;
                                $("#cycle_select").val(j);
                                
                                read_kanban_box();

                                j = check_cycle + 1;
                            }
                        }
                        if (!before_cycle) {
                            var all_cycles = $("#cycle_select option").length;
                            var i = cycle;
                            while (i < all_cycles) {
                                const res = await check_finish_item(i, zone);
                                if (res) {
                                    i++;
                                } else {
                                    $(".finish-box").removeClass('bg-green');
                                    $("#cycle_select").val(i);
                                    
                                    read_kanban_box();

                                    i = all_cycles;
                                }
                            }
                        }   
                    }
                }
            });

            $(document).on('click', '#btn_finish', function() {
                var href = $(this).attr('href');
                if (href == '#') {
                    playAudio('assets/audio/Kanbans_still_outstanding_can’t_finish_cycle.wav');
                    alert("You didn't complete all kanban");
                    // alert('You can not finish before complete all kanban');
                    return false;
                }
                playAudio('assets/audio/Cycle_complete.wav');
            });

            $(document).on('click', '.select-kanban', function() {
                var kanban_id = $(this).attr('data-kanban');
                // alert(kanban_id);
                $("#kanban_id").val(kanban_id);
                $("#current_kanban_id").val(kanban_id);
                read_kanban_box();
            });

            $("#confirm_help_with_user").on('click', function() {
                if ($("#current_kanban_id").length == 0 || $("#current_kanban_id").val() == '')
                    return false;
                var kanban_id = $("#current_kanban_id").val();
                var confirm_user_id = $("#confirm_user_id").val();
                var complete_skip = $('#complete_skip').is(':checked') ? 1 : 0;
                var reason_val = $("#confirm_reason").val();
                var reason = complete_skip == 1 ? reason_val : 0;
                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();

                let hr = today.getHours();
                let min = today.getMinutes();
                let sec = today.getSeconds();
                const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "confirm_conveyance_andon_help",
                        confirm_user_id: confirm_user_id,
                        is_completed: complete_skip,
                        reason: reason,
                        kanban_id: kanban_id,
                        today: formattedToday,
                    },
                    dataType: 'HTML',
                }).done(function(html) {
                    $("#confirm_user_modal").modal('hide');
                    $(".help-box").removeClass('bg-red');
                    $("#btn_finish").removeClass('bg-red');
                    //$("#kanban_area").addClass('blue-kanban');
                    $("#kanban_area").removeClass('red-kanban');
                    $("#is_help").val('0');
                    // read_pick_list();
                    $("#kanban_id").val(0);
                    if (complete_skip == 1) {
                        playAudio('assets/audio/kanban_complete.wav');
                    }

                    read_kanban_box();
                    check_finish();
                });
            });
            $("#cycle_select").on("change", function() {
                read_kanban_box();
            })
            $("#zone_select").on("change", function() {
                read_kanban_box();
                playAudio('assets/audio/Zone_selected.wav');
            })
        });
    </script>
</body>

</html>