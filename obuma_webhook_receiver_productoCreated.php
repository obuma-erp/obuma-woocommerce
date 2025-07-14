<?php 
require_once "../../../wp-load.php";
require_once "admin/obuma_conector.php";
require_once "admin/functions.php";

$requestBody = file_get_contents('php://input');

date_default_timezone_set('America/Santiago');

$client_secret = get_option("api_key");
echo '<br>$client_secret : '.$client_secret;

$decodedBody = json_decode($requestBody, true);

$eventId = $decodedBody['eventId'];
$eventType = $decodedBody['eventType'];
$eventDate = $decodedBody['eventDate'];

$data = $decodedBody['eventData'];
$data = stripslashes($data);
$data = json_decode($data, true);

echo '<br>eventId : '.$eventId;
echo '<br>eventType : '.$eventType;

//***************************************************************************************************
// Signature
//***************************************************************************************************

$headerSignature = $_SERVER['HTTP_OBUMA_WEBHOOK_SIGNATURE'];
echo '<br>signature received: '.$headerSignature;

$signature = $eventDate.$eventId;
$hmac_result = hash_hmac("sha256", $signature, $client_secret, true);
$generatedSignature = base64_encode($hmac_result);
echo '<br>signature generated: '.$generatedSignature;


if ($generatedSignature !== $headerSignature) {
	echo '<br>Error... signature verification failed';	
	exit;

} else {

	echo '<br>Signature ok...';
			
	$producto_id = trim($data["producto_id"]);
	$producto_codigo_comercial = trim($data["producto_codigo_comercial"]);
	$producto_nombre = trim($data["producto_nombre"]);
	$producto_categoria = trim($data["producto_categoria"]);
	$producto_descripcion = trim($data["producto_descripcion"]);
	$producto_descripcion_larga = html_entity_decode($data["producto_descripcion_larga"]);
	$result = [];

	if(isset($producto_codigo_comercial ) && !empty($producto_codigo_comercial) &&  !empty($producto_nombre)){

		$existe = existe_producto_sku($producto_codigo_comercial);

		$opciones_activas = json_decode(get_option("product_info_sync"), true) ?? [];
		
		if ($existe == false) {

			try{

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

				$agregados++;
		    					
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

				$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wc_product_meta_lookup VALUES(%d,%s,%d,%d,%d,%d,%d,%d,%s,%d,%d,%d,%s,%s)",$post_id,$producto_codigo_comercial,0,0,0,0,1,0,'instock',0,0,0,'taxable',''));


				$result[]["message"] = "Success";

			} catch (Exception $e) {
					$result[]["message"] = $e->getMessage();
					$result[]["code"] = $e->getCode();
					$result[]["file"] = $e->getFile();
					
			}




		}else{
			$result[]["message"] = "El codigo comercial del producto ya existe en woocommerce";
		}

	}else{
		$result[]["message"] = "El codigo comercial del producto no existe , esta vacio o el nombre del producto esta vacio";  
	}


	$result[]["producto_codigo_comercial"] = $producto_codigo_comercial;
	$result[]["producto_id"] = $producto_id; 

	$table_obuma_log_webhook = $wpdb->prefix . 'obuma_log_webhook';
	$wpdb->query("INSERT INTO {$table_obuma_log_webhook} 
	    								  SET 
	    								  fecha='".date('Y-m-d')."', 
	    								  hora='".date('H:i:s')."',
	    								  peticion='".json_encode($requestBody, JSON_PRETTY_PRINT)."',
	    								  tipo='Crear producto',
	    								  resultado='".json_encode($result, JSON_PRETTY_PRINT)."'");

}