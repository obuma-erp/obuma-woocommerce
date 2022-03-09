<?php 

// Top level menu del plugin
function ob_menu_administrador(){
	add_menu_page("Obuma Sync","Obuma Sync",'manage_options',RAI_RUTA.'/admin/configuracion.php');
	add_submenu_page(RAI_RUTA . '/admin/configuracion.php','Configurar','Configurar','manage_options',RAI_RUTA . '/admin/configuracion.php');
	add_submenu_page(RAI_RUTA . '/admin/configuracion.php','Sincronizar','Sincronizar','manage_options',RAI_RUTA . '/admin/sincronizar_obuma.php');
	add_submenu_page(RAI_RUTA . '/admin/configuracion.php','Vincular categorías','Vincular categorías','manage_options',RAI_RUTA . '/admin/vincular_categorias.php');
	add_submenu_page(RAI_RUTA . '/admin/configuracion.php','Log de sincronizaci&oacute;n','Log de sincronizaci&oacute;n','manage_options',RAI_RUTA . '/admin/log_sincronizacion.php');
	add_submenu_page(RAI_RUTA . '/admin/configuracion.php','Log de órdenes','Log de órdenes','manage_options',RAI_RUTA . '/admin/log_ordenes.php');
	add_submenu_page(RAI_RUTA . '/admin/configuracion.php','Log de webhook','Log de webhook','manage_options',RAI_RUTA . '/admin/log_webhook.php');
}

add_action('admin_menu', 'ob_menu_administrador');

?>