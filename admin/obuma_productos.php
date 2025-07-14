<?php

require_once "obuma_conector.php";
require_once "functions.php";


$productos_categorias_sincronizar = get_option("productos_categorias_sincronizar");

$resumen = array();
$resumen["resumen"] = []; 
$indice = 0;
$log = array();
$indice_log = 0;
$result = array();
$cantidad_paginas = 0;
$sqls = array();

$pagina = obtener_numero_pagina($_POST["pagina"]);

$url = set_url()."productos.list.json";

$json = verificar_categorias_seleccionadas($url,$_POST["categorias_seleccionadas"],"productos",null,null,$productos_categorias_sincronizar);




$json = json_encode($json, true);
$json = json_decode($json, true);
$data_productos = $json["data"];
$cantidad_paginas = $json["data-total-pages"];


//Variables log de sincronizacion:

$log_synchronization_type = "Products";
$log_synchronization_option = "All categories";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";




if($cantidad_paginas > 0){
	
	foreach ($data_productos as $data) {

		$producto_id = trim($data["producto_id"]);

		$producto_codigo_comercial = trim($data["producto_codigo_comercial"]);

		$producto_nombre = trim($data["producto_nombre"]);

		$producto_categoria = trim($data["producto_categoria"]);

		$producto_descripcion = trim($data["producto_descripcion"]);

		$producto_descripcion_larga = html_entity_decode($data["producto_descripcion_larga"]);

		if(isset($producto_codigo_comercial ) && !empty($producto_codigo_comercial) && isset($producto_nombre) &&  !empty($producto_nombre)){
				
				$existe = existe_producto_sku($producto_codigo_comercial);

				$opciones_activas = json_decode(get_option("product_info_sync"), true) ?? [];

				if ($existe !== false) {

					

					// Construimos el array solo con los campos permitidos
					$my_post = array('ID' => $existe[0]->ID);

					
					$my_post['post_title'] = $producto_nombre;
					
					if (in_array('descripcion_larga', $opciones_activas)) {
						$my_post['post_content'] = $producto_descripcion_larga;
					}
					if (in_array('descripcion_corta', $opciones_activas)) {
						$my_post['post_excerpt'] = $producto_descripcion;
					}

					wp_update_post($my_post);

					// Meta campos
					if (in_array('ancho', $opciones_activas)) {
						update_post_meta($existe[0]->ID, '_width', $data["producto_ancho"]);
					}
					if (in_array('alto', $opciones_activas)) {
						update_post_meta($existe[0]->ID, '_height', $data["producto_alto"]);
					}
					if (in_array('largo', $opciones_activas)) {
						update_post_meta($existe[0]->ID, '_length', $data["producto_largo"]);
					}
					if (in_array('peso', $opciones_activas)) {
						update_post_meta($existe[0]->ID, '_weight', $data["producto_peso_fisico"]);
					}

					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET obuma_id_product=%d WHERE ID=%d",$producto_id,$existe[0]->ID));

					$categoria_existe = existe_categoria_vincular($producto_categoria);

					if ($categoria_existe !== false) {

						$terms_by_product = wp_get_object_terms( (int)$existe[0]->ID, $categoria_existe[0]->woocommerce_taxonomy);

						$columns_term_id = array_column($terms_by_product, "term_id");

						if(!in_array((int)$categoria_existe[0]->term_id, $columns_term_id)){

							wp_set_object_terms((int)$existe[0]->ID, (int)$categoria_existe[0]->term_id ,$categoria_existe[0]->woocommerce_taxonomy,false);

						}

						
					}else{

						$categoria_no_definida = existe_sin_categorizar();

						if ($categoria_no_definida !== false) {

							wp_set_object_terms((int)$existe[0]->ID,(int)$categoria_no_definida[0]->term_id, 'product_cat',false);

						}
					}

					
					$resumen["resumen"][$indice]["name"] = $data["producto_nombre"];

					$resumen["resumen"][$indice]["action"] = "actualizado";

					$indice++;

				}else{

					$args = [
						'post_type'    => 'product',
						'post_status'  => 'publish',
					];

					
					
					$args['post_title'] = $producto_nombre;
					
					if (in_array('descripcion_larga', $opciones_activas)) {
						$args['post_content'] = $producto_descripcion_larga;
					}
					if (in_array('descripcion_corta', $opciones_activas)) {
						$args['post_excerpt'] = $producto_descripcion;
					}

					$post_id = wp_insert_post($args);
	    			
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."posts SET obuma_id_product=%d WHERE ID=%d",$producto_id,$post_id));
	    
					
					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'instock');
					update_post_meta( $post_id, 'total_sales', '0' );
					update_post_meta( $post_id, '_downloadable', 'no' );
					update_post_meta( $post_id, '_virtual', 'no' );
					update_post_meta( $post_id, '_regular_price', 0 );
					update_post_meta( $post_id, '_sale_price', 0 );
					update_post_meta( $post_id, '_purchase_note', '' );
					update_post_meta( $post_id, '_featured', 'no' );
					if (in_array('peso', $opciones_activas)) {
						update_post_meta($post_id, '_weight', $data["producto_peso_fisico"]);
					}
					if (in_array('largo', $opciones_activas)) {
						update_post_meta($post_id, '_length', $data["producto_largo"]);
					}
					if (in_array('ancho', $opciones_activas)) {
						update_post_meta($post_id, '_width', $data["producto_ancho"]);
					}
					if (in_array('alto', $opciones_activas)) {
						update_post_meta($post_id, '_height', $data["producto_alto"]);
					}
					update_post_meta( $post_id, '_sku', $producto_codigo_comercial);
					update_post_meta( $post_id, '_product_attributes', array() );
					update_post_meta( $post_id, '_sale_price_dates_from', '' );
					update_post_meta( $post_id, '_sale_price_dates_to', '' );
					update_post_meta( $post_id, '_price', 0 );
					update_post_meta( $post_id, '_sold_individually', 'no' );
					update_post_meta( $post_id, '_manage_stock', 'yes' );
					update_post_meta( $post_id, '_stock', 0);
					update_post_meta( $post_id, '_backorders', 'no' );


					wp_set_object_terms( $post_id, 'simple', 'product_type');
					
					$categoria_existe = existe_categoria_vincular($producto_categoria);
					if ($categoria_existe !== false) {
						wp_set_object_terms((int)$post_id, (int)$categoria_existe[0]->term_id , $categoria_existe[0]->woocommerce_taxonomy,false);
					}else{
						$categoria_no_definida = existe_sin_categorizar();
						if ($categoria_no_definida !== false) {
							wp_set_object_terms((int)$post_id,(int)$categoria_no_definida[0]->term_id, 'product_cat',false);
						}
						
					}


					$result_meta_lookup  = $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wc_product_meta_lookup VALUES(%d,%s,%d,%d,%d,%d,%d,%d,%s,%d,%d,%d,%s,%s)",$post_id,$producto_codigo_comercial,0,0,0,0,1,0,'instock',0,0,0,'taxable',''));
					
					$resumen["resumen"][$indice]["meta_lookup"] = $result_meta_lookup;
					$resumen["resumen"][$indice]["name"] = $data["producto_nombre"];
					$resumen["resumen"][$indice]["action"] = "agregado";
					$indice++;
				}
			
		}
	}



}else{
			$cantidad_paginas = 0;
			$pagina = 0;
}
	
$log[$indice_log]["url"] = $url;
$log[$indice_log]["page"] = $pagina;
$log[$indice_log]["response"] = $json; 
$indice_log++;


$result = array("completado" => $pagina,"total" => $cantidad_paginas,"resumen" => $resumen,"log" => $log);


if($cantidad_paginas > 0 && $pagina == $cantidad_paginas){

	$log_synchronization_result = "Completed";

	$table_obuma_log_synchronization = $wpdb->prefix . 'obuma_log_synchronization';
	$wpdb->query("INSERT INTO {$table_obuma_log_synchronization} 
	    								  SET 
	    								  fecha='".date('Y-m-d')."', 
	    								  hora='".date('H:i:s')."',
	    								  tipo='".$log_synchronization_type."',
	    								  opcion='".$log_synchronization_option."',
	    								  resultado='".$log_synchronization_result."'");
}

echo json_encode($result);
