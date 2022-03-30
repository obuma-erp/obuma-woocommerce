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

	$items = $data["items"];
	$result = [];

	foreach ($items as $item) {

		$rel_producto_id = $item["rel_producto_id"];
		$rel_bodega_id 	 = $item["rel_bodega_id"];
		$producto_codigo_comercial = $item["producto_codigo_comercial"];

		if ($rel_bodega_id == get_option("id_bodega")) {
			
			if (isset($producto_codigo_comercial) && !empty($producto_codigo_comercial)) {

				try {
					
					$pro = existe_producto_sku($producto_codigo_comercial);

					if($pro !== false){

						$pi_saldo = $item["pi_saldo"];
						
						$id_producto = wc_get_product_id_by_sku($producto_codigo_comercial);

						if($id_producto > 0){
							$producto = wc_get_product($id_producto);
							$producto->set_stock_quantity($pi_saldo);
							$producto->save();
						}else{
							update_post_meta($pro[0]->ID, '_stock', $pi_saldo);
						}
						
						//$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."wc_product_meta_lookup  SET stock_quantity='".$pi_saldo."' WHERE product_id='".$pro[0]->ID."'"));


						
						$result[]["message"] = "Success";
						
					
					}else{

						$result[]["message"] = "El codigo comercial del producto no existe en woocommerce";

					}

				} catch (Exception $e) {
					
					$result[]["message"] = $e->getMessage();
					$result[]["code"] = $e->getCode();
					$result[]["file"] = $e->getFile();

				}

			}else{
				$result[]["message"] = "El codigo comercial del producto no existe o esta vacio";

			}

		}else{
			$result[]["message"] = "El id de la bodega configurada  no coincide con la bodega de la peticion";
			
		}

		$result[]["producto_codigo_comercial"] = $producto_codigo_comercial;
		$result[]["producto_id"] = $rel_producto_id; 

	}


	$table_obuma_log_webhook = $wpdb->prefix . 'obuma_log_webhook';
	    				$wpdb->query("INSERT INTO {$table_obuma_log_webhook} 
	    								  SET 
	    								  fecha='".date('Y-m-d')."', 
	    								  hora='".date('H:i:s')."',
	    								  peticion='".json_encode($requestBody, JSON_PRETTY_PRINT)."',
	    								  tipo='Actualizar stock',
	    								  resultado='".json_encode($result, JSON_PRETTY_PRINT)."'");



}
