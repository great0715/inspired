<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Conveyance Pick";
$_SESSION['page'] = 'conveyance_pick.php';
login_check();
// require_once("assets.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /*header*/
    .header {
        width: 100%;
        height: 30px;
        padding: 20px 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #eaecee;
        color: #063c49;
        font-weight: bold;
    }

    .header .date {
        width: 15%;
    }

    .header .build_amount {
        width: 35px
    }

    .header_input::placeholder {
        text-align: start;
        text-indent: 5%;
    }

    input::placeholder {
        color: #063c49;
        font-weight: bold;
        text-align: center;
    }

    /** container-component */

    .container {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    /* day */
    .day {
        color: #063c49;
        font-weight: bold;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        padding: 10px;
    }

    .dayshift {
        font-size: 40px;
        font-family: sans-serif;
    }

    .box {
        display: flex;
        justify-content: space-around;
        text-align: center;
    }

    .box_1 {
        margin-right: 20px;
    }

    .square_box {
        display: flex;
        justify-content: space-between;
    }

    .square_box label {
        font-size: 12px;
    }

    .col {
        border: 1px solid gray;
        width: 80px;
        margin: 5px
    }

    .col .time {
        font-size: 25px;
    }

    /* content */
    .content {
        width: 100%
    }

    table {
        border-collapse: collapse;
        text-align: center;
    }

    tr,
    th,
    td {
        border: 1px solid black;
        padding: 10px 20px;
    }

    .left_side {
        display: block;
        width: 47%;
    }
</style>

<body onload="showTime()">
    <div class="header">
        <script>
            function showTime() {
                var date = new Date();
                var hours = date.getHours();
                var minutes = date.getMinutes();
                var seconds = date.getSeconds();
                var ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                var time = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
                document.getElementById('present-time').innerHTML = time;
                setTimeout(showTime, 1000);
            }
        </script>
        <span id="present-time"></span>
        <div>
            <label for="BUILD AMOUNT">BUILD AMOUNT</label>
            <input name="BUILD AMOUNT" type="text" class="build_amount" placeholder="">
        </div>
        <input type="text" placeholder="INPUT" class=header_input>
        <div class="row m-0 p-0 finish-box">
            <div class="col-md-12 align-items-center" style="display: flex; height: 40px; text-align: center">
                <div class="text-center" style="width: 100%; font-size: 24px; font-weight: 600;">
                    <a href="logout.php" style="color: #000;"><?php echo $_SESSION['user']['username'] ?>: Logout</a>
                </div>
            </div>
        </div>
        <button onclick="exportToExcel()"><img src="assets/img/report.png" alt="report" width="30px" height="30px" /></button>
        <script>
            function exportToExcel() {
                // Get the data element
                var data = document.getElementById("ExcelData");

                // Create an array of rows for the CSV file
                var rows = [];
                var dataRows = data.getElementsByTagName("tr");
                for (var i = 0; i < dataRows.length; i++) {
                    var row = [];
                    var dataCells = dataRows[i].getElementsByTagName("td");
                    for (var j = 0; j < dataCells.length; j++) {
                        row.push(dataCells[j].innerText);
                    }
                    rows.push(row);
                }

                // Combine the array of rows into a single CSV file
                var csv = "";
                for (var i = 0; i < rows.length; i++) {
                    csv += rows[i].join(",") + "\n";
                }

                // Create a link to download the CSV file as an Excel file
                var link = document.createElement("a");
                link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
                link.download = "data.csv";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        </script>
        <div>
            <label for="last_uploaded">Last Uploaded</label>
            <input type="date" name="last_upload">
        </div>
    </div>
    <div class="container" id="ExcelData">
        <div class="left_side">
            <div class="day">
                <div><Label class="dayshift">DAYSHIFT</Label>
                </div>
                <div class="box">
                    <div class="right_box box_1">
                        <div><label for="" class="title">Ave.Sets/Module</label></div>
                        <div class="square_box">
                            <div class="square_box1 col">
                                <label for="">TARGET</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                            <div class=" square_box2 col">
                                <label for="">LIVE</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                        </div>
                    </div>
                    <div class="right_box box_2">
                        <label for="" class="title">Ave.Sets/Module</label>
                        <div class="square_box">
                            <div class="square_box1 col">
                                <label for="">TARGET</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                            <div class=" square_box2 col">
                                <label for="">LIVE</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" id="table_data">
                <script>
                    let previousObj = [];
                    const rows = $("table tr");
                    rows.each(function(i, el) {
                        let obj = [];
                        $(el).children("td").each(function(ic, elc) {
                            obj.push(elc);

                            if (previousObj.length > ic) {
                                if (previousObj[ic].innerHTML == obj[ic].innerHTML) {
                                    $(previousObj[ic]).attr('rowspan', getRowsSpan(ic, i, obj[ic]
                                        .innerHTML));
                                    $(obj[ic]).remove();
                                }
                            }
                        });

                        previousObj = obj;
                    });

                    function getRowsSpan(col, row, value) {
                        var rowSpan = 2;
                        var actualRow = row + 1;

                        while ($(rows[actualRow]).children("td")[col].innerHTML == value) {
                            rowSpan++;
                            actualRow++;
                        }

                        return rowSpan;
                    }
                </script>
            </div>
        </div>
        <div class="left_side">
            <div class="day">
                <div><Label class="dayshift">NIGHTSHIFT</Label>
                </div>
                <div class="box">
                    <div class="right_box box_1">
                        <div><label for="" class="title">Ave.Sets/Module</label></div>
                        <div class="square_box">
                            <div class="square_box1 col">
                                <label for="">TARGET</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                            <div class=" square_box2 col">
                                <label for="">LIVE</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                        </div>
                    </div>
                    <div class="right_box box_2">
                        <label for="" class="title">Ave.Sets/Module</label>
                        <div class="square_box">
                            <div class="square_box1 col">
                                <label for="">TARGET</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                            <div class=" square_box2 col">
                                <label for="">LIVE</label><br>
                                <label for="" class="time">00:00</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" id="table_data"></div>
        </div>
    </div>

</body>

<script>
    function getShifts() {
        $.ajax({
            url: "actions.php",
            method: "post",
            data: {
                action: 'get_shifts'
            }
        }).then((res) => {
            $('#table_data').html(res);
        })
    }

    getShifts();
</script>

</html>