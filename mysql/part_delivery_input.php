<?php
require_once ("config.php");
require_once ("functions.php");
$page_name = "Part Delivery Driver";
$_SESSION['page'] = 'part_delivery_input.php';
login_check();
require_once ("assets.php");
?>

<style>
    .help-andon {
        font-size: 14px;
    }
    @media screen and (min-width: 600px) {
        .help-andon {
            font-size:24px;
        }
    }
    @media screen and (min-width: 1024px) {
        .help-andon {
            font-size:36px;
        }
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
        background-color: #007bff;
        color: #FFF;
    }
    .grey-kanban {
        background-color: #E6E7E6;
        color: #FFF;
    }
    .col-md-12.align-items-center {
        position: fixed;
        bottom: 0px;
        background: #1797ff;
        width: 92%;
    }
    @keyframes red_flash {
        from {background-color: red;}
        to {background-color: red;}
    }
</style>

<body class="hold-transition sidebar-collapse layout-top-nav" onload="startTime()">
<div class="wrapper">
    <?php include("header.php"); ?>
    <?php include("menu.php"); ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="row mx-auto justify-content-center">
                        <input type="text" class="form-control" 
                                style="width: 20vw; display: inline-block; border-radius: 15px; text-transform: uppercase;" autofocus placeholder="INPUT" id="zone_input">
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12" id="driver_member_screen">
                        <div class="text-center">
                            <p class="font-weight-bold d-none" style="color:white; font-size: 6vw" id="label_help_called">HELP CALLED</p>
                        </div>
                        <div class="text-center">
                            <p class="h2 font-weight-bold"><?php
                                echo $_SESSION['user']['username'];
                            ?>
                            </p>
                        </div>
                        <div class="row mx-2 my-2 justify-content-center" style="border-radius: 15px;">
                            <div class="row justify-content-center" id="svg_area">
                                
                            </div>                            
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-2 my-2 border text-center p-1" style="border-radius: 15px;">
                                <p class="h6 font-weight-bold mt-1">BUILD/CYCLE</p>
                                <p style="font-size: 7vw; font-weight: bold; margin: 0px 0px 0px 0px;" id="build_cycle_count"></p>
                                <p style="font-size: 24px; font-weight: bold;" id="build_cycle_cycle"></p>
                            </div>
                        </div>
                        <div class="row justify-content-center my-2">
                            <button class="btn border" style="background-color:red ;width: 25vw; height: 7vh; border-radius: 15px;" id="btn_help_andon"><p class="help-andon h2 font-weight-bold text-white">HELP ANDON</p></button>
                        </div>
                    </div>
                </div>
                <div class="row m-2 p-0 blue-kanban">
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
    <?php include ("footer.php"); ?>
</div>

<div class="d-none" id="help_alarm_area">
</div>

<input type="hidden" id="driver_part" value="0">
<input type="hidden" id="cycle_value" value="0">
<input type="hidden" id="current_zone" value="1">
<input type="hidden" id="help_pressed" value="0">
<input type="hidden" id="is_initial" value="1">
<input type="hidden" id="allow_change_text" value="1">
<input type="hidden" id="zone1_base_build_amount" value="">
<input type="hidden" id="truck_step_counter" value="0">

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/js/adminlte.min.js"></script>
<script src="assets/js/custom.js"></script>
<script>
    $(document).ready(function() {
        $(document).keypress(function (event) {  
                // $("#zone_input").focus();
        });

        var move_counter = -1;
        var entered_scan = false;
        $(document).on('keypress', "#zone_input", function(e) {
            if (e.keyCode == 13) {
                entered_scan = true;                
                var value = $(this).val();
                if(value.toUpperCase().replace(" ","") == "ZONE4") {
                    $("#is_initial").val("1");
                    $("#current_zone").val("1");
                    reset_zone();
                    get_prev_next_B_C("ZONE1");
                }
                else {
                    if($("#is_initial").val("0") != "0")
                        get_prev_next_B_C(value);
                }
                // build_cycle_svg_area();
                $("#zone_input").val('');
            }
        });

        function reset_zone() {
            $("#c1").attr("fill", "gray");
            $("#prev_zone").attr("fill", "gray");
            $("#prev_b").attr("fill", "gray");
            $("#prev_c").attr("fill", "gray");
            $("#prev_zone").text(`ZONE ${1}`);
            $("#prev_b").text(``);
            $("#prev_c").text(``);
            $("#next_zone").text(`ZONE ${1+1}`);
            $("#next_b").text(``);
            $("#next_c").text(``);
            $("#truck_step_counter").val(0);
        }

        function get_prev_next_B_C(zone) {
            
            const today = new Date();
            var hh_now = today.getHours();
            var mm_now = today.getMinutes();
            var ampm_now = hh_now > 12 ? "PM" : "AM";
            hh_now = hh_now % 12;
            hh_now = hh_now < 10 ? `0${hh_now}` : hh_now;
            mm_now = mm_now < 10 ? `0${mm_now}` : mm_now;

            value = zone;
            var value1 = value.replace(/ /g,'').toUpperCase();
            var zone_no = parseInt(value1.split("ZONE")[1]);

            var mm = ~~(pause_counter / 60);
            mm = mm < 10 ? "0" + mm : mm;
            var ss = pause_counter % 60;
            ss = ss < 10 ? "0" + ss : ss;
            var paused_time = pause_counter > 0 ? `${mm}:${ss}` : ' ';

            if (value != "" &&  value.toUpperCase().includes("ZONE")) {                
                if(zone_no < 1 || zone_no > 3 || isNaN(zone_no)) {
                    if(entered_scan)
                        alert("Wrong zone input");
                    return;
                }

                var current_zone_no = parseInt($("#current_zone").val().split("ZONE")[1]);
                if(zone_no < current_zone_no)
                    return;

                

                move_counter = 0;
                $("#c1").attr("fill", "green");
                $("#prev_zone").attr("fill", "green");
                $("#prev_b").attr("fill", "green");
                $("#prev_c").attr("fill", "green");
                
                if($("#prev_b").text() != '')
                    $("#allow_change_text").val("0");
                if($("#current_zone").val() != value1) // if zone changed
                    $("#allow_change_text").val("1");

                $("#current_zone").val(value1);
                var is_zone4_or_initial = $("#is_initial").val() =="1" && zone_no == 1;
                console.log("is_zone4_or_initial: ", is_zone4_or_initial);
                if($("#allow_change_text").val() == "1")
                {
                    if(!is_zone4_or_initial) 
                    {
                        if($("#zone1_base_build_amount").val() == ""){                            
                            $("#zone1_base_build_amount").val($("#build_cycle_count").text());
                        }

                        var cycle = $("#cycle_value").val();
                        var build_count = parseInt($("#zone1_base_build_amount").val());//parseInt($("#build_cycle_count").text());                        
                        var build_cycle = parseInt($("#build_cycle_cycle").text().split("CYCLE")[1]);
                        var driver_part = $("#driver_part").val();
                        var prev_b = build_count + driver_part * (zone_no - 1);
                        var prev_c = Math.ceil(prev_b / cycle);
                        $("#prev_zone").text(`ZONE ${zone_no}`);
                        $("#prev_b").text(`B: ${prev_b}`);
                        $("#prev_c").text(`C: ${prev_c}`);
                        var next_b = build_count + driver_part * zone_no;
                        var next_c = Math.ceil(next_b / cycle);
                        $("#next_zone").text(`ZONE ${zone_no+1}`);
                        $("#next_b").text(`B: ${next_b}`);
                        $("#next_c").text(`C: ${next_c}`);
                    } else {
                        if($("#zone1_base_build_amount").val() != ""){
                            $("#zone1_base_build_amount").val($("#build_cycle_count").text());
                            var cycle = $("#cycle_value").val();
                        var build_count = parseInt($("#zone1_base_build_amount").val());//parseInt($("#build_cycle_count").text());                        
                        var build_cycle = parseInt($("#build_cycle_cycle").text().split("CYCLE")[1]);
                        var driver_part = $("#driver_part").val();
                        var prev_b = build_count + driver_part * (zone_no - 1);
                        var prev_c = Math.ceil(prev_b / cycle);
                        $("#prev_zone").text(`ZONE ${zone_no}`);
                        $("#prev_b").text(`B: ${prev_b}`);
                        $("#prev_c").text(`C: ${prev_c}`);
                        var next_b = build_count + driver_part * zone_no;
                        var next_c = Math.ceil(next_b / cycle);
                        $("#next_zone").text(`ZONE ${zone_no+1}`);
                        $("#next_b").text(`B: ${next_b}`);
                        $("#next_c").text(`C: ${next_c}`);
                        }
                    }
                    $("#truck_step_counter").val("0");
                    // move_truck(0);
                    // $("#truck_step_counter").val(0);
                } 
                else 
                {
                    var build_count = parseInt($("#build_cycle_count").text());
                    var driver_part = $("#driver_part").val();
                    var prev_b = parseInt($("#prev_b").text().split(" ")[1]);
                    var step_counter = build_count - prev_b;                        
                    if(step_counter <= driver_part)
                        // move_truck(step_counter);
                        $("#truck_step_counter").val(step_counter);
                    else
                        $("#truck_step_counter").val(driver_part);
                }           

                if(is_zone4_or_initial) // if zone4
                {                    
                    reset_zone();
                }
                else {
                    $("#is_initial").val("0");
                    move_truck();
                }

                
               
               var base_target = $("#zone1_base_build_amount").val();
               if(base_target != '')
                    console.log("=== Set_driver_member =====: ", base_target);

                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            action: 'set_driver_member',
                            current_zone: zone_no,
                            now: `${hh_now}:${mm_now} ${ampm_now}`,
                            zone_background: zone_background,
                            zone_red_status: zone_red_status,
                            paused_time: paused_time,
                            is_initial: $("#is_initial").val(),
                            truck_step_counter: $("#truck_step_counter").val(),
                            base_target: base_target,
                        },
                        dataType: 'JSON',
                    }).done(function(response) {

                    });

            } else {
                if(entered_scan)
                    alert("Wrong zone input");
                return false;
            }            
        }

        
        function build_cycle_svg_area() { // Get remaining cycles, build/cycle,
            var svg_html = '';
            if($(window).width() <= 605) { //Small Screen
                svg_html = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600" height="200">
                                    <g transform="matrix(1.3333334 0 0 1.3333334 0 0)">                                        
                                        <circle id="c1" cx="110" cy="30" r="15" stroke="black" stroke-width="0" fill="gray" />
                                        <circle id="c2" cx="340" cy="30" r="15" stroke="black" stroke-width="0" fill="gray" />
                                        <image id="truck" x="90" y="11" xlink:href="data:image/png;base64,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" preserveAspectRatio="none" width="40" height="20"/>
                                        <line x1="130" y1="32" x2="320" y2="32" style="stroke:#027847;stroke-width:3" />
                                        <text x="95" y="10" fill="black" font-size="8px" font-weight="bold">PREV / LOCATION</text>
                                        <text x="95" y="55" fill="gray" font-size="10px" font-weight="bold" id="prev_zone">ZONE 1</text>
                                        <text x="100" y="75" fill="gray" font-size="10px" font-weight="bold" id="prev_b"></text>
                                        <text x="100" y="90" fill="gray" font-size="10px" font-weight="bold" id="prev_c"></text>
                                        <text x="280" y="10" fill="black" font-size="8px" font-weight="bold">NEXT / LOCATION</text>
                                        <text x="315" y="55" fill="gray" font-size="10px" font-weight="bold" id="next_zone">ZONE 2</text>
                                        <text x="320" y="75" fill="gray" font-size="10px" font-weight="bold" id="next_b"></text>
                                        <text x="320" y="90" fill="gray" font-size="10px" font-weight="bold" id="next_c"></text>
                                    </g>
                                </svg>`;
                
            } else {
                svg_html = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600" height="200">
                                    <g transform="matrix(1.3333334 0 0 1.3333334 0 0)">                                        
                                        <circle id="c1" cx="50" cy="65" r="20" stroke="black" stroke-width="0" fill="gray" />
                                        <circle id="c2" cx="400" cy="65" r="20" stroke="black" stroke-width="0" fill="gray" />
                                        <image id="truck" x="22" y="38" xlink:href="data:image/png;base64,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" preserveAspectRatio="none" width="52" height="25"/>
                                        <line x1="75" y1="65" x2="375" y2="65" style="stroke:#027847;stroke-width:5" />
                                        <text x="0" y="30" fill="black" font-size="10px" font-weight="bold">PREV / LOCATION</text>
                                        <text x="20" y="105" fill="gray" font-size="14px" font-weight="bold" id="prev_zone">ZONE 1</text>
                                        <text x="20" y="125" fill="gray" font-size="14px" font-weight="bold" id="prev_b"></text>
                                        <text x="20" y="145" fill="gray" font-size="14px" font-weight="bold" id="prev_c"></text>
                                        <text x="350" y="30" fill="black" font-size="10px" font-weight="bold">NEXT / LOCATION</text>
                                        <text x="370" y="105" fill="gray" font-size="14px" font-weight="bold" id="next_zone">ZONE 2</text>
                                        <text x="370" y="125" fill="gray" font-size="14px" font-weight="bold" id="next_b"></text>
                                        <text x="370" y="145" fill="gray" font-size="14px" font-weight="bold" id="next_c"></text>
                                    </g>
                                </svg>`
            }

            
            if($("#allow_change_text").val() == "1")
                $("#svg_area").html(svg_html);

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
                
                var driver_part = response.driver;
                var count = response.count;
                var cycle = response.cycle;
                $("#cycle_value").val(cycle);
                var cycle_str = "CYCLE " + Math.ceil(count / cycle);                
                $("#build_cycle_count").text(count);
                $("#build_cycle_cycle").text(cycle_str);
                $("#driver_part").val(driver_part);
                
                var zone = $("#zone_input").val();
                if(zone == "" && response.driver_current_zone != undefined) {                    
                    zone = `Zone ${response.driver_current_zone}`;
                }
                
                {
                
                    get_prev_next_B_C(zone);
                }
            });
        }

        $("#btn_help_andon").on('click', function() {
            if($("#help_pressed").val() != "1") {
                $("#help_pressed").val("1");
                $("#driver_member_screen").attr("style", "background-color:red");
                $("#label_help_called").text('HELP CALLED');
                $("#label_help_called").removeClass("d-none");
                zone_background = "red";
                zone_red_status = "HELP ANDON";
                counter = 0;

                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: "set_help_alarm",
                        page: 'Driver'
                    },
                }).done(function(html) {
                    $("#help_alarm_area").html(html);
                });
             }else {
                    $("#help_pressed").val("0");
                    $("#driver_member_screen").attr("style", "background-color:white");
                    $("#label_help_called").addClass("d-none");
                    zone_background = "white";
                    zone_red_status = "";
                    counter = 0;

                    var alarm_id = $("#help_alarm_id").val();
                    $.ajax({
                        url: "actions.php",
                        method: "post",
                        data: {
                            action: "confirm_help_alarm",
                            confirm_user_id: "<?php echo $_SESSION['user']['user_id'];?>",
                            alarm_id: alarm_id
                        },
                        dataType: 'HTML',
                    }).done(function(html) {
                        // $("#confirm_user_modal").modal('hide');
                        // $(".devan-help").removeClass('bg-red');
                        // $(".devan-help").addClass('bg-yellow');
                    });
                }
            get_prev_next_B_C($("#current_zone").val());
            
        });


        var counter = 0, pause_counter = 0;
        var first_amount = 0;
        var zone_background = "white";
        var zone_red_status = "";

        function get_build_amount() {
            
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
                var build_amount = response.count;

                var target = parseInt($("#next_b").text().split(" ")[1]);                
                if(build_amount > target) { // support help
                    // console.log("RRRRRRRR: ", build_amount, target);
                    // $("#driver_member_screen").attr('style', 'animation: red_flash 2s infinite');
                    // $("#label_help_called").removeClass("d-none");
                    $("#label_help_called").text('SUPPORT');
                    zone_background = "red";
                    zone_red_status = "SUPPORT";
                    get_prev_next_B_C($("#current_zone").val());
                } else {
                    if($("#help_pressed").val() != "1") {
                        $("#label_help_called").text('');
                        zone_background = "white";
                        zone_red_status = "";
                        get_prev_next_B_C($("#current_zone").val());
                    }
                }

                if(counter == 0)
                    first_amount = build_amount;
                var pause_time_setting_value = "<?php echo get_setting('pause_time'); ?>";
                pause_time_setting_value = parseInt(pause_time_setting_value);
                if(counter >= pause_time_setting_value && build_amount == first_amount && $("#help_pressed").val() != "1") { // bigger than 90s
                    $("#driver_member_screen").attr('style', 'animation: red_flash 2s infinite');
                    $("#label_help_called").removeClass("d-none");
                    var mm = ~~(pause_counter / 60);
                    mm = mm < 10 ? "0" + mm : mm;
                    var ss = pause_counter % 60;
                    ss = ss < 10 ? "0" + ss : ss;
                    $("#label_help_called").text(`PAUSED: ${mm}:${ss}`);
                    zone_background = "red";
                    zone_red_status = "PAUSED";
                    get_prev_next_B_C($("#current_zone").val());
                    pause_counter ++;
                }
                if(build_amount != first_amount) {
                    first_amount = build_amount;
                    counter = 0;
                    pause_counter = 0;
                    $("#help_pressed").val("0");                    
                    build_cycle_svg_area();
                    $("#driver_member_screen").attr('style', 'background-color: white;');
                    $("#label_help_called").text(`PAUSED: 00:00`);
                    $("#label_help_called").addClass("d-none");
                    zone_background = "white";
                    zone_red_status = "";
                    get_prev_next_B_C($("#current_zone").val());
                    
                }
                counter ++;
            });
            
            // if(counter > 90) counter = 0;
        }        
        
        function move_truck() {
            var step_counter = parseInt($("#truck_step_counter").val());
            
            if (step_counter < 0) return;
            var parts = parseInt($("#driver_part").val());            
            if($(window).width() <= 605) { //Small Screen
                var step = 230 / parts; // move step for truck
                $("#truck").attr("x", 90 + step * step_counter);
            }
            else {
                var step = 350 / parts; // move step for truck
                $("#truck").attr("x", 22 + step * step_counter);
            }
            step_counter = (step_counter + 1) % (parts + 1);
            
        }

        build_cycle_svg_area();
        setInterval(get_build_amount, 1 * 1000); //update every 1 second
        // setInterval(move_truck, 2 * 1000); //update every 1 second

    });
</script>
</body>
</html>
