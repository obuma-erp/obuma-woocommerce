<?php 

// Top level menu del plugin
function ob_menu_administrador(){
	add_menu_page("Obuma Sync","Obuma Sync",'manage_options','obuma_configuracion','load_configuracion');
	add_submenu_page('obuma_configuracion','Configurar','Configurar','manage_options','obuma_configuracion','load_configuracion');
	add_submenu_page('obuma_configuracion','Sincronizar','Sincronizar','manage_options','obuma_sincronizar','load_sincronizar');
	add_submenu_page('obuma_configuracion','Vincular categorías','Vincular categorías','manage_options','obuma_vincular_categorias','load_vincular_categorias');
	add_submenu_page('obuma_configuracion','Log de sincronizaci&oacute;n','Log de sincronizaci&oacute;n','manage_options','obuma_log_sincronizacion','load_log_sincronizacion');
	add_submenu_page('obuma_configuracion','Log de órdenes','Log de órdenes','manage_options','obuma_log_ordenes','load_log_ordenes');
	add_submenu_page('obuma_configuracion','Log de webhook','Log de webhook','manage_options','obuma_log_webhook','load_log_webhook');
	add_submenu_page('obuma_configuracion','Otros','Otros','manage_options','otros','load_otros');

}

add_action('admin_menu', 'ob_menu_administrador');

?>