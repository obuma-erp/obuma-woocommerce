<?php

require_once "obuma_conector.php";
require_once "functions.php";
require_once "Image.php";

$resumen = array();
$resumen["resumen"] = []; 
$indice = 0;
$log = array();
$indice_log = 0;
$cantidad_paginas = 0;
$mensaje = "";
$result = array();
$json = array();





$pagina = obtener_numero_pagina($_POST["pagina"]);

$url = set_url()."productosImagenes.list.json";
$json = verificar_categorias_seleccionadas($url,$_POST["categorias_seleccionadas"],"imagenes");


$json = json_encode($json, true);
$json = json_decode($json, true);
$data_imagenes_productos = $json["data"];
$cantidad_paginas = $json["data-total-pages"];



//Variables log de sincronizacion:

$log_synchronization_type = "Product Images";
$log_synchronization_option = "All categories";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";




if(isset($data_imagenes_productos) && $cantidad_paginas > 0){

	foreach ($data_imagenes_productos as $key => $value) {
		
		$imagen_url = $value["producto_imagen_url"];

		$existe_product = $wpdb->get_results("SELECT ID FROM  ".$wpdb->prefix."posts WHERE  post_status <> 'trash'  AND post_type='product' AND obuma_id_product='".$value['obuma_id_product']."' LIMIT 1");

		if(count($existe_product) == 1 ){

			if (!empty($imagen_url)) {
				
				if (is_image($imagen_url)) {

					$imagen_explode = explode("/", $imagen_url);

					$imagen_product = end($imagen_explode);

					$existe_imagen = $wpdb->get_results("SELECT ID FROM  ".$wpdb->prefix."posts WHERE post_title='".sanitize_file_name($imagen_product)."' AND post_type='attachment' LIMIT 1");

					if(count($existe_imagen) == 0){

						$imagen_a_copiar = $imagen_url;

						attach_product_thumbnail($existe_product->ID, $imagen_a_copiar, 0);

						$resumen["resumen"][$indice]["name"] = $imagen_url;

						$resumen["resumen"][$indice]["action"] = "actualizado";

						$indice++;
					}


				}


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