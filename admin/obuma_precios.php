<?php 

require_once "obuma_conector.php";
require_once "functions.php";

$resumen= array();
$resumen["resumen"] = []; 
$indice = 0;
$log = array();
$indice_log = 0;
$mensaje = "";
$cantidad_paginas = 0;
$result = array();

$sqls = array();

$pagina = obtener_numero_pagina($_POST["pagina"]);

$url = 	set_url()."productosConsultaPrecios.list.json";

$json = verificar_categorias_seleccionadas($url,$_POST["categorias_seleccionadas"],"precios");
$json = json_encode($json, true);
$json = json_decode($json, true);
$data_precios = $json["data"];
$cantidad_paginas = $json["data-total-pages"];



//Variables log de sincronizacion:

$log_synchronization_type = "Product price";
$log_synchronization_option = "All categories";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";



if ($cantidad_paginas > 0) {
	
	foreach ($data_precios as $data) {
		$producto_id = $data["producto_id"];
		$producto_codigo_comercial = trim($data["producto_codigo_comercial"]);
		$producto_precio_clp_neto = $data["producto_precio_clp_neto"];
		$producto_precio_clp_iva = $data["producto_precio_clp_iva"];
		$producto_precio_clp_total = $data["producto_precio_clp_total"];

		if (isset($producto_codigo_comercial) && !empty($producto_codigo_comercial)) {
			$pro = existe_producto_sku($producto_codigo_comercial);
			if($pro !== false){
				
					$id_producto = wc_get_product_id_by_sku($producto_codigo_comercial);

					$precio_aplicar = $producto_precio_clp_total;
					if (get_option("sincronizar_precio") == 1) {
						$precio_aplicar = $producto_precio_clp_neto;
					}


					if($id_producto > 0){

						$producto = wc_get_product($id_producto);

						$producto->set_regular_price($precio_aplicar);
						$producto->set_sale_price($precio_aplicar);
						$producto->set_price($precio_aplicar);

						$producto->save();


					}else{

						update_post_meta($pro[0]->ID, '_regular_price', $precio_aplicar);
						update_post_meta($pro[0]->ID, '_sale_price', $precio_aplicar);
						update_post_meta($pro[0]->ID, '_price',  $precio_aplicar);

					}
					
					//Actualizar precio en tabla wc_product_meta_lookup
					$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."wc_product_meta_lookup  SET max_price=%d WHERE product_id=%d",$precio_aplicar,$pro[0]->ID));
					

					
					$resumen["resumen"][$indice]["name"] = $pro[0]->post_title;
					$resumen["resumen"][$indice]["action"] = "actualizado";
					$indice++;
					
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


$result = array("completado" => $pagina,"total" => $cantidad_paginas,"resumen" => $resumen , "log" => $log);


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

?>