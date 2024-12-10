<?php
require_once("config.php");
$sess = $_SESSION['page'];
unset($_SESSION['user']);
unset($_SESSION['stocking_action']);

unset($_SESSION['driver_current_zone']);
unset($_SESSION['driver_zone_time']);
unset($_SESSION['driver_zone_background']);
unset($_SESSION['driver_zone_red_status']);
unset($_SESSION['driver_zone_paused_time']);

unset($_SESSION['overview_pick_pick']);
unset($_SESSION['overview_pick_zone']);
unset($_SESSION['overview_pick_cycle']);
unset($_SESSION['overview_pick_kanban']);
unset($_SESSION['overview_pick_user']);
unset($_SESSION['overview_pick_OPR']);

unset($_SESSION['overview_delivery_pick']);
unset($_SESSION['overview_delivery_zone']);
unset($_SESSION['overview_delivery_cycle']);
unset($_SESSION['overview_delivery_kanban']);
unset($_SESSION['overview_delivery_address']);
unset($_SESSION['overview_delivery_user']);
unset($_SESSION['overview_delivery_OPR']);


if ($sess == 'conveyance_delivery.php') {
    header('Location: ./conveyance_delivery.php');
} else if ($sess == 'conveyance_pick.php') {
    header('Location: ./conveyance_pick.php');
}
 else
    header('Location: ./index.php');
