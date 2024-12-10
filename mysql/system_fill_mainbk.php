<?php
require_once("config.php");
require_once("functions.php");
$page_name = "System Fill Main";
$_SESSION['user']['page'] = 'conveyance_pick.php';
login_check();
require_once("assets.php");
?>
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
<script>
  let contArr_day = [];
  let modArr_day = [];
  let contArr_night = [];
  let modArr_night = [];
</script>
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
    position:fixed;
    top:6%;
  }

  .header .date {
    width: 15%;
  }

  .header .build_amount {
    width: 70px
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
  .day_container {
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
    width: 100%;
    padding-top:4% !important;
  }

  table {
    border-collapse: collapse;
    text-align: center;
  }

  tr,
  th,
  td {
    border: 1px solid black;
    padding: 10px 10px;
  }

  td p {
    margin: 0;
  }

  .left_side {
    display: block;
    width: 47%;
  }

  /* customize some tags */
  label {
    display: unset;
  }

  .bootstrap-datetimepicker-widget>table thead th {
    background: #fff;
  }
</style>


<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/moment/moment.min.js"></script>
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/js/adminlte.min.js"></script>
<script src="assets/js/custom.js"></script>
<!-- <script src="assets/js/websocket.js"></script> -->

<body class="hold-transition sidebar-collapse layout-top-nav" onload="startTime()">
  <div class="wrapper">
    <?php include("header.php"); ?>
    <?php include("menu.php"); ?>
    <?php include("latest_updated.php"); ?>
    <div class="content-wrapper">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">

              <div onload="showTime()">
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
                  <!-- <span id="present-time"></span> -->

                  <input class="form-control" type="text" id="date_picker" name="date_picker" value="<?php echo date('d-m-Y'); ?>" style="display: inline-block; width: 150px;">

                  <input type="text" placeholder="INPUT" class="form-control" style="width:250px;" id="cont_mod">
                  <div>
                    <label for="last_uploaded">Current Live Build Amount: &nbsp;</label>
                    <span id="live_build_value"></span>
                  </div>
                  <button onclick="exportToExcel()"><img src="assets/img/excel-download.png" alt="report" width="30px" height="30px" /></button>
                  <script>
                    function exportToExcel() {
                      var data = {
                        'day': getTableData('dayShiftTB'),
                        'night': getTableData('nightShiftTB')
                      };
                      $.ajax({
                        url: 'system_excel_export.php',
                        type: 'post',
                        data: data,
                        success: function(res) {
                          // Create a link to download the CSV file as an Excel file
                          var link = document.createElement("a");
                          link.href = './' + res;
                          link.download = res;
                          document.body.appendChild(link);
                          link.click();
                          document.body.removeChild(link);
                          deleteFile(res);
                        }
                      })
                    }
                  </script>
                  <div>
                    <label for="last_uploaded">Last Uploaded: &nbsp;</label>
                    <?php
                    if ($latest_updated === NULL) {
                      echo "No data";
                    } else {

                      if ($latest_updated["Update_time"]) {
                        $dateTime = explode(" ", $latest_updated["Update_time"]);
                        $dateObj = explode("-", $dateTime[0]);
                        echo  $dateObj[2] . "-" . $dateObj[1] . "-" . $dateObj[0] . " " . $dateTime[1];
                      } else {
                        echo date("d-m-Y");
                      }
                    }
                    ?>
                  </div>
                </div>
                <div class="container" id="ExcelData" style="margin-top:2%;">
                  <div class="left_side">
                    <div class="day_container">
                      <div><Label class="dayshift">DAYSHIFT</Label>
                      </div>
                      <div class="box">
                        <div style="display:flex; align-items: center;">
                          <label for="BUILD AMOUNT" style="margin:unset;">BUILD AMOUNT:&nbsp;</label>
                          <input name="BUILD AMOUNT" type="number" id="day_build_amount" class="form-control" style="width:150px;">
                        </div>
                      </div>
                    </div>
                    <div class="content day_table" id="table_data">

                    </div>
                    <script>
                      let previousObj = [];
                      const rows = $("table tr");
                      rows.each(function(i, el) {
                        let obj = [];
                        $(el).children("td").each(function(ic, elc) {
                          obj.push(elc);

                          if (previousObj.length > ic) {
                            if (previousObj[ic].innerHTML == obj[ic]
                              .innerHTML) {
                              $(previousObj[ic]).attr('rowspan',
                                getRowsSpan(ic, i, obj[ic]
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
                  <div class="left_side">
                    <div class="day_container">
                      <div><Label class="dayshift">NIGHTSHIFT</Label>
                      </div>
                      <div class="box">
                        <div style="display:flex; align-items: center;">
                          <label for="BUILD AMOUNT" style="margin:unset;">BUILD AMOUNT:&nbsp;</label>
                          <input name="BUILD AMOUNT" type="number" id="night_build_amount" class="form-control" style="width:150px;">
                        </div>
                      </div>
                    </div>
                    <div class="content night_table" id="table_data">
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
  <!-- REQUIRED SCRIPTS -->
</body>
<script>
  var dayRowNum = 0,
    nightRowNum = 0;
  var userNames = [];
  var liveVal;
  $(document).keypress(function(event) {
    $("#cont_mod").focus();
  });
  // document.getElementById("date_picker").valueAsDate = new Date();
  $(document).ready(function() {
    $("#cont_mod").focus();

    $("#date_picker").datetimepicker({
      format: 'DD-MM-YYYY',
      icons: {
        previous: 'fas fa-angle-left',
        next: 'fas fa-angle-right',
      }
    });
  })
  $("#day_build_amount").keydown(function(e) {
    if (e.keyCode === 13) {
      countBuild("day", $("#day_build_amount").val());
    }
  });

  $("#night_build_amount").keydown(function(e) {
    if (e.keyCode === 13) {
      countBuild("night", $("#night_build_amount").val());
    }
  });

  function countBuild(type, amount)
  {
    if(type === "day"){
      let segVal_Day = (amount / dayRowNum).toFixed(2);
      for (let i = 0; i < dayRowNum; i++) {
          if ($('tr[dayrowid=' + i + ']').attr('id')) {
            let id = $('tr[dayrowid=' + i + ']').attr('id').split('_')[1];
            $("#day_counter_" + id).text(Math.ceil(segVal_Day * i));
            saveData(id, 'all', 'day');
          }
        }
      saveBuildAmount('day',amount);
    }else if(type == "night"){
      let segVal_Night = (amount / nightRowNum).toFixed(2);
      for (let j = 0; j < nightRowNum; j++) {
        if ($('tr[nightrowid=' + j + ']').attr('id')) {
          let id = $('tr[nightrowid=' + j + ']').attr('id').split('_')[1];
          $("#night_counter_" + id).text(Math.ceil(segVal_Night * j));
          saveData(id, 'all', 'night');
        }
      }
      saveBuildAmount('night',amount);
    }
  }

  async function saveBuildAmount(tbl,amount)
  {
    const data = await $.ajax({
      url:"buildAmount.php",
      type:"POST",
      data : {
        table:tbl,
        amount:amount,
        date:$('#date_picker').val()
      }
    });
  }

  function removeOption(select_item){
    let options = select_item.getElementsByTagName('option');
    for (var i = 1; i<options.length; i++) {
        select_item.removeChild(options[i]);
    }

    // 
    var option = document.createElement("option");
        option.text = "Select User";
        option.selected = true;
        select_item.add(option);
  }


  $("#cont_mod").keydown(function(e) {
    if (e.keyCode === 13) {
      let cont = $("#cont_mod").val().slice(0, 5);
      let mod = $("#cont_mod").val().slice(5);
      let contModStr = $("#cont_mod").val();

      // in case of day shift
      if (dayRowNum > 0) {
        for (let i = 0; i < dayRowNum - 1; i++) {
          let contVal = $($("tr[dayrowid='" + i + "'] td:first")).text();
          let modVal = $($("tr[dayrowid='" + i + "'] td:nth-child(2)")).text();
          if (contModStr.includes(contVal) && contModStr.includes(modVal)) {
            $($("tr[dayrowid='" + i + "'] td:first")).css("background-color", "#1EB823");
            $($("tr[dayrowid='" + i + "'] td:nth-child(2)")).css("background-color", "#1EB823");
            $($("tr[dayrowid='" + i + "'] td:first")).css("color", "white");
            $($("tr[dayrowid='" + i + "'] td:nth-child(2)")).css("color", "white");

            $($("#dayShiftTB tr")[i + 1]).attr("greenFlag", "1");
            let selRow = $($("#dayShiftTB tr")[i + 1]);
            let loadVal = parseInt($($(selRow).children()[4]).attr("load"));
            let tmpDayRowID = $(selRow).attr("dayrowid");

            // Save Data
            var rowID1 = $("tr[dayrowid='" + i + "']").attr("id");
            var rowID2 = $('#' + rowID1).next().next().next().attr("id");
            var s_cont = $("tr[dayrowid='" + i + "']").children(':first').text();
            var s_mod = $("tr[dayrowid='" + i + "']").children(':nth-child(2)').text();
            var count = $("tr[dayrowid='" + i + "']").children(':nth-child(3)').text();
            var curretUser = '';
            var liveTime = '';
            var liveBuild = liveVal;
            var startInfo = {};
            var finishInfo = {};
            var complete = true;
            // Save Data End

            if (tmpDayRowID % 2 === 0) {
              let sibling = parseInt(tmpDayRowID) + 1;
              curretUser = $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayLCUser']").text();
              liveTime = $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayLCTime']").text();
              startInfo = {
                userName: $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayStUser']").text(),
                regTime: $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayStTime']").text()
              }
              finishInfo = {
                userName: $($("tr[dayrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='dayFnUser']").text(),
                regTime: $($("tr[dayrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='dayFnTime']").text()
              }
              if ($("tr[dayrowid='" + sibling + "']").attr("greenFlag") == "1") {
                var dayStSel = document.getElementById("dayStSel_" + parseInt(i / 2 + 1));
                var dayFnSel = document.getElementById("dayFnSel_" + parseInt(i / 2 + 1));
                var dayLCSel = document.getElementById("dayLCSel_" + parseInt(i / 2 + 1));
                dayStSel.disabled = false;
                dayFnSel.disabled = false;
                dayLCSel.disabled = false;
                var startSelectElement = document.getElementById("dayStSel_" + parseInt(i / 2 + 1));
                var finishSelectElement = document.getElementById("dayFnSel_" + parseInt(i / 2 + 1));
                var loadSelectElement = document.getElementById("dayLCSel_" + parseInt(i / 2 + 1));
                removeOption(startSelectElement);
                removeOption(finishSelectElement);
                removeOption(loadSelectElement);
                $('#' + 'dayStSel_' + parseInt(i / 2 + 1)).parent().prev().css('background-color', '#1EB823');
                $('#' + 'dayStSel_' + parseInt(i / 2 + 1)).parent().prev().css('color', 'white');

                userNames.map(name => {
                  var option = document.createElement("option");
                  option.text = name;
                  startSelectElement.add(option);

                  var optionClone = option.cloneNode(true);
                  var optionCloneLoad = option.cloneNode(true);
                  finishSelectElement.add(optionClone);
                  loadSelectElement.add(optionCloneLoad);
                })
              }
            } else {
              let sibling = parseInt(tmpDayRowID) - 1;
              curretUser = $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayLCUser']").text();
              liveTime = $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayLCTime']").text();

              startInfo = {
                userName: $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayStUser']").text(),
                regTime: $($("tr[dayrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='dayStTime']").text()
              }
              finishInfo = {
                userName: $($("tr[dayrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='dayFnUser']").text(),
                regTime: $($("tr[dayrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='dayFnTime']").text()
              }
              if ($("tr[dayrowid='" + sibling + "']").attr("greenFlag") == "1") {
                var dayStSel = document.getElementById("dayStSel_" + parseInt(i / 2 + 1));
                var dayFnSel = document.getElementById("dayFnSel_" + parseInt(i / 2 + 1));
                var dayLCSel = document.getElementById("dayLCSel_" + parseInt(i / 2 + 1));
                dayStSel.disabled = false;
                dayFnSel.disabled = false;
                dayLCSel.disabled = false;
                var startSelectElement = document.getElementById("dayStSel_" + parseInt(i / 2 + 1));
                var finishSelectElement = document.getElementById("dayFnSel_" + parseInt(i / 2 + 1));
                var loadSelectElement = document.getElementById("dayLCSel_" + parseInt(i / 2 + 1));
                removeOption(startSelectElement);
                removeOption(finishSelectElement);
                removeOption(loadSelectElement);
                $('#' + 'dayStSel_' + parseInt(i / 2 + 1)).parent().prev().css('background-color', '#1EB823');
                $('#' + 'dayStSel_' + parseInt(i / 2 + 1)).parent().prev().css('color', 'white');

                userNames.map(name => {
                  var option = document.createElement("option");
                  option.text = name;
                  startSelectElement.add(option);

                  var optionClone = option.cloneNode(true);
                  var optionCloneLoad = option.cloneNode(true);
                  finishSelectElement.add(optionClone);
                  loadSelectElement.add(optionCloneLoad);
                })
              }
            }

            if (rowID1) {
              let data = {
                cont: s_cont,
                mod: s_mod,
                firstID: rowID1,
                secondID: rowID2,
                currentUser: curretUser,
                count: count,
                liveTime: liveTime,
                liveBuild: liveBuild,
                startInfo: startInfo,
                finishInfo: finishInfo,
                complete: complete ? "1" : "0"
              };
              finalData(data);
            }

            $("#cont_mod").val("");

          }
        }
      }

      // in case of night shift
      if (nightRowNum > 0) {
        for (let i = 0; i < nightRowNum; i++) {
          let contVal = $($("tr[nightrowid='" + i + "'] td:first")).text();
          let modVal = $($("tr[nightrowid='" + i + "'] td:nth-child(2)")).text();
          if (contModStr.includes(contVal) && contModStr.includes(modVal)) {
            $($("tr[nightrowid='" + i + "'] td:first")).css("background-color", "#1EB823");
            $($("tr[nightrowid='" + i + "'] td:nth-child(2)")).css("background-color", "#1EB823");
            $($("tr[nightrowid='" + i + "'] td:first")).css("color", "white");
            $($("tr[nightrowid='" + i + "'] td:nth-child(2)")).css("color", "white");

            $($("#nightShiftTB tr")[i + 1]).attr("greenFlag", "1");
            let selRow = $($("#nightShiftTB tr")[i + 1]);
            let loadVal = parseInt($($(selRow).children()[4]).attr("load"));
            let tmpDayRowID = $(selRow).attr("nightrowid");

            // Save Data
            var rowID1 = $("tr[nightrowid='" + i + "']").attr("id");
            var rowID2 = $('#' + rowID1).next().next().next().attr("id");
            var s_cont = $("tr[nightrowid='" + i + "']").children(":first").text();
            var s_mod = $("tr[nightrowid='" + i + "']").children(":nth-child(2)").text();
            var count = $("tr[nightrowid='" + i + "']").children(':nth-child(3)').text();
            var curretUser = '';
            var liveTime = '';
            var liveBuild = liveVal;
            var startInfo = {};
            var finishInfo = {};
            var complete = true;
            // Save Data End

            if (tmpDayRowID % 2 === 0) {
              let sibling = parseInt(tmpDayRowID) + 1;
              currentUser = $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightLCUser']").text();
              liveTime = $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightLCTime']").text();

              startInfo = {
                userName: $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightStUser']").text(),
                regTime: $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightStTime']").text()
              }
              finishInfo = {
                userName: $($("tr[nightrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='nightFnUser']").text(),
                regTime: $($("tr[nightrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='nightFnTime']").text()
              }
              if ($("tr[nightrowid='" + sibling + "']").attr("greenFlag") == "1") {
                var nightStSel = document.getElementById("nightStSel_" + parseInt(i / 2 + 1));
                var nightFnSel = document.getElementById("nightFnSel_" + parseInt(i / 2 + 1));
                var nightLCSel = document.getElementById("nightLCSel_" + parseInt(i / 2 + 1));
                nightStSel.disabled = false;
                nightFnSel.disabled = false;
                nightLCSel.disabled = false;
                var startSelectElement = document.getElementById("nightStSel_" + parseInt(i / 2 + 1));
                var finishSelectElement = document.getElementById("nightFnSel_" + parseInt(i / 2 + 1));
                var loadSelectElement = document.getElementById("nightLCSel_" + parseInt(i / 2 + 1));
                removeOption(startSelectElement);
                removeOption(finishSelectElement);
                removeOption(loadSelectElement);
                $('#' + 'nightStSel_' + parseInt(i / 2 + 1)).parent().prev().css('background-color', '#1EB823');
                $('#' + 'nightStSel_' + parseInt(i / 2 + 1)).parent().prev().css('color', 'white');

                userNames.map(name => {
                  var option = document.createElement("option");
                  option.text = name;
                  startSelectElement.add(option);

                  var optionClone = option.cloneNode(true);
                  var optionCloneLC = option.cloneNode(true);
                  finishSelectElement.add(optionClone);
                  loadSelectElement.add(optionCloneLC);
                })
              }
            } else {
              let sibling = parseInt(tmpDayRowID) - 1;
              currentUser = $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightLCUser']").text();
              liveTime = $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightLCTime']").text();

              startInfo = {
                userName: $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightStUser']").text(),
                regTime: $($("tr[nightrowid='" + sibling + "'] td:nth-child(5)")).children("div[name='nightStTime']").text()
              }
              finishInfo = {
                userName: $($("tr[nightrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='nightFnUser']").text(),
                regTime: $($("tr[nightrowid='" + sibling + "'] td:nth-child(6)")).children("div[name='nightFnTime']").text()
              }
              if ($("tr[nightrowid='" + sibling + "']").attr("greenFlag") == "1") {
                var nightStSel = document.getElementById("nightStSel_" + parseInt(i / 2 + 1));
                var nightFnSel = document.getElementById("nightFnSel_" + parseInt(i / 2 + 1));
                var nightLCSel = document.getElementById("nightLCSel_" + parseInt(i / 2 + 1));
                nightStSel.disabled = false;
                nightFnSel.disabled = false;
                nightLCSel.disabled = false;
                var startSelectElement = document.getElementById("nightStSel_" + parseInt(i / 2 + 1));
                var finishSelectElement = document.getElementById("nightFnSel_" + parseInt(i / 2 + 1));
                var loadSelectElement = document.getElementById("nightLCSel_" + parseInt(i / 2 + 1));
                removeOption(startSelectElement);
                removeOption(finishSelectElement);
                removeOption(loadSelectElement);
                $('#' + 'nightStSel_' + parseInt(i / 2 + 1)).parent().prev().css('background-color', '#1EB823');
                $('#' + 'nightStSel_' + parseInt(i / 2 + 1)).parent().prev().css('color', 'white');

                userNames.map(name => {
                  var option = document.createElement("option");
                  option.text = name;
                  startSelectElement.add(option);

                  var optionClone = option.cloneNode(true);
                  var optionCloneLC = option.cloneNode(true);
                  finishSelectElement.add(optionClone);
                  loadSelectElement.add(optionCloneLC);
                })
              }
            }
            if (rowID1) {
              let data = {
                cont: s_cont,
                mod: s_mod,
                firstID: rowID1,
                secondID: rowID2,
                currentUser: curretUser,
                count: count,
                liveTime: liveTime,
                liveBuild: liveBuild,
                startInfo: startInfo,
                finishInfo: finishInfo,
                complete: complete ? "1" : "0"
              };
              finalData(data);
            }

          }
        }
      }

      finalData();
      $("#cont_mod").val("");
    }
  })

  function selectDayLC(row) {
    $("#dayLCSel_" + row).parent().css('background-color', '#1EB823');
    $("#dayLCSel_" + row).parent().css('color', 'white');

    var selectElement = document.getElementById("dayLCSel_" + row);
    var selectedValue = selectElement.value;

    var startUserElement = document.getElementById("dayLCUser_" + row);
    var startTimeElement = document.getElementById("dayLCTime_" + row);

    var username = selectedValue;
    var currentTime = new Date().toLocaleTimeString();

    startUserElement.textContent = username;
    startTimeElement.textContent = currentTime;
    saveData(row, 'load', 'day');
  }

  function selectNightLC(row) {
    $("#nightLCSel_" + row).parent().css('background-color', '#1EB823');
    $("#nightLCSel_" + row).parent().css('color', 'white');

    var selectElement = document.getElementById("nightLCSel_" + row);
    var selectedValue = selectElement.value;

    var startUserElement = document.getElementById("nightLCUser_" + row);
    var startTimeElement = document.getElementById("nightLCTime_" + row);

    var username = selectedValue;
    var currentTime = new Date().toLocaleTimeString();

    startUserElement.textContent = username;
    startTimeElement.textContent = currentTime;
    saveData(row, 'load', 'night');
  }

  //Select start user name and set it and time for day shift
  function selectDayStName(row) {
    activeRow(row, 'day');
    var selectElement = document.getElementById("dayStSel_" + row);
    var selectedValue = selectElement.value;
    
    var startUserElement = document.getElementById("dayStUser_" + row);
    var startTimeElement = document.getElementById("dayStTime_" + row);
    
    var username = selectedValue;
    var currentTime = new Date().toLocaleTimeString();
    
    startUserElement.textContent = username;
    startTimeElement.textContent = currentTime;
    saveData(row, 'start', 'day');
    finalData();
  }
  //Select finish user name and set it and time for day shift
  function selectDayFnName(row) {
    $("#dayFnSel_" + row).parent().css('background-color', '#1EB823');
    $("#dayFnSel_" + row).parent().css('color', 'white');
    activeRow(row, 'day');
    activeStartBox("dayFnSel_" + row);
    var selectElement = document.getElementById("dayFnSel_" + row);
    var selectedValue = selectElement.value;

    var finishUserElement = document.getElementById("dayFnUser_" + row);
    var finishTimeElement = document.getElementById("dayFnTime_" + row);

    var username = selectedValue;
    var currentTime = new Date().toLocaleTimeString();

    finishUserElement.textContent = username;
    finishTimeElement.textContent = currentTime;
    saveData(row, 'finish', 'day');
  }
  //Select start user name and set it and time for night shift
  function selectNightStName(row) {
    activeRow(row, 'night');
    var selectElement = document.getElementById("nightStSel_" + row);
    var selectedValue = selectElement.value;

    var startUserElement = document.getElementById("nightStUser_" + row);
    var startTimeElement = document.getElementById("nightStTime_" + row);

    var username = selectedValue;
    var currentTime = new Date().toLocaleTimeString();

    startUserElement.textContent = username;
    startTimeElement.textContent = currentTime;
    saveData(row, 'start', 'night');
    finalData();
  }
  //Select finish user name and set it and time for night shift
  function selectNightFnName(row) {
    $("#nightFnSel_" + row).parent().css('background-color', '#1EB823');
    $("#nightFnSel_" + row).parent().css('color', 'white');
    activeRow(row, 'night');
    activeStartBox("nightFnSel_" + row);
    var selectElement = document.getElementById("nightFnSel_" + row);
    var selectedValue = selectElement.value;

    var finishUserElement = document.getElementById("nightFnUser_" + row);
    var finishTimeElement = document.getElementById("nightFnTime_" + row);

    var username = selectedValue;
    var currentTime = new Date().toLocaleTimeString();

    finishUserElement.textContent = username;
    finishTimeElement.textContent = currentTime;
    saveData(row, 'finish', 'night');
  }

  // fetching or saving final data
  function finalData(data) {
    if (data == null) {
      $.ajax({
        url: "finalData.php",
        type: "GET",
        success: (data) => {
          if (data.length > 0) {
            var len = 0;
            JSON.parse(data).forEach(function(item) {
              if (Number(item.complete)) {
                $("#" + item.firstID).children(":first").css("background-color", "#1EB823");
                $("#" + item.firstID).children(":nth-child(2)").css("background-color", "#1EB823");
                $("#" + item.firstID).children(":first").css("color", "white");
                $("#" + item.firstID).children(":nth-child(2)").css("color", "white");
              }
              $("#" + item.firstID).children(":nth-child(3)").html(item.count);
              if(item.startNm && item.startTime)
                $("#" + item.firstID).children(":nth-child(4)").html(Number(item.liveBuild) ? item.liveBuild : "");

              if (item.finishNm && item.finishTime) {
                $("#" + item.firstID).children(":nth-child(6)").css("background-color", "#1EB823");
                $("#" + item.firstID).children(":nth-child(7)").css("background-color", "#1EB823");
                $("#" + item.firstID).children(":nth-child(6)").css("color", "white");
                $("#" + item.firstID).children(":nth-child(7)").css("color", "white");
              }

              $("#" + item.firstID).children(":nth-child(5)").children("div:first").html(item.userNm);
              $("#" + item.firstID).children(":nth-child(5)").children("div:nth-child(2)").html(item.liveTime);

              $("#" + item.firstID).children(":nth-child(6)").children("div:first").html(item.startNm);
              $("#" + item.firstID).children(":nth-child(6)").children("div:nth-child(2)").html(item.startTime);

              $("#" + item.firstID).children(":last-child").children("div:first").html(item.finishNm);
              $("#" + item.firstID).children(":last-child").children("div:nth-child(2)").html(item.finishTime);
            })

            JSON.parse(data).forEach(function(item) {
              if (Number(item.complete) && item.secondID) {
                const result = searchItem(JSON.parse(data), item.secondID);

                if (result && Number(result.complete)) {
                  $("#" + item.firstID).children(":nth-child(5)").children(':first').html(item.userNm);
                  $("#" + item.firstID).children(":nth-child(5)").children(':nth-child(2)').html(item.liveTime);
                  if (item.userNm && item.liveTime) {
                    $("#" + item.firstID).children(":nth-child(5)").css("background-color", "#1EB823");
                    $("#" + item.firstID).children(":nth-child(5)").css("color", "white");
                  }

                  // Select 
                  var rowid = 0;

                  if (item.firstID.includes('day')) {
                    rowid = $("#" + item.firstID).attr('dayrowid');
                  } else {
                    rowid = $("#" + item.firstID).attr('nightrowid');
                  }
                  if (rowid % 2 == 0) {
                    var startSelectElementID = $("#" + item.firstID).children(":nth-child(6)").children("select").attr('id');
                    var finishSelectElementID = $("#" + item.firstID).children(":last-child").children("select").attr('id');
                    var loadSelectElementID = $("#" + item.firstID).children(":nth-child(5)").children("select").attr('id');

                    var startSelectElement = document.getElementById(startSelectElementID);
                    var finishSelectElement = document.getElementById(finishSelectElementID);
                    var loadSelectElement = document.getElementById(loadSelectElementID);
                    removeOption(startSelectElement);
                    removeOption(finishSelectElement);
                    removeOption(loadSelectElement);
                    userNames.map(name => {
                      var option = document.createElement("option");
                      option.text = name;
                      startSelectElement.add(option);

                      var optionClone = option.cloneNode(true);
                      var optionCloneLC = option.cloneNode(true);
                      finishSelectElement.add(optionClone);
                      loadSelectElement.add(optionCloneLC);
                    });
                    $("#" + item.firstID).children(":nth-child(5)").children(":last").val(item.userNm);
                    $("#" + item.firstID).children(":nth-child(6)").children(":last").val(item.startNm);
                    $("#" + item.firstID).children(":last-child").children(":last").val(item.finishNm);
                  }

                  $("#" + item.firstID).children(":nth-child(5)").children("select").prop("disabled", false);
                  $("#" + item.firstID).children(":nth-child(6)").children("select").prop("disabled", false);
                  $("#" + item.firstID).children(":last-child").children("select").prop("disabled", false);
                }
              } else if (item.complete && !item.secondID) {
                $("#" + item.firstID).children(":nth-child(5)").children(':first').html(item.userNm);
                $("#" + item.firstID).children(":nth-child(5)").children(':nth-child(2)').html(item.liveTime);
                if (item.userNm && item.liveTime) {
                  $("#" + item.firstID).children(":nth-child(5)").css("background-color", "#1EB823");
                  $("#" + item.firstID).children(":nth-child(5)").css("color", "white");
                }

                // Select 
                var rowid = 0;

                if (item.firstID.includes('day')) {
                  rowid = $("#" + item.firstID).attr('dayrowid');
                } else {
                  rowid = $("#" + item.firstID).attr('nightrowid');
                }
                if (rowid % 2 == 0) {
                  var startSelectElementID = $("#" + item.firstID).children(":nth-child(6)").children("select").attr('id');
                  var finishSelectElementID = $("#" + item.firstID).children(":last-child").children("select").attr('id');
                  var loadSelectElementID = $("#" + item.firstID).children(":nth-child(5)").children("select").attr('id');

                  var startSelectElement = document.getElementById(startSelectElementID);
                  var finishSelectElement = document.getElementById(finishSelectElementID);
                  var loadSelectElement = document.getElementById(loadSelectElementID);

                  userNames.map(name => {
                    var option = document.createElement("option");
                    option.text = name;
                    startSelectElement.add(option);

                    var optionClone = option.cloneNode(true);
                    var optionCloneLC = option.cloneNode(true);
                    finishSelectElement.add(optionClone);
                    loadSelectElement.add(optionCloneLC);
                  });
                  $("#" + item.firstID).children(":nth-child(5)").children(":last").val(item.userNm);
                  $("#" + item.firstID).children(":nth-child(6)").children(":last").val(item.startNm);
                  $("#" + item.firstID).children(":last-child").children(":last").val(item.finishNm);
                }

                $("#" + item.firstID).children(":nth-child(5)").children("select").prop("disabled", false);
                $("#" + item.firstID).children(":nth-child(6)").children("select").prop("disabled", false);
                $("#" + item.firstID).children(":last-child").children("select").prop("disabled", false);
              }
            })
          } else {
            console.log("No data");
          }
        }
      })
    } else {
      $.ajax({
        url: "finalData.php",
        type: "POST",
        data: {
          data: data
        },
        success: () => {
          console.log("Data is saved successfully.");
        }
      })
    }
  }

  // 2023-7-20

  function saveData(row, col, state) {
    var cont, mod, rowID1, rowID2, curretUser, count, liveTime, liveBuild, complete, buildAmount;
    var startInfo = {};
    var finishInfo = {};
    if (col == 'start') {
      var selID;
      if (state == 'day') {
        selID = '#dayStSel_' + row;
        buildAmount = $('#day_build_amount').val();
      } else {
        selID = '#nightStSel_' + row;
        buildAmount = $('#night_build_amount').val();
      }
      rowID1 = $(selID).parent().parent().attr("id");
      rowID2 = $('#' + rowID1).next().next().next().attr("id");
      cont = $(selID).parent().parent().children(":first").text();
      mod = $(selID).parent().parent().children(":nth-child(2)").text();
      curretUser = $(selID).parent().prev().children(":first").text();
      count = $(selID).parent().parent().children(':nth-child(3)').text();
      liveTime = $(selID).parent().prev().children(":nth-child(2)").text();
      liveBuild = $(selID).parent().prev().prev().text() ? $(selID).parent().prev().prev().text() : liveVal;
      startInfo = {
        userName: $(selID).parent().children(":first").text(),
        regTime: $(selID).parent().children(":nth-child(2)").text()
      }
      finishInfo = {
        userName: $(selID).parent().next().children(":first").text(),
        regTime: $(selID).parent().next().children(":nth-child(2)").text()
      }
      if ($('#' + rowID1).children(":first").css("background-color") == 'rgb(30, 184, 35)') {
        complete = "1";
      } else {
        complete = "0";
      }
    } else if (col == 'finish') {
      var selID;
      if (state == 'day') {
        selID = '#dayFnSel_' + row;
        buildAmount = $('#day_build_amount').val();
      } else {
        selID = '#nightFnSel_' + row;
        buildAmount = $('#night_build_amount').val();
      }
      rowID1 = $(selID).parent().parent().attr("id");
      rowID2 = $('#' + rowID1).next().next().next().attr("id");
      cont = $(selID).parent().parent().children(":first").text();
      mod = $(selID).parent().parent().children(":nth-child(2)").text();
      count = $(selID).parent().parent().children(':nth-child(3)').text();
      curretUser = $(selID).parent().prev().prev().children(":first").text();
      liveTime = $(selID).parent().prev().prev().children(":nth-child(2)").text();
      liveBuild = $(selID).parent().prev().prev().prev().text();
      startInfo = {
        userName: $(selID).parent().prev().children(":first").text(),
        regTime: $(selID).parent().prev().children(":nth-child(2)").text()
      }
      finishInfo = {
        userName: $(selID).prev().prev().text(),
        regTime: $(selID).prev().text(),
      }
      if ($('#' + rowID1).children(":first").css("background-color") == 'rgb(30, 184, 35)') {
        complete = "1";
      } else {
        complete = "0";
      }
    } else if (col == 'load') {
      var selID;
      if (state == 'day') {
        selID = '#dayLCSel_' + row;
        buildAmount = $('#day_build_amount').val();
      } else {
        selID = '#nightLCSel_' + row;
        buildAmount = $('#night_build_amount').val();
      }
      rowID1 = $(selID).parent().parent().attr("id");
      rowID2 = $('#' + rowID1).next().next().next().attr("id");
      cont = $(selID).parent().parent().children(":first").text();
      mod = $(selID).parent().parent().children(":nth-child(2)").text();
      count = $(selID).parent().parent().children(':nth-child(3)').text();
      curretUser = $(selID).prev().prev().text();
      liveTime = $(selID).prev().text();
      liveBuild = $(selID).parent().prev().text();
      startInfo = {
        userName: $(selID).parent().next().children(":first").text(),
        regTime: $(selID).parent().next().children(":nth-child(2)").text()
      }
      finishInfo = {
        userName: $(selID).parent().next().next().children(":first").text(),
        regTime: $(selID).parent().next().next().children(":nth-child(2)").text()
      }
      if ($('#' + rowID1).children(":first").css("background-color") == 'rgb(30, 184, 35)') {
        complete = "1";
      } else {
        complete = "0";
      }
    } else {
      var selID = state == "day" ? "#dayRow_" + row : "#nightRow_" + row;
      if (state == 'day') {
        buildAmount = $('#day_build_amount').val();
      } else {
        buildAmount = $('#night_build_amount').val();
      }
      rowID1 = $(selID).attr("id");
      rowID2 = $('#' + rowID1).next().next().next().attr("id");
      count = $(selID).children(':nth-child(3)').text();
      if ($('#' + rowID1).children(":first").css("background-color") == 'rgb(30, 184, 35)') {
        complete = "1";
      } else {
        complete = "0";
      }
      if ($(selID).attr(state == "day" ? "dayrowid" : "nightrowid") % 2 == 0) {
        curretUser = $(selID).children(':nth-child(5)').children(':first').text();
        liveTime = $(selID).children(':nth-child(5)').children(':nth-child(2)').text();

        liveBuild = $(selID).children(':nth-child(4)').text();

        startInfo = {
          userName: $(selID).children(':nth-child(6)').children(":first").text(),
          regTime: $(selID).children(':nth-child(6)').children(":nth-child(2)").text()
        }

        finishInfo = {
          userName: $(selID).children(':nth-child(7)').children(":first").text(),
          regTime: $(selID).children(':nth-child(7)').children(":nth-child(2)").text()
        }
      } else {
        curretUser = '';
        liveTime = '';
        liveBuild = "";
        startInfo = {
          userName: '',
          regTime: ''
        };
        finishInfo = {
          userName: '',
          regTime: ''
        };
      }
    }

    if (rowID1) {
      let data = {
        cont: cont,
        mod: mod,
        firstID: rowID1,
        secondID: rowID2 ? rowID2 : "",
        currentUser: curretUser,
        count: count,
        liveTime: liveTime,
        liveBuild: liveBuild,
        startInfo: startInfo,
        finishInfo: finishInfo,
        complete: complete,
        buildAmount : buildAmount
      }
      // console.log(data);
      finalData(data);
    }

  }

  function activeStartBox(id) {
    $('#' + id).parent().prev().css('background-color', '#1EB823');
    $('#' + id).parent().prev().css('color', 'white');
  }

  function activeRow(row, table) {
    saveActiveRow(row,table);
    removeActiveRow();
    // Select Row
    const selectedRow = 2 * row - 1;
    if (table == 'day') {
      var secondID = $("tr[dayrowid='" + (selectedRow - 1) + "']").next().next().next().attr('id');
      if (secondID) {
        $("tr[dayrowid='" + (selectedRow - 1) + "']").css('border-width', '5px 5px 0 5px');
        $("tr[dayrowid='" + (selectedRow - 1) + "']").css('border-color', 'orange');
        $("tr[dayrowid='" + selectedRow + "']").css('border-width', '0 5px 5px 5px');
        $("tr[dayrowid='" + selectedRow + "']").css('border-color', 'orange');
      } else {
        $("tr[dayrowid='" + (selectedRow - 1) + "']").css('border-width', '5px 5px 5px 5px');
        $("tr[dayrowid='" + (selectedRow - 1) + "']").css('border-color', 'orange');
      }
    } else {
      var secondID = $("tr[nightrowid='" + (selectedRow - 1) + "']").next().next().next().attr('id');
      if (secondID) {
        $("tr[nightrowid='" + (selectedRow - 1) + "']").css('border-width', '5px 5px 0 5px');
        $("tr[nightrowid='" + (selectedRow - 1) + "']").css('border-color', 'orange');
        $("tr[nightrowid='" + selectedRow + "']").css('border-width', '0 5px 5px 5px');
        $("tr[nightrowid='" + selectedRow + "']").css('border-color', 'orange');
      } else {
        $("tr[nightrowid='" + (selectedRow - 1) + "']").css('border-width', '5px 5px 5px 5px');
        $("tr[nightrowid='" + (selectedRow - 1) + "']").css('border-color', 'orange');
      }
    }
  }

  function removeActiveRow() {
    for (let i = 0; i < dayRowNum - 1; i++) {
      $("tr[dayrowid='" + i + "']").css('border', '1px solid black');
    }

    for (let i = 0; i < nightRowNum - 1; i++) {
      $("tr[nightrowid='" + i + "']").css('border', '1px solid black');
    }
  }

  function saveActiveRow(row,table){
    $.ajax({
      url: "activeRow.php",
      method: "post",
      data: {
        row:row,
        table:table,
        date:$('#date_picker').val()
      }
    })
  }

  async function getActiveRow(){
    const data = await $.ajax({
      url:'actions.php',
      method:'post',
      data : {
        action :'get_active_row',
        date:$('#date_picker').val()
      }
    });
    return JSON.parse(data);
  }

  // async function removeActiveRow(){
  //   const data = await $.ajax({
  //     url:'action.php',
  //     method:'post',
  //     data : {
  //       action :'get_active_row',
  //       date:$('#date_picker').val()
  //     }
  //   });
  //   console.log("get====>",data);
  // }

  function search(obj, tar) {
    var state = false;
    obj.map(item => {
      if (item.firstID == tar) {
        state = true;
      }
    })
    return state;
  }

  function searchItem(obj, tar) {
    var result;
    obj.map(item => {
      if (item.firstID == tar) {
        result = item;
      }
    })
    return result;
  }

  function getLiveVal() {
    $.ajax({
      url: "getLatestLive.php",
      type: "get",
      success: (data) => {
        liveVal = JSON.parse(data)[0];
        // var dayLastID = '';
        // for (let i = 0; i < ($('#dayShiftTB tr').length - 1); i++) {
        //   if (i % 2 == 0 && $('tr[dayrowid="' + i + '"]').children(':first').css('background-color') == 'rgb(30, 184, 35)' && Number($('tr[dayrowid="' + i + '"]').children(':nth-child(4)').text()) && !Number($('tr[dayrowid="' + (i + 2) + '"]').children(':nth-child(4)').text()))
        //     dayLastID = i + 2;
        // }
        // if (Number(dayLastID))
        //   $('tr[dayrowid="' + dayLastID + '"]').children(':nth-child(4)').html(liveVal);
        // var nightLastID = '';
        // for (let i = 0; i < ($('#nightShiftTB tr').length - 1); i++) {
        //   if (i % 2 == 0 && $('tr[nightrowid="' + i + '"]').children(':first').css('background-color') == 'rgb(30, 184, 35)' && $('tr[nightrowid="' + i + '"]').children(':nth-child(4)').text() && !$('tr[nightrowid="' + (i + 2) + '"]').children(':nth-child(4)').text())
        //     nightLastID = i + 2;
        // }
        // if (Number(nightLastID))
        //   $('tr[nightrowid="' + nightLastID + '"]').children(':nth-child(4)').html(liveVal);
        $('#live_build_value').html(liveVal ? liveVal : 0);
        $("td[status='updated']").html(JSON.parse(data)[0]);
      }
    })
  }

  function getUsers() {
    $.ajax({
      url: "actions.php",
      method: "post",
      data: {
        action: 'get_users'
      }
    }).done(function(result) {
      userNames = JSON.parse(result);
    });
  }

  function deleteFile(filename) {
    $.ajax({
      url: "actions.php",
      method: "post",
      data: {
        action: 'delete_file',
        file: filename
      }
    })
  }

  function getTableData(table) {
    var rows = [];
    var dataRows = $('#' + table + ' tr');
    for (var i = 1; i < dataRows.length; i++) {
      var row = [];
      if (i == 0)
        var dataCells = dataRows[i].getElementsByTagName("th");
      else
        var dataCells = dataRows[i].getElementsByTagName("td");
      for (var j = 0; j < dataCells.length; j++) {
        var tdText = dataCells[j].innerText.split('\n');
        var addedText = tdText[0] + "   ";
        if (tdText[1]) {
          addedText += '\n' + tdText[1];
        }
        row.push(addedText);
      }
      rows.push(row);
    }
    return rows;
  }

  $('#date_picker').on('dp.change', function(e) {
    var selected = $(this).val().split("-");
    read_excel_data(selected[2] + "-" + selected[1] + "-" + selected[0]);
    $("#cont_mod").focus();
    finalData();
    getLiveVal();
    get_build_amount();
  })

  function get_build_amount(){
    $.ajax({
      url:'actions.php',
      method:'post',
      data : {
        action : 'get_built_amount',
        date : $('#date_picker').val()
      }
    }).then(function(data){
      if(data.length){
        data = JSON.parse(data);
        data.map(item=>{
          if(item.tbl_name == "day"){
            $('#day_build_amount').val(item.amount);
            countBuild("day", item.amount);
          }else if(item.tbl_name == "night"){
            $('#night_build_amount').val(item.amount);
            countBuild("night", item.amount);
          }
        })
      }else{
        $('#day_build_amount').val('')
        $('#night_build_amount').val('')
      }
    })
  }

  function read_excel_data(selectedDate) {
    const today = new Date();
    const yyyy = today.getFullYear();
    let mm = today.getMonth() + 1; // Months start at 0!
    let dd = today.getDate();
    let hr = today.getHours();
    let min = today.getMinutes();
    let sec = today.getSeconds();
    const formattedToday = yyyy + '-' + mm + '-' + dd;
    $.ajax({
      url: "actions.php",
      method: "post",
      data: {
        action: 'read_excel',
        date: selectedDate ? selectedDate : formattedToday
      }
    }).done(async function(result) {
        var result = JSON.parse(result);
        $('.night_table').html(result.night);
        $('.day_table').html(result.day);
  
        // Active Row
        if (result.day != 'No data found' && result.night != 'No data found') {
          const activeArray = await getActiveRow();
          if (dayRowNum && nightRowNum && activeArray && activeArray.tbl_name) {
            if(activeArray.DATE == $('#date_picker').val())
              activeRow(activeArray.row_id, activeArray.tbl_name)
          }
        }
    });
  }

  // Initial function
  read_excel_data();
  getUsers();
  finalData();
  getLiveVal();
  setInterval(function() {
    getLiveVal();
  }, 30000);

  setInterval(function() {
    finalData();

    // Set Date & Time
    const dt = new Date();
    var localTime = dt.toLocaleTimeString().split(":");
    if(localTime[0] == "7" && localTime[1] == "15" && localTime[2].includes("AM")){
      $('#date_picker').data("DateTimePicker").date(new Date());
    }
  },10000);
</script>

</html>