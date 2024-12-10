<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Stocking Levels";
require_once("assets.php");
$week_start = date('Y-m-d', strtotime("this week"));
$week_end = date('Y-m-d', strtotime("+6 days", strtotime($week_start)));
?>
<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="assets/css/base.css">
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

<style>
    #low_content {
        padding: 2%;
    }

    .low-item {
        cursor:pointer;
        text-align: center;
        background: #efa3a3;
        padding: 10px 0 5px 0;
        margin: 2%;
        width: 130px;
        height: 105px;
    }

    #high_content {
        padding: 2%;
    }

    .high-item {
        cursor:pointer;
        text-align: center;
        background: #d5efa7;
        padding: 10px 0 5px 0;
        margin: 2%;
        width: 130px;
        height: 105px;
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
                        <div class="col-sm-12" style="text-transform: uppercase; text-align:center;">
                            <h1 class="m-0" style="display: inline"><?php echo $page_name; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6" style="border-right:1px solid; height:75vh">
                            <div class="col-sm-12" style="text-transform: uppercase; text-align:center;">
                                <h2 class="m-0" style="display: inline">HIGH</h2>
                            </div>
                            <div class="col-sm-9" style="text-transform: uppercase; text-align:center;background-color:#d5efa7;padding:5%;">
                                <h3 class="m-0" style="display: inline">HIGH OVERVIEW TOTAL: <span id="high_count">0</span></h3>
                            </div>
                            <div id="high_content" class="row">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-sm-12" style="text-transform: uppercase; text-align:center;">
                                <h2 class="m-0" style="display: inline">LOW</h2>
                            </div>
                            <div class="col-sm-9" style="text-transform: uppercase; text-align:center;background-color:#efa3a3;padding:5%;float:right;">
                                <h3 class="m-0" style="display: inline">LOW OVERVIEW TOTAL: <span id="low_count">0</span></h3>
                            </div>
                            <div id="low_content" class="row col-sm-12">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <?php include("footer.php"); ?>

        <!-- Modal -->
        <div class="modal fade" id="kanban_detail_modal">
            <div class="modal-dialog" style="max-width: 1000px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">STOCKING LEVEL</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 col-sm-12 d-flex justify-content-center align-items-end">
                                <h2>I&nbsp;&nbsp;N</h2>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="bg-danger text-align">
                                    <h2 id="modal_kanban"></h2>
                                    <h2 id="modal_stock"></h2>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 d-flex justify-content-center align-items-end">
                                <h2>O&nbsp;U&nbsp;T</h2>
                            </div>
                        </div>
                        <br />
                        <br />
                        <div class="row">
                            <div class="col-sm-12 col-md-6" style="border-right:1px solid;">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 text-align">
                                        <h4>TIME / DATE</h4>
                                        <h4 id="in_time"></h4>
                                    </div>
                                    <div class="col-sm-12 col-md-4 text-align">
                                        <h4>MEMBER</h4>
                                        <h4 id="in_member"></h4>
                                    </div>
                                    <div class="col-sm-12 col-md-4 text-align">
                                        <h4>QTY</h4>
                                        <h4 id="in_stock"></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4 text-align">
                                        <h4>TIME / DATE</h4>
                                        <h4 id="out_time"></h4>
                                    </div>
                                    <div class="col-sm-12 col-md-4 text-align">
                                        <h4>MEMBER</h4>
                                        <h4 id="out_member"></h4>
                                    </div>
                                    <div class="col-sm-12 col-md-4 text-align">
                                        <h4>QTY</h4>
                                        <h4 id="out_stock"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success" data-dismiss="modal" style="width: 160px;">OK</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/js/adminlte.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        $(document).ready(function() {
            read_stock_level();

            function read_stock_level() {
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'read_stock_level'
                    }
                }).done(function(res) {
                    res = JSON.parse(res);
                    $('#low_content').html(res.min);
                    $('#low_count').html(res.min.split('<div').length - 1);
                    $('#high_content').html(res.max);
                    $('#high_count').html(res.max.split('<div').length - 1);
                });

            }

        // setInterval(function(){read_stock_level()},5000);
        });

        function kanban_detail(kanban, stock, member, time, outM, outT, outS) {
            $('#modal_kanban').html(kanban);
            $('#modal_stock').html(stock);
            $('#in_time').html(time);
            $('#in_member').html(member);
            $('#out_time').html(outT);
            $('#out_member').html(outM);
            $('#out_stock').html(outS);
            $('#in_stock').html(stock.split('/')[0]);
            $('#kanban_detail_modal').modal();
        }

    </script>
</body>

</html>