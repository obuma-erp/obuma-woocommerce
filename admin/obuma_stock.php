<?php 
require_once "../../../../wp-load.php";
require_once "obuma_conector.php";
require_once "functions.php";

$bodega = get_option("bodega");
$resumen = array();
$resumen["resumen"] = []; 

$indice = 0;
$log = array();
$indice_log = 0;
$cantidad_paginas = 0;
$mensaje = "";
$result = array();	

$pagina = obtener_numero_pagina($_POST["pagina"]);

$url = set_url()."productosStock.list.json";

$json = verificar_categorias_seleccionadas($url,$_POST["categorias_seleccionadas"],"stock",$bodega);

$json = json_encode($json, true);
$json = json_decode($json, true);
$data_stock = $json["data"];
$cantidad_paginas = $json["data-total-pages"];




//Variables log de sincronizacion:

$log_synchronization_type = "Product stock";
$log_synchronization_option = "All categories";
if(isset($_POST["categorias_seleccionadas"])){
	$log_synchronization_option = $_POST['categorias_seleccionadas'] == "all" ? "All categories" : $_POST['categorias_seleccionadas'];
}
$log_synchronization_result = "";



if ($cantidad_paginas > 0) {
	
	foreach ($data_stock as $data) {
		
		$producto_codigo_comercial = trim($data["producto_codigo_comercial"]);
		$producto_stock_minimo = $data["producto_stock_minimo"];
		$producto_stock_ideal = $data["producto_stock_ideal"];
		$producto_stock_actual = $data["stock_actual"];

		if (isset($producto_codigo_comercial) && !empty($producto_codigo_comercial)) {

			$pro = existe_producto_sku($producto_codigo_comercial);
			
			if($pro !== false){
				

				$id_producto = wc_get_product_id_by_sku($producto_codigo_comercial);

					if($id_producto > 0){
						$producto = wc_get_product($id_producto);
						$producto->set_stock_quantity($producto_stock_actual);
						$producto->save();
					}else{
						update_post_meta($pro[0]->ID, '_stock', $producto_stock_actual);
					}

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
?>