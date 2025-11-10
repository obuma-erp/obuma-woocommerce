<?php 	
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once "../wp-load.php";

global $wpdb;




$wpdb->query("ALTER TABLE ".$wpdb->prefix."posts DROP obuma_id_product");

$wpdb->query("ALTER TABLE ".$wpdb->prefix."terms DROP obuma_id_category");
    
$wpdb->query("ALTER TABLE ".$wpdb->prefix."users DROP obuma_id_customer");
  		
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_order");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_log_order");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_vincular_categorias");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_comunas");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_log_webhook");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_log_synchronization");


