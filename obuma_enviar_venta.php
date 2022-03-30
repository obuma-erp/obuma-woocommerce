<?php

//Funcion para crear las tablas que utiliza el plugin Obuma
function create_tables(){
    global $wpdb;

    $table_order_obuma_log = $wpdb->prefix . 'obuma_log_order';

    $sql_order_obuma_log = "CREATE TABLE IF NOT EXISTS $table_order_obuma_log (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `order_woocommerce_id` int(11)  NOT NULL,
      `fecha` date  NOT NULL,
      `hora` time  NOT NULL,
      `peticion` text  NOT NULL,
      `respuesta` text  NOT NULL,
      `estado` text  NOT NULL,
       PRIMARY KEY (`id`))";

    $wpdb->query($sql_order_obuma_log);

    $table_order_obuma = $wpdb->prefix . 'obuma_order';
    $sql_order_obuma = "CREATE TABLE IF NOT EXISTS $table_order_obuma (
      `id` int(11) NOT NULL AUTO_INCREMENT,
       `order_woocommerce_id` int(11)  NOT NULL,
      `dte_id` int(11)  NOT NULL,
       `dte_tipo` int(11)  NOT NULL,
        `dte_folio` int(11)  NOT NULL,
      `dte_result` text  NOT NULL,
      `dte_xml` text  NOT NULL,
      `dte_pdf` text  NOT NULL,
      `fecha` date  NOT NULL,
      `hora` time  NOT NULL,
       PRIMARY KEY (`id`))";
    $wpdb->query($sql_order_obuma);


    $table_categorias_obuma = $wpdb->prefix . 'obuma_vincular_categorias';
    $sql_categorias_obuma = "CREATE TABLE IF NOT EXISTS $table_categorias_obuma (
      `id` int(11) NOT NULL AUTO_INCREMENT,
       `categoria_woocommerce_id` int(11)  NOT NULL,
      `categoria_woocommerce_nombre` text  NOT NULL,
       `obuma_id_category` int(11)  NOT NULL,
       PRIMARY KEY (`id`))";
    $wpdb->query($sql_categorias_obuma);


    $table_comunas_obuma = $wpdb->prefix . 'obuma_comunas';
    $sql_comunas_obuma = "CREATE TABLE IF NOT EXISTS $table_comunas_obuma (
        `pg_comuna_id` int(11) NOT NULL,
        `pg_comuna_nombre` text  NOT NULL,
        `pg_comuna_distancia_santiago` text  NOT NULL,
        `rel_pg_region_id` int(11)  NOT NULL,
        `pg_comuna_codigo_dt` text  NOT NULL,
        `pg_comuna_codigo_chilexpress` text  NOT NULL,
        `pg_comuna_codigo_ine` text  NOT NULL,
         
       PRIMARY KEY (`pg_comuna_id`))";
    $wpdb->query($sql_comunas_obuma);



    $table_obuma_log_webhook = $wpdb->prefix . 'obuma_log_webhook';
    $sql_obuma_log_webhook = "CREATE TABLE IF NOT EXISTS $table_obuma_log_webhook (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `fecha` date  NOT NULL,
        `hora` time  NOT NULL,
        `tipo` text  NOT NULL,
        `peticion` text  NOT NULL,
        `resultado` text  NOT NULL,
         
       PRIMARY KEY (`id`))";
    $wpdb->query($sql_obuma_log_webhook);

    $table_obuma_log_synchronization = $wpdb->prefix . 'obuma_log_synchronization';
    $sql_obuma_log_synchronization = "CREATE TABLE IF NOT EXISTS $table_obuma_log_synchronization (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `fecha` date  NOT NULL,
        `hora` time  NOT NULL,
        `tipo` text  NOT NULL,
        `opcion` text  NOT NULL,
        `resultado` text  NOT NULL,
         
       PRIMARY KEY (`id`))";
    $wpdb->query($sql_obuma_log_synchronization);

}





//Funcion para agregar columnas en la tablas posts,terms,users, en caso no existan.
function add_columns(){
    global $wpdb;
    $myPosts = $wpdb->get_row("SHOW COLUMNS FROM ".$wpdb->prefix."posts WHERE Field = 'obuma_id_product'",ARRAY_A);
    $myTerms = $wpdb->get_row("SHOW COLUMNS FROM ".$wpdb->prefix."terms WHERE Field = 'obuma_id_category'",ARRAY_A);
    $myCustomer = $wpdb->get_row("SHOW COLUMNS FROM ".$wpdb->prefix."users WHERE Field = 'obuma_id_customer'",ARRAY_A);

    
    if(!isset($myPosts["Field"])){
        $wpdb->query("ALTER TABLE ".$wpdb->prefix."posts ADD obuma_id_product INT(11)");
    }
    if(!isset($myTerms["Field"])){
        $wpdb->query("ALTER TABLE ".$wpdb->prefix."terms ADD obuma_id_category INT(11)");
    }
    if(!isset($myCustomer["Field"])){
        $wpdb->query("ALTER TABLE ".$wpdb->prefix."users ADD obuma_id_customer INT(11)");
    }

}


//Funcion para enviar la venta a Obuma (retorna los datos enviados y la respuesta)
function enviar_orden_venta($data){

  $data_enviar = array(
    'venta_tipo_dcto'             => $data["tipo_documento"],                   //Obtenido del checkout de la tienda
    'venta_nro_dcto'              => "",                                        // irrelevante, obuma maneja el folio
    'venta_fecha'                 => date("Y-m-d"),                             // Fecha actual
    'venta_sucursal'              => $data["sucursal"],                         // Definido en la configuracion del plugin
    'venta_bodega'                => $data["bodega"],                           // Definido en la configuracion del plugin
    'venta_lista_precio'          => $data["lista_precio"],                     // Definido en la configuracion del plugin
    'venta_usuario'               => $data["usuario"],                          // Definido en la configuracion del plugin
    'venta_vendedor'              => $data["vendedor"],                         // Definido en la configuracion del plugin
    'venta_canal'                 => $data["canal_venta"],                      // Definido en la configuracion del plugin
    'venta_contacto'              => 0,
    'venta_subtotal'              => number_format($data["subtotal"],0,'.',''),
    'venta_rebajar_stock'         => $data["rebajar_stock"],                    // Definido en la configuracion del plugin
    'venta_registrar_contabilidad'=> $data["registrar_contabilidad"],           // Definido en la configuracion del plugin
    'venta_enviar_email_cliente'  => $data["enviar_email_cliente"],             // Definido en la configuracion del plugin
    'venta_registrar_cobro'       => $data["registrar_cobro"],                  // Definido en la configuracion del plugin
    'venta_forma_pago'            => $data["forma_pago"],                       // Definido en la configuracion del plugin
    'venta_exento'                => 0,
    'venta_neto'                  => number_format($data["total_neto"],0,'.',''), 
    'venta_iva'                   => number_format(($data["total"] - $data["total_neto"]),0,'.',''),
    'venta_total'                 => number_format($data["total"],0,'.',''),
    'venta_total_pagado'          => number_format($data["total_pagado"],0,'.',''),
    'venta_total_por_pagar'       => number_format($data["total_por_pagar"],0,'.',''),
    'venta_observacion'           => $data["observacion"],                      //Obtenido del checkout de la tienda
    'cliente_actualizar_datos'    => $data["cliente_actualizar_datos"],         // Definido en la configuracion del plugin
    'cliente_rut'                 => $data["rut"],                              //Obtenido del checkout de la tienda
    'cliente_razon_social'        => $data["razon_social"],
    'cliente_contacto'            => $data["contacto"],                    //Obtenido del checkout de la tienda
    'cliente_direccion'           => $data["direccion"],                        //Obtenido del checkout de la tienda
    'cliente_comuna'              => $data["comuna"],                           //Obtenido del checkout de la tienda
    'cliente_region'              => $data["region"],                           //Obtenido del checkout de la tienda
    'cliente_giro'                => $data["giro_comercial"],                   //Obtenido del checkout de la tienda
    'cliente_email'               => $data["email"],                            //Obtenido del checkout de la tienda
    'cliente_telefono'            => $data["telefono"]                          //Obtenido del checkout de la tienda
   
  );



  foreach ($data["orden_detalle"] as $key => $producto) {
      
    $data_enviar["venta_detalle"][$key]  = array(
                    'codigo_comercial'      => $producto["codigo_comercial"],
                    'producto_nombre'       => $producto["nombre"],
                    'mostrar_descripcion'   => '',
                    'producto_descripcion'  => '',
                    'cantidad'              => $producto["cantidad"],  
                    'producto_precio'       =>  number_format($producto["precio"],2,'.',''),
                    'descuento'             => 0,
                    'descuento_monto'       => 0,
                    'subtotal'              => number_format($producto["subtotal"],0,'.',''),
                    'producto_exento'       => "",
                    'unidad_medida'         => ""
    );
   }


    $data_enviar["venta_referencias"][0] = array(
                  'tipo_dcto_ref' => '802', 
                  'folio_dcto_ref' => $data["orden_id"],
                  'fecha_dcto_ref' => date("Y-m-d"),
                  'razon_ref' => 'Woocommerce');
           

  $data_enviar =  array('docs' => [$data_enviar]); 
  
  sleep(3);

  $json = ObumaConector::post(set_url()."ventasIntegracionExternas.create.json",$data_enviar,get_option("api_key"));


  $return = array('peticion' => $data_enviar , "respuesta" => $json);

  return $return;
}


//Funcion para insertar un registro de LOG cuando el estado el estado de la orden es diferente a completed
function insert_order_obuma_log($data,$order_woocommerce_id){
  global $wpdb;

  $sql = "INSERT INTO {$wpdb->prefix}obuma_log_order SET 
                                                        order_woocommerce_id='".$order_woocommerce_id."',
                                                        fecha='".date('Y-m-d')."',
                                                        hora='".date("H:i:s")."',
                                                        peticion='".json_encode($data["data"])."',
                                                        respuesta='".print_r($data["response"],true)."',
                                                        estado='".$data["estado"]."'";


    $wpdb->query($sql);

}


//Funcion para insertar un registro  cuando la orden ha sido completada (completed status)
function insert_order_obuma($data){
  global $wpdb;

  $sql = "INSERT INTO {$wpdb->prefix}obuma_order SET 
                                                        order_woocommerce_id='".$data["order_woocommerce_id"]."',
                                                        dte_id='".$data['dte_id']."',
                                                        dte_tipo='".$data['dte_tipo']."',
                                                        dte_folio='".$data['dte_folio']."',
                                                        dte_result='".$data['dte_result']."',
                                                        dte_xml='".$data["dte_xml"]."',
                                                        dte_pdf='".$data["dte_pdf"]."',
                                                        fecha='".date('Y-m-d')."',
                                                        hora='".date("H:i:s")."'";


    $wpdb->query($sql);

}


?>