<?php 
require_once "../../../wp-load.php";
require_once "admin/obuma_conector.php";
require_once "admin/functions.php";

// Get contents of webhook request
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

	$cliente_id = $data["cliente_id"];
	$cliente_razon_social = trim($data["cliente_razon_social"]);
	$cliente_email = trim($data["cliente_email"]);


	if(!empty($cliente_razon_social) && is_valid_email($cliente_email)){

		$cliente_existe = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."users WHERE obuma_id_customer > 0 AND  obuma_id_customer='".$cliente_id."' LIMIT 1");

		if(count($cliente_existe) == 1){

			$result = [];

			try{

				update_user_meta($cliente_existe[0]->ID,"first_name",$cliente_razon_social);
				update_user_meta($cliente_existe[0]->ID,"last_name","");

	    		$result[]["message"] = "Success";
				$result[]["cliente_email"] = $cliente_email;
				$result[]["cliente_id"] = $cliente_id;

			} catch (Exception $e) {

				$result[]["message"] = $e->getMessage();
				$result[]["code"] = $e->getCode();
				$result[]["file"] = $e->getFile();
				$result[]["cliente_email"] = $cliente_email;
				$result[]["cliente_id"] = $cliente_id;

			}


			$table_obuma_log_webhook = $wpdb->prefix . 'obuma_log_webhook';
		    $wpdb->query("INSERT INTO {$table_obuma_log_webhook} 
		    								  SET 
		    								  fecha='".date('Y-m-d')."', 
		    								  hora='".date('H:i:s')."',
		    								  peticion='".json_encode($requestBody, JSON_PRETTY_PRINT)."',
		    								  tipo='Actualizar cliente',
		    								  resultado='".json_encode($result, JSON_PRETTY_PRINT)."'");
		    
		}

	}

}
