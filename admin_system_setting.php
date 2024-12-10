<?php
require_once("config.php");
require_once("functions.php");
$page_name = "System Setting";
$_SESSION['page'] = 'admin_system_setting.php';
login_check();
require_once("assets.php");
?>
<link rel="stylesheet" href="assets/css/bootstrap-colorpicker.min.css" />
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" type="text/css" href="plugins/daterangepicker/daterangepicker.css" />
<style>
  table,
  td {
    vertical-align: middle !important;
  }

  .btn-sm {
    width: 100%;
    padding: auto !important;
  }

  #kanbanAdd {
    padding: 5px;
    width: 100%;
  }

  .add-dolly,
  .delete-dolly {
    width: 70% !important;
    padding-top: 5px !important;
    padding-bottom: 5px !important;
  }

  .add-reason,
  .delete-reason {
    width: 50% !important;
    padding-top: 5px !important;
    padding-bottom: 5px !important;
  }

  .spinner_div {
    position: absolute;
    top: 100px;
    left: 50%;
    z-index: 100;
    display: flex;
    justify-content: center;
    background-color: transparent;

  }

  .hide {
    display: none;
  }

#system_fill_reset{
  float: right;
}
  body {
    position: relative;
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
            <div class="col-sm-12">
              <h1 class="m-0" style="display: inline"><?php echo $page_name; ?></h1>
            </div>
          </div>
        </div>
      </div>
      <div class="spinner_div">
        <img src="assets/img/spinner.gif" alt="Loading..." width="30px" height="30px" class="hide" id="spinner">
      </div>
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Renban No Prefix
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="renban_no_prefix" name="renban_no_prefix" value="<?php echo get_setting('renban_no_prefix') ?>" style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="save_renban_no_prefix">Save</button>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Cycle Settings
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="cycle_value" name="cycle_value" style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="save_cycle_setting">Save</button>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Import Conveyance pick list(csv)
                    </h3>
                  </div>
                </div>
                <div class="card-body" style="display:grid; grid-template-columns: 1fr 1fr; padding-top:5px">
                  <div>
                    <label class="btn btn-primary" style="margin-top: 7px; font-weight: normal;">
                      Choose CSV<input type="file" hidden id="file" name="file" required>
                    </label>
                    <button type="button" class="btn btn-success" id="import_conveyance_csv">Import</button>
                  </div>
                  <div style="margin: 7px 0 0 auto;">
                    <button type="button" class="btn btn-danger" id="clear_list">Clear</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      OPR Pick Settings
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="opr_pick_value" name="opr_pick_value" style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="save_opr_pick_setting">Save</button>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      OPR Delivery Settings
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="opr_delivery_value" name="opr_delivery_value" style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="save_opr_delivery_setting">Save</button>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Driver Settings
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="driver_value" name="driver_value" style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="driver_setting">Save</button>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Pause Settings
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="pause_value" name="pause_value" value='<?php echo get_setting("pause_time"); ?>' style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="pause_setting">Save</button>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      System Fill && Packing List Import
                    </h3>
                  </div>
                </div>

                <div class="card-body">
                  <div style="text-align: right;display:flex;">
                    <label class="btn btn-primary" style="font-weight: normal;margin: 0px">
                      Choose File
                      <input type="file" hidden id="fileToUpload" name="fileToUpload" required>
                    </label>
                    &nbsp;
                    &nbsp;
                    <button type="button" class="btn btn-success" id="upload_excel_id">System Fill Upload</button>
                    &nbsp;
                    &nbsp;
                    <button type="button" class="btn btn-success" id="upload_excel_pack_id">Packing List Upload</button>
                    &nbsp;
                    &nbsp;
                    <button type="button" class="btn btn-success" id="upload_build_csv">Build CSV Upload</button>
                  </div>
                </div>

              </div>
            </div>
            <div class="col-lg-4">
              <div class="card">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Live Build
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <input type="text" class="form-control" id="live_build" name="live_build" style="width: 200px; display: inline-block">
                  <button class="btn btn-success" id="live_build_btn">Save</button>
                  <button type="button" class="btn btn-danger" id="system_fill_reset" data-target="#confirm_reset_modal" data-toggle="modal" data-dismiss="modal">System fill reset</button>
                </div>
              </div>
            </div>
            <!-- <div class="col-lg-4"></div> -->
           
            <div class="row col-lg-12">
              <div class="col-lg-6">
                <div class="card card-primary">
                  <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                      <h3 class="card-title">
                        Dolly Setting
                      </h3>
                    </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered table-bordered" id="dolly_table">
                      <thead>
                        <tr>
                          <th>Dolly Name</th>
                          <th>Color</th>
                          <th></th>
                        </tr>
                        <tr id="tr_dolly_0">
                          <th>
                            <input type="text" class="form-control" name="dolly_name">
                          </th>
                          <th>
                            <div class="input-group dolly-colorpicker colorpicker-element" id="dolly_color_0">
                              <input type="text" class="form-control" name="dolly_color" id="input_dolly_color_0" value="" data-original-title="" title="">
                              <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-square"></i></span>
                              </div>
                            </div>
                          </th>
                          <th style="text-align: center;">
                            <button type="button" class="btn btn-success add-dolly">Add</button>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $dolly = get_all_dolly();
                        foreach ($dolly as $item) {
                          echo '<tr id="tr_dolly_' . $item->id . '">';
                          echo '<td><input type="text" class="form-control dolly-name" name="dolly_name" value="' . $item->name . '"></td>';
                          echo '<td>';
                          echo '<div class="input-group dolly-colorpicker colorpicker-element" id="dolly_color_' . $item->id . '">
                                                  <input type="text" class="form-control" name="dolly_color" id="input_dolly_color_' . $item->id . '" value="' . $item->color . '" data-original-title="" title="">
                                                  <div class="input-group-append">
                                                      <span class="input-group-text"><i class="fas fa-square" style="color: ' . $item->color . '"></i></span>
                                                  </div>
                                              </div>';
                          echo '</td>';
                          echo '<td style="text-align: center;"><button type="button" class="btn btn-danger delete-dolly" value="' . $item->id . '">Delete</button></td>';
                          echo '</tr>';
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="card card-primary">
                              <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                  <h3 class="card-title">
                                    Reason Setting
                                  </h3>
                                </div>
                              </div>
                              <div class="card-body">
                                <table class="table table-bordered table-bordered" id="reason_table">
                                  <thead>
                                    <tr>
                                      <th>Reason Name</th>
                                      <th></th>
                                    </tr>
                                    <tr id="tr_reason_0">
                                      <th>
                                        <input type="text" class="form-control" name="reason_name">
                                      </th>
                                      <th style="text-align: center;">
                                        <button type="button" class="btn btn-success add-reason">Add</button>
                                      </th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php
                                    $reasons = get_all_reason();
                                    foreach ($reasons as $item) {
                                      echo '<tr id="tr_reason_' . $item->id . '">';
                                      echo '<td>' . $item->name . '</td>';
                                      echo '<td style="text-align: center;"><button type="button" class="btn btn-danger delete-reason" value="' . $item->id . '">Delete</button></td>';
                                      echo '</tr>';
                                    }
                                    ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>

                            <div class="card card-primary">
                              <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                  <h3 class="card-title">
                                    Tag Setting
                                  </h3>
                                </div>
                              </div>
                              <div class="card-body">
                                <div class="row">
                                  <p class="mx-2">Current Tag</p>
                                  <input type="text" class="form-control" name="tag_value" id="tag_value" style="width: 200px; display: inline-block">
                                  <button class="btn btn-success mx-2" id="save_tag">Save</button>
                                </div>
                              </div>
                            </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="card card-primary">
                <div class="card-header border-0">
                  <div class="d-flex justify-content-between">
                    <h3 class="card-title">
                      Part to Kanban
                    </h3>
                  </div>
                </div>
                <div class="card-body">
                  <div style="text-align: right;">
                    <label class="btn btn-primary" style="margin-top: 7px; font-weight: normal;">
                      Choose CSV<input type="file" hidden id="part2kanban_file" name="part2kanban_file" required>
                    </label>
                    <button type="button" class="btn btn-success" id="import_kanban_csv">Import</button>
                    <button type="button" class="btn btn-default" id="clear_kanban">Clear</button>
                  </div>
                  <table class="table table-bordered table-bordered" style="table-layout:fixed" id="part2kanban_table">
                    <thead>
                      <tr>
                        <th>Kanban</th>
                        <th>Part Number</th>
                        <th>Zone / Dolly</th>
                        <th>Barcode</th>
                        <th>Pick Address</th>
                        <th>Delivery Address</th>
                        <th>Delivery Address2</th>
                        <th>Pick Seq</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th></th>
                      </tr>
                      <tr id="tr_p2k_0">
                        <th>
                          <input type="text" class="form-control" name="kanban">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="part_number">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="dolly">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="barcode">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="pick_address">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="delivery_address">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="delivery_address2">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="pick_seq">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="min">
                        </th>
                        <th>
                          <input type="text" class="form-control" name="max">
                        </th>
                        <th style="text-align: center;">
                          <button type="button" class="btn btn-success save-part2kanban" id="kanbanAdd">Add</button>
                        </th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
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

  <div class="modal fade" id="clear_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="user_form">
          <div class="modal-header">
            <h4 class="modal-title">Clear Data</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php
          $aaa = date("d / m / Y");
          ?>
          <div class="modal-body" style="" aria-hidden='true'>
            <input type="text" class="form-control" style="width: 50%; margin:10px auto; text-align: center" name="daterange" value="<?php echo $aaa; ?> - <?php echo $aaa; ?>" />
            <div class="d-flex" style="justify-content: center">
              <button type="button" class="btn btn-danger m-2" id="clear_all" style="width:150px" data-target="#deleteModal" data-toggle="modal" data-dismiss="modal">Clear All</button>
              <button type="button" class="btn btn-primary m-2" id="clear_select_item" style="width:150px">Select
                Delete</button>
            </div>
          </div>'
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <!-- Modal -->
  <div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Clear All</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to clear all data?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="close-modal" data-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger" id="clear_items" data-dismiss="modal">Yes</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="confirm_reset_modal" aria-hidden='true'>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Clear all of system fill</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to clear all data?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger" id="reset_fill_system" data-dismiss="modal">Yes</button>
        </div>
      </div>
    </div>
  </div>
  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="plugins/moment/moment.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/js/adminlte.min.js"></script>
  <script src="assets/js/bootstrap-colorpicker.min.js"></script>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script> -->
  <script src="assets/js/custom.js"></script>
  <script type="text/javascript" src="plugins/daterangepicker/daterangepicker.js"></script>
  
  <script>
    $(document).ready(function() {
      $("#save_renban_no_prefix").on('click', function() {
        if ($("#renban_no_prefix").val() == '') {
          $("#renban_no_prefix").focus();
          return false;
        }

        var set_value = $("#renban_no_prefix").val();

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
      });

      //Import pick list for conveyance
      $("#import_conveyance_csv").on('click', function() {
        if ($("#file").val() == '') {
          alert('Please select csv file');
          return false;
        }
        var file_data = $("#file").prop('files')[0];
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
            if (result == "Success")
              alert('Imported successfully');
            else
              alert('Import failed');
            $("#file").val("");
          }
        });
      });

      //Import pick list for system fill import
      $("#upload_excel_id").on('click', function() {
        if ($("#fileToUpload").val() == '') {
          alert('Please select CSV file');
          return false;
        }

        var file_data = $("#fileToUpload").prop('files')[0];
        var form_data = new FormData();

        form_data.append('file', file_data);
        form_data.append('kind', 'system');

        $.ajax({
          url: 'excel_pick_import.php',
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,
          type: 'post',
          success: function(result) {
            console.log(result);
            window.location.href = "./system_fill_main.php";
            $("#fileToUpload").val("");
          }
        });

      });
      //spinner
      document.getElementById("upload_excel_id").addEventListener("click", function() {
        document.getElementById("spinner").classList.remove("hide");
      });

      //Import pick list for system fill import
      $("#upload_excel_pack_id").on('click', function() {
        if ($("#fileToUpload").val() == '') {
          alert('Please select CSV file');
          return false;
        }

        var file_data = $("#fileToUpload").prop('files')[0];
        var form_data = new FormData();

        form_data.append('file', file_data);
        form_data.append('kind', "pack");

        $.ajax({
          url: 'excel_pick_import.php',
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,
          type: 'post',
          success: function(result) {
            window.location.href = "./stocking_kanban.php";
            $("#fileToUpload").val("");
          }
        });

      });

      //spinner
      document.getElementById("upload_excel_pack_id").addEventListener("click", function() {
        document.getElementById("spinner").classList.remove("hide");
      });

      // Buid CSV Upload

      //Import pick list for system fill import
      $("#upload_build_csv").on('click', function() {
        if ($("#fileToUpload").val() == '') {
          alert('Please select CSV file');
          return false;
        }

        var file_data = $("#fileToUpload").prop('files')[0];
        var form_data = new FormData();
        
        form_data.append('file', file_data);
        form_data.append('kind', "build");

        $.ajax({
          url: 'excel_pick_import.php',
          cache: false,
          contentType: false,
          processData: false,
          data: form_data,
          type: 'post',
          success: function(result) {
            console.log(result);
            window.location.href = "./system_fill_main.php?build";
            $("#fileToUpload").val("");
          }
        });

      });
      
      //spinner
      document.getElementById("upload_build_csv").addEventListener("click", function() {
        document.getElementById("spinner").classList.remove("hide");
      });

// Buid CSV Upload Finish

      /*
      Dolly
       */
      $('.dolly-colorpicker').colorpicker();

      $(document).on('colorpickerChange', '.dolly-colorpicker', function(event) {
        $(this).find('.fa-square').css('color', event.color.toString());
        var color = event.color.toString();
        //Update color for column
        var dolly_id = $(this).attr('id').replace('dolly_color_', '');
        $("#input_dolly_color_" + dolly_id).val(color);
        if (dolly_id == 0)
          return false;
        //Update color for column
        update_dolly(dolly_id, 'color', color);
      });

      function update_dolly(dolly_id, field, value) {
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'update_dolly',
            dolly_id: dolly_id,
            field: field,
            value: value,
          },
        }).done(function(result) {
          //console.log(result);
        });
      }

      $(document).on('click', '.add-dolly', function() {
        var tr = $(this).closest('tr');
        var dolly_id = tr.attr('id').replace('tr_dolly_', '');
        var dolly_name = tr.find('input[name="dolly_name"]').val();
        var color = tr.find('input[name="dolly_color"]').val();
        if (dolly_name == "") {
          tr.find('input[name="dolly_name"]').focus();
          return false;
        }
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'save_dolly',
            dolly_id: dolly_id,
            dolly_name: dolly_name,
            color: color,
          },
        }).done(function(result) {
          if (dolly_id == 0) {
            tr.find('input[name="dolly_name"]').val('');
            tr.find('input[name="dolly_color"]').val('');
            $("#dolly_table").find('tbody').append(result);
          }
        });
      });

      $(document).on('click', '.delete-dolly', function() {
        var tr = $(this).closest('tr');
        var dolly_id = $(this).val();
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'delete_dolly',
            dolly_id: dolly_id,
          },
        }).done(function(result) {
          tr.remove();
        });
      });

      $(document).on('input', '.dolly-name', function() {
        var tr = $(this).closest('tr');
        var dolly_id = tr.attr('id').replace('tr_dolly_', '');
        var dolly_name = $(this).val();
        update_dolly(dolly_id, 'name', dolly_name);
      });

      /*
      Reason
      */
      function update_reason(reason_id, field, value) {
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'update_reason',
            reason_id: reason_id,
            field: field,
            value: value,
          },
        }).done(function(result) {
          //console.log(result);
        });
      }

      $(document).on('click', '.add-reason', function() {
        var tr = $(this).closest('tr');
        var reason_id = tr.attr('id').replace('tr_reason_', '');
        var reason_name = tr.find('input[name="reason_name"]').val();
        // var color = tr.find('input[name="reason_color"]').val();
        if (reason_name == "") {
          tr.find('input[name="reason_name"]').focus();
          return false;
        }
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'save_reason',
            reason_id: reason_id,
            reason_name: reason_name,
            // color: color,
          },
        }).done(function(result) {
          if (reason_id == 0) {
            tr.find('input[name="reason_name"]').val('');
            // tr.find('input[name="reason_color"]').val('');
            $("#reason_table").find('tbody').append(result);
          }
        });
      });

      $(document).on('click', '.delete-reason', function() {
        var tr = $(this).closest('tr');
        var reason_id = $(this).val();
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'delete_reason',
            reason_id: reason_id,
          },
        }).done(function(result) {
          tr.remove();
        });
      });

      $(document).on('input', '.reason-name', function() {
        var tr = $(this).closest('tr');
        var reason_id = tr.attr('id').replace('tr_reason_', '');
        var reason_name = $(this).val();
        update_reason(reason_id, 'name', reason_name);
      });

      /*
      Part to Kanban
       */
      read_part2kanban();

      $(document).on('click', '.save-part2kanban', function() {
        var tr = $(this).closest('tr');
        var item_id = tr.attr('id').replace('tr_p2k_', '');
        var kanban = tr.find('input[name="kanban"]').val();
        var part_number = tr.find('input[name="part_number"]').val();
        var dolly = tr.find('input[name="dolly"]').val();
        var barcode = tr.find('input[name="barcode"]').val();
        var pick_address = tr.find('input[name="pick_address"]').val();
        var delivery_address = tr.find('input[name="delivery_address"]').val();
        var delivery_address2 = tr.find('input[name="delivery_address2"]').val();
        var pick_seq = tr.find('input[name="pick_seq"]').val();
        var min = tr.find('input[name="min"]').val();
        var max = tr.find('input[name="max"]').val();
        if (kanban == "") {
          tr.find('input[name="kanban"]').focus();
          return false;
        }
        if (part_number == "") {
          tr.find('input[name="part_number"]').focus();
          return false;
        }
        if (dolly == "") {
          tr.find('input[name="dolly"]').focus();
          return false;
        }
        // if(barcode == "") {
        //     tr.find('input[name="barcode"]').focus();
        //     return false;
        // }
        if (pick_address == "") {
          tr.find('input[name="pick_address"]').focus();
          return false;
        }
        if (delivery_address == "") {
          tr.find('input[name="delivery_address"]').focus();
          return false;
        }
        // if(delivery_address2 == "") {
        //     tr.find('input[name="delivery_address2"]').focus();
        //     return false;
        // }
        if (pick_seq == "") {
          tr.find('input[name="pick_seq"]').focus();
          return false;
        }

        if (min == "") {
          tr.find('input[name="min"]').focus();
          return false;
        }

        if (max == "") {
          tr.find('input[name="max"]').focus();
          return false;
        }

        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'save_part2kanban',
            item_id: Number(item_id),
            kanban: kanban,
            part_number: part_number,
            dolly: dolly,
            barcode: barcode,
            pick_address: pick_address,
            delivery_address: delivery_address,
            delivery_address2: delivery_address2,
            pick_seq: Number(pick_seq),
            min: Number(min),
            max: Number(max)
          },
        }).done(function(result) {
          console.log(result);
          if (item_id == 0) {
            tr.find('input[name="kanban"]').val('');
            tr.find('input[name="part_number"]').val('');
            tr.find('input[name="dolly"]').val('');
            tr.find('input[name="barcode"]').val('');
            tr.find('input[name="pick_address"]').val('');
            tr.find('input[name="delivery_address"]').val('');
            tr.find('input[name="delivery_address2"]').val('');
            tr.find('input[name="pick_seq"]').val('');
            tr.find('input[name="min"]').val('');
            tr.find('input[name="max"]').val('');
            $("#part2kanban_table").find('tbody').append(result);
          } else {
            var audio = new Audio('assets/audio/sound.mp3');
            audio.play();
            alert('Saved successfully');

          }
        });
      });

      $(document).on('click', '.delete-part2kanban', function() {
        var tr = $(this).closest('tr');
        var item_id = $(this).val();
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'delete_part2kanban',
            item_id: item_id,
          },
        }).done(function(result) {
          tr.remove();
          alert("Delete Successed");
          var audio = new Audio('assets/audio/sound.mp3');
          audio.play();
        });
      });

      $("#save_opr_pick_setting").on('click', function() {
        var opr_pick_value = $("#opr_pick_value").val();
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
      });

      $("#save_opr_delivery_setting").on('click', function() {
        var opr_deliver_value = $("#opr_delivery_value").val();
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
      });

      $("#driver_setting").on('click', function() {
        var driver_value = $("#driver_value").val();
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'save_driver_setting',
            val: driver_value
          },
        }).done(function(result) {
          if (result == 'Ok') {
            alert("Saved successfully")
          } else {
            alert("Failed to save")
          }
        });
      });

      $("#pause_setting").on('click', function() {
        var pause_value = $("#pause_value").val();
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'save_setting',
            set_type: 'pause_time',
            set_value: pause_value,
          },
        }).done(function(result) {
          if (result == "Ok")
            alert('Saved successfully');
          else
            alert('Save failed');
        });
      });

      $("#save_cycle_setting").on('click', function() {
        var cycle_value = $("#cycle_value").val();
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
      });

      $("#save_tag").on('click', function() {
        var tag_value = $("#tag_value").val();
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'save_tag',
            tag_value: tag_value
          },
        }).done(function(result) {
          if (result == 'Ok') {
            alert("Saved successfully")
          } else {
            alert("Failed to save")
          }
        });
      });

      $("#clear_kanban").on('click', function() {
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: "clear_part2kanban",
            // page: 'Stocking'
          },
          dataType: 'HTML',
        }).done(function(response) {
          if (response == 'Ok') {
            alert("Cleared successfully");
            read_part2kanban();
          } else {
            alert("Failed to clear");
          }
        });
      });
      //import csv for part to kanban
      $("#import_kanban_csv").on('click', function() {
        if ($("#part2kanban_file").val() == '') {
          alert('Please select csv file');
          return false;
        }
        var file_data = $("#part2kanban_file").prop('files')[0];
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
            console.log(result, 'import kanban data');
            if (result == "Success") {
              alert('Imported successfully');
              read_part2kanban();
            } else
              alert('Import failed');
            $("#part2kanban_file").val("");
          }
        });
      });

      function read_part2kanban() {
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'read_part2kanban',
          },
          dataType: 'HTML',
        }).done(function(result) {
          $("#part2kanban_table").find('tbody').html(result);
        });
      }

      read_opr_settings();

      function read_opr_settings() {
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'get_opr_settings',
          },
          dataType: 'HTML',
        }).done(function(response) {
          var data = JSON.parse(response);
          $("#cycle_value").val(data.opr_cycle_settings);
          $("#opr_pick_value").val(data.opr_pick_settings);
          $("#opr_delivery_value").val(data.opr_del_settings);
          $("#driver_value").val(data.driver_settings);
        });
      }


    });
  </script>
  <script>
    $(function() {
      let periodStart;
      let periodEnd;
      $('input[name="daterange"]').daterangepicker({
        opens: 'center',
        locale: {
          format: 'DD/MM/YYYY' // set the format to "DD/MM/YYYY"
        }
      }, function(start, end, label) {
        console.log(start)
        periodStart = start.format('YYYY-MM-DD');
        periodEnd = end.format('YYYY-MM-DD');
        console.log("A new date selection was made: " + periodStart + ' to ' + periodEnd);
        $("#clear_select_item").on('click', function() {
          $.ajax({
            url: "actions.php",
            method: "post",
            data: {
              action: 'delete_periodData',
              periodStart: periodStart,
              periodEnd: periodEnd
            },
          }).done(function(result) {
            console.log(result);
            if (result == "OK")
              alert('Delete successfully');
            else
              alert('Delete failed');
          });
        })

      });
      $("#clear_items").on('click', function() {
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'delete_EveryData',
          },
        }).done(function(result) {
          console.log(result);
          if (result == "OK")
            alert('Delete successfully');
          else
            alert('Delete failed');
        });
      })
      // Bind a change event listener to the input element
      $("#kanbanAdd").on('click', function() {
        // When the value of the input changes, play the sound
        var audio = new Audio('assets/audio/sound.mp3');
        audio.play();
      });
      //Clear all list
      $("#clear_list").on('click', function() {
        $("#clear_modal").modal();
      });

      $('#reset_fill_system').click(function(){
        $.ajax({
          url: "actions.php",
          method: "post",
          data: {
            action: 'reset_fill_system',
          },
        }).done(function(res) {
          if(res == 'success'){
            alert('System fill successfully reseted');
          }else{
            alert('Reset failed');
          }
        });
      });
    });
  </script>

</body>

</html>