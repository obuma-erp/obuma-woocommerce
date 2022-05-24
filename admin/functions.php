<?php 

function eliminar_simbolos($string){
 
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä','Ã'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A','A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    $string = str_replace(
        array("\\", "¨", "º","°", "-","_", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "<code>", "]",
             "+", "}", "{", "¨", "´",
             ">", "<","=" ,";", ",", ":","©","³",
             ".", " "),
        ' ',
        $string
    );
return $string;
} 
    
function limpiar_cadena_imagen_post_title($string){
    $string = str_replace(
    array("\\", "¨", "º","°", "~",
             "#", "@", "|", "!", "\"",
            "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "<code>", "]",
             "+", "}", "{", "¨", "´",
             ">", "<","=" ,";", ",", ":","©","³"," "),'',$string);


    $string = str_replace(array("--","---","----","-----","------"),'-',$string);

    return $string;
}

function is_valid_email($str){
  $matches = null;
  return (1 === preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $str, $matches));
}


function set_url(){
    $result = "";
    $url_obuma = get_option("url_obuma");
    $url_obuma_array = str_split($url_obuma);
    $ultimo_caracter = end($url_obuma_array);
    
    if($ultimo_caracter == "/"){
        $result = $url_obuma;
    }else{
        $result = $url_obuma . "/";
    }
    return $result;
}


function total_paginas_first($file,$pagina){
    global $cantidad_paginas;
    $json = ObumaConector::get(set_url().$file,get_option("api_key"));
    $json = json_encode($json, true);
    $json = json_decode($json, true);
    $cantidad_paginas = $json["data-total-pages"];
    echo json_encode(array("total" => $cantidad_paginas, "first" => true,"pagina" => $pagina));
}

function is_image($image){
    $result = false;
    $permit = ["jpg","jpeg","png"];
    $string = explode(".",$image);
    $extension = end($string);
    if (in_array($extension, $permit)) {
        $result = true;
    }
    return $result;
}


function verificar_categorias_seleccionadas($url,$categoria_seleccionada,$nombre,$bodega = null){
    global $pagina;
    $url_final = "";

    if(!isset($categoria_seleccionada)){ 
        $categoria_seleccionada = "all"; 
    }

    $url_final .= $url;

    if ($categoria_seleccionada == "all") {   
        if (trim($nombre) == "stock") {
            $url_final .= "?codigo_bodega=". $bodega . "&stock_mostrar=0&page=" . $pagina;
        }else{
            $url_final .= "?page=" . $pagina;
        }
    }else{
        if (trim($nombre) == "stock") {
            $url_final .= "?codigo_bodega=" . $bodega . "&categoria=" . $categoria_seleccionada . "&stock_mostrar=0&page=" . $pagina;
        }else{
            $url_final .= "?categoria=" . $categoria_seleccionada . "&page=" . $pagina;
        }          
    }
    $json = ObumaConector::get($url_final,get_option("api_key"));   
    return $json;
}

function obtener_numero_pagina($pagina){
    $pag = 1;
    if (isset($pagina)) {
        $pag = (int)$pagina;   
    }
    return $pag;
}


function esRut($r = false){
    if((!$r) or (is_array($r)))
        return false; /* Hace falta el rut */
     
    if(!$r = preg_replace('|[^0-9kK]|i', '', $r))
        return false; /* Era código basura */
     
    if(!((strlen($r) == 8) or (strlen($r) == 9)))
        return false; /* La cantidad de carácteres no es válida. */
     
    $v = strtoupper(substr($r, -1));
    if(!$r = substr($r, 0, -1))
        return false;
     
    if(!((int)$r > 0))
        return false; /* No es un valor numérico */
     
    $x = 2; $s = 0;
    for($i = (strlen($r) - 1); $i >= 0; $i--){
        if($x > 7)
            $x = 2;
            $s += ($r[$i] * $x);
            $x++;
    }
    $dv=11-($s % 11);
    if($dv == 10)
        $dv = 'K';
    if($dv == 11)
        $dv = '0';
    if($dv == $v)
        return number_format($r, 0, '', '.').'-'.$v; /* Formatea el RUT */

    return false;
}
   



function existe_categoria_vincular($producto_categoria){
    global $wpdb;
    $result = false;
    $categoria_existe = $wpdb->get_results("SELECT t.term_id,vco.woocommerce_taxonomy FROM  ".$wpdb->prefix."terms t INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON t.term_id=tt.term_id INNER JOIN  ".$wpdb->prefix."obuma_vincular_categorias vco ON t.term_id = vco.categoria_woocommerce_id  WHERE vco.obuma_id_category > 0 AND vco.obuma_id_category = '".$producto_categoria."'");

    if (count($categoria_existe) > 0) {
        $result = $categoria_existe;
    }
    return $result;
}

function existe_sin_categorizar(){
    global $wpdb;
    $result = false;
    $categoria_no_definida = $wpdb->get_results("SELECT term_id FROM  ".$wpdb->prefix."terms WHERE name='Uncategorized' OR name='Sin categoría'");

    if (count($categoria_no_definida) == 1) {
        $result = $categoria_no_definida;
    }
    return $result;
}

function existe_producto_sku($producto_codigo_comercial){
    global $wpdb;
    $result = false;
    $existe_producto = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts p  INNER JOIN ".$wpdb->prefix."postmeta pm  ON p.ID=pm.post_id WHERE  meta_key='_sku'  AND meta_value='".$producto_codigo_comercial."' AND p.post_status <> 'trash' LIMIT 1");
    
    if (count($existe_producto) == 1) {
        $result = $existe_producto;
    }
    return $result;
}


function existe_categoria($producto_categoria_id,$nombre){
    global $wpdb;
    $result = false;
    $existe_categoria = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."terms WHERE obuma_id_category > 0 AND  obuma_id_category='".$producto_categoria_id."' OR name='".$nombre."'  LIMIT 1");

    if (count($existe_categoria) == 1) {
        $result = $existe_categoria;
    }
    return $result;
}

function existe_cliente($cliente_email){
    global $wpdb;
    $result = false;
    $cliente_existe = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."users WHERE user_email <> '' AND  user_email='".$cliente_email."' LIMIT 1");

    if (count($cliente_existe) == 1) {
        $result = $cliente_existe;
    }
    
    return $result;
}


function obtenerTaxonomia($term_id){
    global $wpdb;
    
    $result = false;
    $existe_taxonomia = $wpdb->get_results("SELECT taxonomy FROM ".$wpdb->prefix."term_taxonomy WHERE term_id='".$term_id."' LIMIT 1",ARRAY_A);

    if (count($existe_taxonomia) == 1) {
        $result = $existe_taxonomia[0]["taxonomy"];
    }

     return $result;
}

function getObumaIdCategory($term_id){
    global $wpdb;
    $result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."obuma_vincular_categorias WHERE categoria_woocommerce_id='".$term_id."' LIMIT 1",ARRAY_A);
    return $result;
}

function vinculadas($taxonomy){
    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."terms t INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON t.term_id=tt.term_id INNER JOIN  ".$wpdb->prefix."obuma_vincular_categorias vco ON t.term_id = vco.categoria_woocommerce_id  WHERE tt.taxonomy='".$taxonomy."'  AND vco.obuma_id_category > 0");
    return count($result);
}



function set_comunas(){

  global $wpdb;

  $table_comunas_obuma = $wpdb->prefix . 'obuma_comunas';


  $comunas_nube = file_get_contents("https://obuma-cl.s3.us-east-2.amazonaws.com/cdn-utiles/pg-comunas.list.json");
  $data = json_decode($comunas_nube, TRUE); 

  if(isset($data)){
      foreach ($data as $key => $value) {

        $data_comuna = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."obuma_comunas WHERE pg_comuna_id='".$value['pg_comuna_id']."'");

        if (count($data_comuna) == 0) {

          $sql_comunas_obuma = "INSERT INTO {$table_comunas_obuma} SET
                                                                      pg_comuna_id='{$value['pg_comuna_id']}',
                                                                      pg_comuna_nombre='{$value["pg_comuna_nombre"]}',
                                                                      pg_comuna_distancia_santiago='{$value["pg_comuna_distancia_santiago"]}',
                                                                      rel_pg_region_id={$value["rel_pg_region_id"]},
                                                                      pg_comuna_codigo_dt='{$value["pg_comuna_codigo_dt"]}',
                                                                      pg_comuna_codigo_chilexpress='{$value["pg_comuna_codigo_chilexpress"]}',
                                                                      pg_comuna_codigo_ine='{$value["pg_comuna_codigo_ine"]}'";
          
        }else{
            $sql_comunas_obuma = "UPDATE {$table_comunas_obuma} SET
                                                                      pg_comuna_nombre='{$value["pg_comuna_nombre"]}',
                                                                      pg_comuna_distancia_santiago='{$value["pg_comuna_distancia_santiago"]}',
                                                                      rel_pg_region_id={$value["rel_pg_region_id"]},
                                                                      pg_comuna_codigo_dt='{$value["pg_comuna_codigo_dt"]}',
                                                                      pg_comuna_codigo_chilexpress='{$value["pg_comuna_codigo_chilexpress"]}',
                                                                      pg_comuna_codigo_ine='{$value["pg_comuna_codigo_ine"]}'
                                                                      WHERE pg_comuna_id='{$value['pg_comuna_id']}'";
          
        }


        $wpdb->query($sql_comunas_obuma);
      }
  }


}


function check_version(){

    $response = file_get_contents("https://obuma-cl.s3.us-east-2.amazonaws.com/cdn-utiles/versions_plugin_woocommerce.json");

    $response_decode = json_decode($response,true);

    $result = false;
    $html = "";
    foreach ($response_decode as $key => $version) {
        if($version["version"] > get_option("obuma_plugin_version")){
            $result = true;
            break;
        }
    }


    if($result){
        $html .= "<hr><div style='background-color:#dd8166;padding:10px;color:white;border-radius:2px;'>Hay una nueva versi&oacute;n disponible del plugin Obuma Sync !  <a target='__blank'  style='background-color:#bb4827;padding:5px;color:white;text-decoration:none;'href='https://github.com/obuma-erp/obuma-woocommerce'>Obtener la nueva versi&oacute;n</a></div><br>";
    }else{
        $html .= "<hr>";
    }

    return $html;
}