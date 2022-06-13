<?php

require_once "functions.php";

$date_now = date('Y-m-d');
$date_future = strtotime('-60 day', strtotime($date_now));
$date_future = date('Y-m-d', $date_future);


$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."obuma_log_order WHERE fecha < %s",$date_future));
$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."obuma_log_webhook WHERE fecha < %s",$date_future));
$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."obuma_log_synchronization WHERE fecha < %s",$date_future));

update_option('update_limpiar_registros_date',date("Y-m-d H:i:s"));

echo json_encode(array("result" => "true","date" => get_option('update_limpiar_registros_date')));
