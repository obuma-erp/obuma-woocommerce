<?php 

// Top level menu del plugin
function ob_menu_administrador(){
	add_menu_page("Obuma Sync","Obuma Sync",'manage_options','configuracion','load_configuracion');
	add_submenu_page('configuracion','Configurar','Configurar','manage_options','configuracion','load_configuracion');
	add_submenu_page('configuracion','Sincronizar','Sincronizar','manage_options','sincronizar','load_sincronizar');
	add_submenu_page('configuracion','Vincular categorías','Vincular categorías','manage_options','vincular_categorias','load_vincular_categorias');
	add_submenu_page('configuracion','Log de sincronizaci&oacute;n','Log de sincronizaci&oacute;n','manage_options','log_sincronizacion','load_log_sincronizacion');
	add_submenu_page('configuracion','Log de órdenes','Log de órdenes','manage_options','log_ordenes','load_log_ordenes');
	add_submenu_page('configuracion','Log de webhook','Log de webhook','manage_options','log_webhook','load_log_webhook');
}

add_action('admin_menu', 'ob_menu_administrador');

?>