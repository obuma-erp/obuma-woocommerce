<?php
require_once "../../../../wp-load.php";

function obtener_categorias(){

	$option_seleccionar_taxonomias = get_option("seleccionar_taxonomias");

	if(isset($_POST["obtener"])) {
		global $wpdb;
		$indice = 0;
		$categorias_vinculadas = [];
		$categorias = $wpdb->get_results("SELECT categoria_woocommerce_id,obuma_id_category  as producto_categoria_id,categoria_woocommerce_nombre as producto_categoria_nombre FROM ".$wpdb->prefix."obuma_vincular_categorias WHERE obuma_id_category > 0 ORDER BY categoria_woocommerce_nombre ASC");

		foreach ($categorias as $cat) {
			$categorias_taxonomy = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."terms t INNER JOIN " .$wpdb->prefix. "term_taxonomy tt ON t.term_id=tt.term_id WHERE t.term_id='".$cat->categoria_woocommerce_id."'");

			if (count($categorias_taxonomy) > 0) {
				$categorias_vinculadas[$indice]["producto_categoria_id"] = $cat->producto_categoria_id;
				$categorias_vinculadas[$indice]["producto_categoria_nombre"] = $cat->producto_categoria_nombre;
				$indice++;
			}
		}
		
		echo json_encode($categorias_vinculadas);

		}
}


obtener_categorias();