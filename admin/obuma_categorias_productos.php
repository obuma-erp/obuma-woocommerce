<?php

require_once "obuma_conector.php";
require_once "functions.php";

$resumen = array();
$resumen["resumen"] = []; 
$log = array();
$indice_log = 0;
$indice = 0;

$result = array();
$cantidad_paginas = 0;
$pagina = obtener_numero_pagina($_POST["pagina"]);

$url = set_url()."productosCategorias.list.json";

$json = ObumaConector::get($url."?page=".$pagina,get_option("api_key"));

$json = json_encode($json, true);
$json = json_decode($json, true);
$data_categorias = $json["data"];
$cantidad_paginas = $json["data-total-pages"];


//Variables log de sincronizacion:

$log_synchronization_type = "Categories";
$log_synchronization_option = "All categories";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";


if(isset($data_categorias)){
	foreach ($data_categorias as $data) {
		$producto_categoria_id = $data["producto_categoria_id"];
		$producto_categoria_nombre = trim($data["producto_categoria_nombre"]);

		if (isset($producto_categoria_id) && !empty($producto_categoria_id) && isset($producto_categoria_nombre) && !empty($producto_categoria_nombre)) {
			
			$categoria_existe = existe_categoria($producto_categoria_id,$producto_categoria_nombre);

			if($categoria_existe !== false){


				$taxonomia_a_usar = 'product_cat';

				$obtener_taxonomia_actual = obtenerTaxonomia($categoria_existe[0]->term_id);

				if($obtener_taxonomia_actual != false){
					$taxonomia_a_usar = $obtener_taxonomia_actual;
				}

				$term_data = wp_update_term(
	    			
	    			(int)$categoria_existe[0]->term_id,
	    			$taxonomia_a_usar,
	   			 array( // (optional)
	   			 	'name'=> trim($data["producto_categoria_nombre"]), // (optional)
			        'description'=> $data["producto_categoria_descripcion"], // (optional)
			        'slug' => trim($data["producto_categoria_nombre"])
	    		)
				);


				$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."terms SET obuma_id_category=%d WHERE term_id=%d",$data["producto_categoria_id"],$categoria_existe[0]->term_id));


				$resumen["resumen"][$indice]["name"] = $categoria_existe[0]->name;
				$resumen["resumen"][$indice]["action"] = "actualizado";
				$indice++;


			}else{

				$term_data = wp_insert_term(
	    			trim($data["producto_categoria_nombre"]),
	    			'product_cat',
	   			 array( // (optional)
			        'description'=> $data["producto_categoria_descripcion"], // (optional)
			        'slug' => trim($data["producto_categoria_nombre"])
	    		)
				);

				if (!is_wp_error($term_data)) {
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."terms SET obuma_id_category=%d WHERE term_id=%d",$data["producto_categoria_id"],$term_data["term_id"]));
					update_term_meta($term_data["term_id"], 'order', 0);
					update_term_meta($term_data["term_id"], 'display_type', "");
					update_term_meta($term_data["term_id"], 'thumbnail_id', 0);
					$resumen["resumen"][$indice]["name"] = $data["producto_categoria_nombre"];
					$resumen["resumen"][$indice]["action"] = "agregado";
					$indice++;
				}


			}
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
