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

	$result = [];

	if(isset($producto_codigo_comercial ) && !empty($producto_codigo_comercial) &&  !empty($producto_nombre)){
				
		$existe = existe_producto_sku($producto_codigo_comercial);

		if ($existe !== false) {

			try {

				$my_post = array(
				'ID' =>  $existe[0]->ID,
				'post_title'    => $producto_nombre,
				'post_content'  => $producto_descripcion
				);

				wp_update_post($my_post);

				update_post_meta($existe[0]->ID, '_width', $data["producto_ancho"]);
					
				update_post_meta($existe[0]->ID, '_height', $data["producto_alto"]);
					
				update_post_meta($existe[0]->ID, '_length', $data["producto_largo"]);

				update_post_meta($existe[0]->ID, '_weight', $data["producto_peso_fisico"]);
						
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


				$result[]["message"] = "Success";


			} catch (Exception $e) {

					$result[]["message"] = $e->getMessage();

					$result[]["code"] = $e->getCode();

					$result[]["file"] = $e->getFile();

			}


		}else{

			$result[]["message"] = "El codigo comercial del producto no existe en woocommerce";
			
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
	    								  tipo='Actualizar producto',
	    								  resultado='".json_encode($result, JSON_PRETTY_PRINT)."'");
}