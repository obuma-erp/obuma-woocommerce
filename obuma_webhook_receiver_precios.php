<?php 
require_once "../../../wp-load.php";
require_once "admin/obuma_conector.php";
require_once "admin/functions.php";


$requestBody = file_get_contents('php://input');

date_default_timezone_set('America/Santiago');

obuma_webhook_capturar_fatales('Error fatal precio', $requestBody);

$client_secret = get_option("api_key");
echo '<br>$client_secret : '.$client_secret;

$decodedBody = json_decode($requestBody, true);

$eventId = $decodedBody['eventId'];
$eventType = $decodedBody['eventType'];
$eventDate = $decodedBody['eventDate'];

$data = obuma_webhook_event_data($decodedBody);
	
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
	obuma_webhook_log_error('Error firma precio', array(
		'message'        => 'La firma recibida no coincide con la generada',
		'eventId'        => $eventId,
		'eventDate'      => $eventDate,
		'firma_recibida' => empty($headerSignature) ? 'vacia' : 'presente',
	), $requestBody);
	exit;

} else {
	echo '<br>Signature ok...';

	$producto_id = $data["producto_id"];
	$producto_codigo_comercial = $data["producto_codigo_comercial"];
	$producto_precio_clp_neto = $data["producto_precio_clp_neto"];
	$producto_precio_clp_iva = $data["producto_precio_clp_iva"];
	$producto_precio_clp_total = $data["producto_precio_clp_total"];

	$result = [];

	if (isset($producto_codigo_comercial) && !empty($producto_codigo_comercial)) {
		$pro = existe_producto_sku($producto_codigo_comercial);
		
		if($pro !== false){

			try {

				$id_producto = wc_get_product_id_by_sku($producto_codigo_comercial);

				$precio_aplicar = $producto_precio_clp_total;
				
				if (get_option("sincronizar_precio") == 1) {
						$precio_aplicar = $producto_precio_clp_neto;
				}

				$producto = ($id_producto > 0) ? wc_get_product($id_producto) : false;

				if($id_producto > 0 && !$producto){

					$result[]["message"] = "No se pudo cargar el producto en woocommerce (ID ".$id_producto.")";

				}else{

					if($producto){

						$producto->set_regular_price($precio_aplicar);
						//$producto->set_sale_price($precio_aplicar);
						//$producto->set_price($precio_aplicar);
						$producto->save();

					}else{
						update_post_meta($pro[0]->ID, '_regular_price', $precio_aplicar);
						//update_post_meta($pro[0]->ID, '_sale_price', $precio_aplicar);
						//update_post_meta($pro[0]->ID, '_price', $precio_aplicar);
					}


					//Actualizar precio en tabla wc_product_meta_lookup
						$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."wc_product_meta_lookup  SET max_price=%d WHERE product_id=%d",$precio_aplicar,$pro[0]->ID));


					$result[]["message"] = "Success";
				}


			} catch (Throwable $e) {
					// Throwable captura tambien los errores de PHP 8 (TypeError, Error)
					$result[]["message"] = $e->getMessage();
					$result[]["code"] = $e->getCode();
					$result[]["file"] = $e->getFile();
					$result[]["line"] = $e->getLine();

			}


		}else{
			$result[]["message"] = "El codigo comercial del producto no existe en woocommerce";
		}

	}else{
		$result[]["message"] = "El codigo comercial del producto no existe o esta vacio";
	}


	$result[]["producto_codigo_comercial"] = $producto_codigo_comercial;
	$result[]["producto_id"] = $producto_id; 



	$table_obuma_log_webhook = $wpdb->prefix . 'obuma_log_webhook';
	    				$insert_log = $wpdb->query("INSERT INTO {$table_obuma_log_webhook}
	    								  SET 
	    								  fecha='".date('Y-m-d')."', 
	    								  hora='".date('H:i:s')."',
	    								  peticion='".json_encode($requestBody, JSON_PRETTY_PRINT)."',
	    								  tipo='Actualizar precio',
	    								  resultado='".json_encode($result, JSON_PRETTY_PRINT)."'");

	if ($insert_log === false) {
		obuma_webhook_log_insert_fallido('Error log precio', $requestBody, $result);
	}
}
