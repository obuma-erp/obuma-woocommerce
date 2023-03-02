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

$url_imagenes_producto = set_url()."productosImagenes.findByProductoId.json";

$pagina = obtener_numero_pagina($_POST["pagina"]);

//Variables log de sincronizacion:

$log_synchronization_type = "Product Images";
$log_synchronization_option = "All categories";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";





if (isset($_POST["categorias_seleccionadas"])) {
		$categorias_seleccionadas = $_POST["categorias_seleccionadas"];
		if ($_POST["categorias_seleccionadas"] == "all") {
			$inicio = $pagina * 100 - 100;
			$con = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."posts WHERE obuma_id_product  > 0  AND post_status <> 'trash'  AND post_type='product'");
			$pro = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."posts WHERE obuma_id_product > 0  AND post_status <> 'trash' AND post_type='product' LIMIT $inicio,100 ");
			$cantidad_paginas = count($con);
			$cantidad_paginas = ceil($cantidad_paginas/100);
		}else{
			$inicio = $pagina * 100 - 100;
			$id_categoria= $wpdb->get_results("SELECT term_id FROM  ".$wpdb->prefix."terms WHERE obuma_id_category > 0 AND obuma_id_category='".$_POST["categorias_seleccionadas"]."'");
			$id_categoria = $id_categoria[0]->term_id;


			$pro = $wpdb->get_results("SELECT p.obuma_id_product,p.ID,p.post_title FROM ".$wpdb->prefix."term_relationships tr INNER JOIN ".$wpdb->prefix."posts p ON tr.object_id=p.ID WHERE tr.term_taxonomy_id='".$id_categoria."' AND p.obuma_id_product > 0  AND p.post_status <> 'trash'  AND p.post_type='product' LIMIT $inicio,100");

			$count_products = $wpdb->get_results("SELECT p.obuma_id_product,p.ID,p.post_title FROM ".$wpdb->prefix."term_relationships tr INNER JOIN ".$wpdb->prefix."posts p ON tr.object_id=p.ID WHERE tr.term_taxonomy_id='".$id_categoria."' AND p.obuma_id_product > 0  AND p.post_status <> 'trash'  AND p.post_type='product'");


			$cantidad_paginas = count($count_products);
			$cantidad_paginas = ceil($cantidad_paginas/100);
		}

}	

		if($cantidad_paginas > 0){
			foreach ($pro as $key => $data) {
				$json2 = ObumaConector::get($url_imagenes_producto.'/'.$data->obuma_id_product,get_option("api_key"));
				$json2 = json_encode($json2, true);
				$json2 = json_decode($json2, true);
				$json[] = $json2;
				if(isset($json2["data"])){
					foreach ($json2["data"] as $r2) {
						$imagen_url = $r2['producto_imagen_url'];
						if (!empty($imagen_url)) {
					    	if (is_image($imagen_url)) {

					    		$imagen_explode = explode("/", $imagen_url);
					    		$imagen_product = end($imagen_explode);

								$cp = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."posts WHERE post_parent='".$data->ID."' AND post_title='".sanitize_file_name($imagen_product)."' LIMIT 1");
					     		if (count($cp) == 0) {
					     			$imagen_a_copiar = $imagen_url;
					     			//$imagen_a_copiar = $url_copiar_imagenes.'/'.$imagen_url;
					     			attach_product_thumbnail($data->ID, $imagen_a_copiar, 0);
					     			$resumen["resumen"][$indice]["name"] = $imagen_url;
									$resumen["resumen"][$indice]["action"] = "actualizado";
									$indice++;
									
					     		}		
					     	}		
					    } 	
				    }
				}
			}
		}else{
			$cantidad_paginas = 0;
			$pagina = 0;
			
		}


$log[$indice_log]["url"]["url_imagenes_producto"] = $url_imagenes_producto;


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