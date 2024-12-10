<?php
require_once("config.php");
require_once("functions.php");
$page_name = "Sequence Control Andon";
$_SESSION['page'] = 'sequence_control_andon.php';
require_once("assets.php");
?>

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
            <div class="content">
                <div class="container-fluid">
                    <div class="border m-2 p-2 h2 font-weight-bold text-uppercase" style="border-radius: 15px">
                        <div class="row m-2 p-2">
                            <div class="col-sm-3">
                                <div class="row">
                                    <p>Live Build: </p>
                                    <p class="bg-primary"><?php echo get_build_amount(); ?></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <p>Sequence Control Andon</p>
                            </div>
                            <div class="col-sm-3 text-right">
                                <button class="btn bg-gray rounded-circle" style="margin-left: 20px; width:3vw; height:3vw" id="btn_prev">
                                    <p class="h1 font-weight-bold">
                                        < </p>
                                </button>
                                <button class="btn bg-gray rounded-circle" style="margin-left: 20px; width:3vw; height:3vw" id="btn_next">
                                    <p class="h1 font-weight-bold">
                                        > </p>
                                </button>
                            </div>
                        </div>
                        <div class="row" id="sequence_table">

                        </div>
                    </div>
                    <!-- /.card-body -->


                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <?php include("footer.php"); ?>
    </div>

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="assets/js/adminlte.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        const table_data = {
            headers: [{}, {}, {}, {}, {}],
            rows: [
                [{}, {}, {}, {}, {}],
                [{}, {}, {}, {}, {}],
                [{}, {}, {}, {}, {}],
                [{}, {}, {}, {}, {}],
                [{}, {}, {}, {}, {}],
            ],
            page: -100000,
            rows_per_page: 5,
            live_build: 0,
            cycle_setting: 15,
            result: {},
        }

        const status = [{
                id: 0,
                name: 'to be picked'
            },
            {
                id: 1,
                name: 'picking'
            },
            {
                id: 2,
                name: 'picking in lane'
            },
            {
                id: 3,
                name: 'delivered'
            },
            {
                id: 4,
                name: 'progress / andon'
            },
        ]

        $(document).ready(function() {

            read_data();

            $('#btn_next').on('click', function() {
                table_data.page = table_data.page + 1;
                // if (table_data.page == 0) table_data.page = 1
                draw_table_new()
            })

            $('#btn_prev').on('click', function() {
                // if (table_data.page == 0) return
                // console.log(table_data.page)
                table_data.page = table_data.page - 1;
                // if (table_data.page == 0) table_data.page = -1
                draw_table_new()
            })

            function draw_table_new() {
                const {
                    result,
                    cycle_setting
                } = table_data
                result.map(v => {
                    let status_id = 0;
                    const {
                        is_completed,
                        cycle,
                        count,
                        is_delivered
                    } = v
                    if (is_delivered == count) {
                        status_id = 3
                    } else if (is_completed == count) {
                        status_id = 2;
                    } else if (is_completed > 0) {
                        status_id = 1
                    }
                    v.status_id = status_id

                    return {
                        count: ~~v.count,
                        cycle: ~~v.cycle,
                        status_id: ~~status_id,
                        is_completed: ~~v.is_completed,
                        is_delivered: ~~v.is_delivered,
                        is_help: ~~v.is_help,
                    }
                })
                console.log(result)
                let first_picking_col = result.filter(v => v.status_id == 1)
                if (first_picking_col.length == 0) {
                    first_picking_col = 0
                } else {
                    first_picking_col = ~~first_picking_col[0].cycle - 1

                }
                // alert(`${table_data.page}  ${first_picking_col}`)
                if (table_data.page < -10000) {
                    table_data.page = first_picking_col - 1;
                } else {
                    first_picking_col += table_data.page

                }
                console.log(">>", `${table_data.page}  ${first_picking_col}`)
                if (first_picking_col < 0) {
                    table_data.page = table_data.page + 1;
                    console.log(">>2", `${table_data.page}  ${first_picking_col}`)
                    return;
                }
                // if (first_picking_col > result.length - 5) {
                //     table_data.page--;
                //     return;
                // }
                console.log(`${table_data.page}  ${first_picking_col}`)

                // alert(`${table_data.page}  ${first_picking_col}`)
                // alert(first_picking_col)
                // console.log(cycle_setting, first_picking_col, cycle_setting * (first_picking_col + 0 - 1) + 1)
                table_data.headers = [{
                    data: 'Engine Count'
                }]
                for (let i = 0; i < 5; i++) {

                    table_data.headers.push({
                        data: `${cycle_setting*(first_picking_col + i ) + 1} - ${cycle_setting*(first_picking_col + i+1 )}`
                    })
                }
                table_data.rows = [];
                for (let y = 0; y < 5; y++) {
                    const row = [{
                        data: status[y].name
                    }]
                    for (let x = 0; x < 5; x++) {
                        const realX = x + first_picking_col;
                        if (realX > result.length - 1) continue
                        const cell = {}
                        switch (result[realX].status_id) {
                            case 0:
                                cell.class = "text-gray-500"
                                break;
                            case 1:
                                cell.class = "text-white bg-red-500";
                                break;
                            case 3:
                                cell.class = "text-green-700";
                                if (y == 4) cell.class = "bg-green-700"
                                break;

                        }
                        if (result[realX].is_help > 0 && result[realX].status_id < 2) {
                            cell.class += " bg-red-500"
                        }
                        if (result[realX].status_id == y) {
                            cell.data = `cycle  ${realX +1}`
                            // alert(result[realX].status_id)

                        }
                        row.push(cell)
                    }
                    table_data.rows.push(row)
                }
                console.log(table_data.rows)
                let html = ` <div class="table-responsive table-bordered">
                                <table rules="all" class="table text-center" style="border-collapse: separate; border: 1px solid; border-radius: 10px; border-spacing: 0; border-color: #DDDDDD;">
                                    <tbody>
                                    
                                            
                                    `;
                html += `<tr></tr>`
                table_data.headers.map(v => {
                    if (v == "") return

                    html += `<td class="${(v.class)}">${v.data || ""}</td>`
                })
                html += `<tr></tr>`


                table_data.rows.map(row => {
                    html += `<tr></tr>`
                    // console.log(row)
                    row.map(v => {
                        if (v == "") return

                        html += `<td class="${(v.class)}">${v.data || ""}</td>`
                    })
                    html += `<tr></tr>`
                })


                html += `
                            </tbody>
                                </table>
                            </div>
                `
                $('#sequence_table').html(html)
            }

            setInterval(read_data, 10000)

            function read_data() {

                const today = new Date();
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();
                const formattedToday = yyyy + '-' + mm + '-' + dd;
                //console.log(formattedToday)
                $.ajax({
                    url: "actions.php",
                    method: "post",
                    data: {
                        action: 'get_sequence_control_addon',
                        // today: '2023-2-10'
                        today: formattedToday,
                    },
                    dataType: 'HTML',
                }).done(function(result) {
                    result = JSON.parse(result)
                    console.log(result)
                    const cycle_setting = ~~result.cycle_setting;
                    result = result.sequence;
                    table_data.result = result;
                    table_data.cycle_setting = cycle_setting
                    draw_table_new()
                    // const {
                    //     picking_count,
                    //     delivered_count,
                    //     picked_count,
                    //     cycle_setting,
                    //     live_build
                    // } = result;
                    //console.log(picking_count,
                    // delivered_count,
                    // picked_count,
                    // cycle_setting,
                    // live_build) 
                    /*table_data.live_build = ~~live_build;
                    table_data.cycle_setting = ~~cycle_setting;
                    const col_counts = ~~((live_build - 0.1) / cycle_setting + 1);
                    table_data.rows = []
                    for (let i = 0; i < 5; i++) {
                        const _row = [];
                        for (let j = 0; j < col_counts; j++) {
                            _row.push({})
                        }
                        table_data.rows.push(_row)
                    }
                    table_data.headers = (Array(col_counts + 5).fill(""))


                    function get_col(num) {
                        return ~~((num - 0.1) / cycle_setting) + 1
                    }

                    const pick_col = get_col(picked_count);
                    const del_col = get_col(delivered_count);
                    const picking_col = get_col(picking_count);
                    //console.log(pick_col, del_col, picking_col, get_col(22))
                    if (pick_col != del_col && pick_col < picking_col) {
                        table_data.rows[2][pick_col - 1] = {
                            data: "cycle " + pick_col
                        }

                        table_data.rows[4][pick_col - 1] = {
                            class: "bg-green-500"
                        }
                    }
                    if (del_col < picking_col) {
                        table_data.rows[3][del_col - 1] = {
                            class: "text-green-500 bold",
                            data: "cycle " + del_col
                        }
                        table_data.rows[4][del_col - 1] = {
                            class: "bg-green-500"
                        }

                    }


                    table_data.rows[1][picking_col - 1] = {
                        data: 'cycle ' + picking_col,
                        class: " text-white bg-red-400"
                    }

                    for (let i = 0; i < 5; i++) {
                        if (table_data.rows[i][picking_col - 1].data == undefined)
                            table_data.rows[i][picking_col - 1] = {
                                class: 'bg-red-400'
                            }
                    }

                    for (let i = picking_col; i < col_counts; i++) {
                        table_data.rows[0][i] = {
                            data: 'cycle ' + (i + 1),
                            class: "text-gray-500"
                        }
                    }

                    //console.log(table_data.rows)
                    setPage(1)
                    //{"picking_count":"22","delivered_count":"0","picked_count":"14","cycle_setting":"15","live_build":"105"}
                    */
                });
            }

            // function setPage(page) {
            //     page = ~~page;
            //     table_data.page = page;
            //     const {
            //         live_build,
            //         cycle_setting
            //     } = table_data
            //     const page_min = 75 * (page - 1);
            //     if (page_min > table_data.live_build) {
            //         // alert('page end')
            //         return false;
            //     }
            //     const page_cycle_min = 5 * (page - 1) + 1
            //     const col_counts = ~~((live_build - 0.1) / cycle_setting + 1);
            //     for (let i = 0; i < col_counts; i++) {
            //         const row_min = page_min + i * table_data.cycle_setting;
            //         if (row_min > table_data.live_build) {
            //             table_data.headers[i] = {}
            //             continue;
            //         }
            //         let row_max = row_min + table_data.cycle_setting;
            //         if (row_max > table_data.live_build) row_max = table_data.live_build;
            //         table_data.headers[i] = `${row_min+1}-${row_max}`
            //     }

            //     // alert(table_data.headers)
            //     drawTable()
            // }

            // function drawTable() {

            //     let {
            //         page,
            //         rows_per_page,
            //         live_build,
            //         rows,
            //         headers
            //     } = table_data

            //     const first_col = [
            //         'to be picked',
            //         'picking',
            //         'picking in lane',
            //         'delivered',
            //         'progress/andon'
            //     ]
            //     let html = ` <div class="table-responsive table-bordered">
            //                     <table rules="all" class="table text-center" style="border-collapse: separate; border: 1px solid; border-radius: 10px; border-spacing: 0; border-color: #DDDDDD;">
            //                         <tbody>
            //                         <tr>
            //                                 <td style="width: 10vw">Engine count</td>
            //                         `;
            //     //console.log(headers)
            //     // alert(headers.length)
            //     // alert(page)
            //     // console.log("headers: ", page, headers);
            //     let _headers = headers.slice((page - 1) * rows_per_page, (page - 1) * rows_per_page + rows_per_page)
            //     // console.log("_headers: ", _headers)
            //     _headers.map(v => {
            //         if (v == "") return
            //         html += `<td class=${(v.class || "") + " uppercase"}>${v || ""}</td>`
            //     })

            //     html += `</tr>`
            //     const data = []
            //     for (let i = 0; i < 5; i++) {

            //         const _rows = [{
            //             data: first_col[i]
            //         }];
            //         for (let j = 0; j < 5; j++) {
            //             const x = j + (page - 1) * rows_per_page
            //             if (x > live_build) continue
            //             //console.log("rows[i,x]", i, x, rows[i][x])
            //             _rows.push(rows[i][x])
            //         }
            //         // //console.log(_rows)
            //         data.push(_rows)
            //     }
            //     //console.log(data)
            //     data.map(row => {
            //         html += `<tr>`
            //         row.map(v => {
            //             if (v != undefined) {
            //                 //console.log(data, row, v)
            //                 html += `<td class="${(v.class || "")}">${v.data || ""}</td>`
            //             } else {
            //                 // html += `<td></td>`
            //             }

            //         })
            //         html += `</tr>`
            //     })

            //     html += `
            //                 </tbody>
            //                     </table>
            //                 </div>
            //     `
            //     $('#sequence_table').html(html)
            //     /*
            //     <div class="table-responsive table-bordered">
            //                     <table rules="all" class="table text-center" style="border-collapse: separate; border: 1px solid; border-radius: 10px; border-spacing: 0; border-color: #DDDDDD;">
            //                         <tbody>
            //                             <tr>
            //                                 <td style="width: 10vw">Engine count</td>
            //                                 <td>0-15</td>
            //                                 <td>16-30</td>
            //                                 <td>31-45</td>
            //                                 <td>46-60</td>
            //                                 <td>61-75</td>
            //                             </tr>
            //                             <tr>
            //                                 <td>To be picked</td>
            //                                 <td></td>
            //                                 <td></td>
            //                                 <td></td>
            //                                 <td>Cycle 4</td>
            //                                 <td>Cycle 5</td>
            //                             </tr>
            //                             <tr>
            //                                 <td>Picking</td>
            //                                 <td></td>
            //                                 <td></td>
            //                                 <td>Cycle 3</td>
            //                                 <td></td>
            //                                 <td></td>
            //                             </tr>
            //                             <tr>
            //                                 <td>Picking in lane</td>
            //                                 <td></td>
            //                                 <td>Cycle 2</td>
            //                                 <td></td>
            //                                 <td></td>
            //                                 <td></td>
            //                             </tr>
            //                             <tr>
            //                                 <td>Delivered</td>
            //                                 <td>Cycle 1</td>
            //                                 <td></td>
            //                                 <td></td>
            //                                 <td></td>
            //                                 <td></td>
            //                             </tr>
            //                             <tr>
            //                                 <td>Progress / Andon</td>
            //                                 <td class="bg-success"></td>
            //                                 <td class="bg-success"></td>
            //                                 <td class="bg-danger"></td>
            //                                 <td></td>
            //                                 <td></td>
            //                             </tr>
            //                         </tbody>
            //                     </table>
            //                 </div>

            //     */
            // }



            // read_data();

            // function read_data() {
            //     const today = new Date();
            //     const yyyy = today.getFullYear();
            //     let mm = today.getMonth() + 1; // Months start at 0!
            //     let dd = today.getDate();
            //     let hr = today.getHours();
            //     let min = today.getMinutes();
            //     let sec = today.getSeconds();
            //     const formattedToday = yyyy + '-' + mm + '-' + dd + ' ' + hr + ":" + min + ":" + sec;

            //     const yesterday = new Date();
            //     yesterday.setDate(today.getDate() - 1);
            //     const yyyy_yt = yesterday.getFullYear();
            //     let mm_yt = yesterday.getMonth() + 1; // Months start at 0!
            //     let dd_yt = yesterday.getDate();
            //     const formattedYesterday = yyyy_yt + '-' + mm_yt + '-' + dd_yt + ' ' + hr + ":" + min + ":" + sec;

            //     $.ajax({
            //         url: "actions.php",
            //         method: "post",
            //         data: {
            //             action: 'get_sequence_control_addon',
            //             today: formattedToday,
            //         },
            //         dataType: 'HTML',
            //     }).done(function(result) {
            //         alert(result);
            //     });
            // }
        });
    </script>
</body>

</html>