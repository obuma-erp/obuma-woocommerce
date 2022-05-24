<?php 	
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once "../wp-load.php";

global $wpdb;

delete_option('rut_empresa');
delete_option('bodega');
delete_option('id_bodega');
delete_option('api_key');
delete_option('url_obuma');
delete_option('sucursal');
delete_option('vendedor');
delete_option('usuario');
delete_option('canal_venta');
delete_option('lista_precio');
delete_option('rebajar_stock');
delete_option('cliente_actualizar_datos');
delete_option('registrar_contabilidad');
delete_option('enviar_email_cliente');
delete_option('registrar_cobro');
delete_option('codigo_forma_pago');
delete_option('tipo_documento');
delete_option('nota_venta_segundo_plano');
delete_option('enviar_ventas_obuma');
delete_option('cambiar_a_completado');
delete_option('sincronizar_precio');
delete_option('seleccionar_taxonomias');
delete_option('update_comunas_date');
delete_option('update_limpiar_registros_date');
delete_option('obuma_plugin_version');



delete_site_option("rut_empresa");
delete_site_option("bodega");
delete_site_option('id_bodega');
delete_site_option("api_key");
delete_site_option("url_obuma");
delete_site_option('sucursal');
delete_site_option('vendedor');
delete_site_option('usuario');
delete_site_option('canal_venta');
delete_site_option('lista_precio');
delete_site_option('rebajar_stock');
delete_site_option('cliente_actualizar_datos');
delete_site_option('registrar_contabilidad');
delete_site_option('enviar_email_cliente');
delete_site_option('registrar_cobro');
delete_site_option('codigo_forma_pago');
delete_site_option('tipo_documento');
delete_site_option('nota_venta_segundo_plano');
delete_site_option('enviar_ventas_obuma');
delete_site_option('cambiar_a_completado');
delete_site_option('sincronizar_precio');
delete_site_option('seleccionar_taxonomias');
delete_site_option('update_comunas_date');
delete_site_option('update_limpiar_registros_date');
delete_site_option('obuma_plugin_version');


$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key=%s OR meta_key=%s OR meta_key=%s OR meta_key=%s",'order_obuma_rut','order_obuma_tipo_documento','order_obuma_giro_comercial','obuma_url_pdf'));

$wpdb->query("ALTER TABLE ".$wpdb->prefix."posts DROP obuma_id_product");

$wpdb->query("ALTER TABLE ".$wpdb->prefix."terms DROP obuma_id_category");
    
$wpdb->query("ALTER TABLE ".$wpdb->prefix."users DROP obuma_id_customer");
  		
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_order");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_log_order");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_vincular_categorias");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_comunas");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_log_webhook");

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."obuma_log_synchronization");


