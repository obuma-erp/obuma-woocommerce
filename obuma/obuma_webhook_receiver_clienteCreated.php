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

// Save the signature sended
$headerSignature = $_SERVER['HTTP_OBUMA_WEBHOOK_SIGNATURE'];
echo '<br>signature received: '.$headerSignature;

// generate the signature
$signature = $eventDate.$eventId;
$hmac_result = hash_hmac("sha256", $signature, $client_secret, true);
$generatedSignature = base64_encode($hmac_result);
echo '<br>signature generated: '.$generatedSignature;

// verificate the signature
if ($generatedSignature !== $headerSignature) {
	echo '<br>Error... signature verification failed';
	exit;

} else {
	echo '<br>Signature ok...';

	$cliente_id = $data["cliente_id"];
	$cliente_razon_social = trim($data["cliente_razon_social"]);
	$cliente_email = trim($data["cliente_email"]);

	if (empty($cliente_razon_social)  || $cliente_razon_social == "-") {
		$cliente_razon_social = "empty".$contador;
	}
	if (empty($cliente_email) || $cliente_email == "-") {
		$cliente_email = "no-mail".$contador."@gmail.com";
	}

	$user_id = wc_create_new_customer($cliente_email);

	if (!is_wp_error($user_id)) {

		$result = [];
		
		try{

			$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."users SET obuma_id_customer=%d WHERE ID=%d",$cliente_id,$user_id));

			update_user_meta($user_id,"first_name",$cliente_razon_social);
			update_user_meta($user_id,"last_name","");

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
	    								  tipo='CREAR CLIENTE',
	    								  resultado='".json_encode($result, JSON_PRETTY_PRINT)."'");
					
	}

}
