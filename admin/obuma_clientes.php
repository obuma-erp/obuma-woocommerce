<?php
require_once "../../../../wp-load.php";
require_once "obuma_conector.php";
require_once "functions.php";

$resumen = array();
$resumen["resumen"] = []; 
$indice = 0;
$log = array();
$indice_log = 0;
$result = array();
$cantidad_paginas = 0;

$pagina = obtener_numero_pagina($_POST["pagina"]);

$url = set_url()."clientes.list.json";
$json = ObumaConector::get($url."?page=".$pagina,get_option("api_key"));
				
	$json = json_encode($json, true);
	$json = json_decode($json, true);
	$data_categorias = $json["data"];
	$cantidad_paginas = $json["data-total-pages"];



	//Variables log de sincronizacion:

$log_synchronization_type = "Customers";
$log_synchronization_option = "";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";





	if(isset($data_categorias)){
		$contador = 1;
		foreach ($data_categorias as $data) {
			$cliente_id = $data["cliente_id"];
			$cliente_razon_social = trim($data["cliente_razon_social"]);
			$cliente_email = trim($data["cliente_email"]);


			if(!empty($cliente_razon_social) && is_valid_email($cliente_email)){

				$cliente_existe = existe_cliente($cliente_email);

				if($cliente_existe !== false){

					update_user_meta($cliente_existe[0]->ID,"first_name",$cliente_razon_social);
					update_user_meta($cliente_existe[0]->ID,"last_name","");

					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."users SET obuma_id_customer=%d WHERE ID=%d",$cliente_id,$cliente_existe[0]->ID));


					$resumen["resumen"][$indice]["name"] = $cliente_razon_social;
					$resumen["resumen"][$indice]["action"] = "actualizado";
					$indice++;
					
				}else{

					$user_id = wc_create_new_customer($cliente_email);
					if (!is_wp_error($user_id)) {

						$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."users SET obuma_id_customer=%d WHERE ID=%d",$cliente_id,$user_id));

						update_user_meta($user_id,"first_name",$cliente_razon_social);
						update_user_meta($user_id,"last_name","");
						
						$resumen["resumen"][$indice]["name"] = $cliente_razon_social;
						$resumen["resumen"][$indice]["action"] = "agregado";
						$indice++;

					}

				}

				$contador++;


			}



		}
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

