<?php 
/*
Plugin Name: Obuma Sync
Plugin URI: 
Description: Plugin Para sincronizar con el API DE OBUMA
Version: 1.0.1
Author: Obuma
Author URI: https://obuma.cl
License: 
*/
    
defined('ABSPATH') or die("Bye bye");
define('RAI_RUTA',plugin_dir_path(__FILE__));

include(RAI_RUTA.'/includes/opciones.php');
include(RAI_RUTA.'/obuma_enviar_venta.php');
include(RAI_RUTA.'/admin/obuma_conector.php');
include(RAI_RUTA.'/admin/functions.php');

date_default_timezone_set('America/Santiago');

register_activation_hook(__FILE__,'activar');
register_activation_hook(__FILE__,'add_columns');
register_activation_hook(__FILE__,'create_tables');
register_activation_hook(__FILE__,'set_comunas');
register_deactivation_hook(__FILE__,'desactivar');

add_action("admin_enqueue_scripts","cargar_archivos");
add_action("wp_enqueue_scripts","load_css_js_frontend");
add_action( "wp_ajax_obuma_action", "so_wp_ajax_function" );
add_action( 'add_meta_boxes', 'add_section_order_admin' );

if (get_option("enviar_ventas_obuma") == 1) {

    add_action('woocommerce_before_order_notes', 'custom_checkout_field');
    add_action('woocommerce_order_status_changed', 'call_order_status_changed',1,3);
    add_action('template_redirect','check_thankyou_order_key');
    add_action('woocommerce_checkout_process', 'billing_vat_field_process');
    add_action('woocommerce_admin_order_data_after_billing_address','admin_view_order_billing',10,4);
    add_action('woocommerce_order_details_after_order_table','oml_custom_checkout_field_display_admin_order_meta',10,4);
    add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
    add_action( 'woocommerce_checkout_create_order', 'save_custom_billingt_fields', 20, 2 );
    if (get_option("cambiar_a_completado") == 1) {
        add_action('woocommerce_payment_complete', 'change_to_completed');
    }

}



function activar(){
    
    add_option('rut_empresa',"",'','yes');
    add_option('bodega',"",'','yes');
    add_option('id_bodega',"",'','yes');
    add_option('api_key',"",'','yes');
    add_option('url_obuma',"",'','yes');

    add_option('sucursal',"",'','yes');
    add_option('vendedor',"",'','yes');
    add_option('usuario',"",'','yes');
    add_option('canal_venta',"",'','yes');
    add_option('lista_precio',"",'','yes');
    add_option('codigo_forma_pago',"",'','yes');

    add_option('rebajar_stock',0,'','yes');
    add_option('cliente_actualizar_datos',0,'','yes');
    add_option('registrar_contabilidad',0,'','yes');
    add_option('enviar_email_cliente',0,'','yes');
    add_option('registrar_cobro',0,'','yes');

    add_option('tipo_documento',"[]",'','yes');
    add_option('nota_venta_segundo_plano',0,'','yes');
    add_option('enviar_ventas_obuma',0,'','yes');
    add_option('cambiar_a_completado',0,'','yes');
    add_option('product_info_sync','["descripcion_corta","descripcion_larga","ancho","alto","largo","peso"]',"","yes");
    add_option('sincronizar_precio',0,'','yes');
    add_option('productos_categorias_sincronizar',0,'','yes');
    add_option('seleccionar_taxonomias',"","",'yes');

    add_option('update_comunas_date',"","",'yes');
    add_option('update_limpiar_registros_date',"","",'yes');
    
    add_option('obuma_plugin_version',"1.0.1","",'yes');
}


function desactivar(){
    
    //delete_option('rut_empresa');
    //delete_option('bodega');
    //delete_option('api_key');
}


function load_css_js_frontend(){

    wp_register_script("obuma_js",plugins_url("/public/js/obuma.js",__FILE__),array("jquery"));
    wp_enqueue_script("jquery");
    wp_enqueue_script("obuma_js");
}


function cargar_archivos(){

    if(isset($_GET["page"])){
        if($_GET["page"] != "obuma_sincronizar" && $_GET["page"] != "obuma_configuracion"  && $_GET["page"] != "otros" ){
            return;
        }
        wp_register_style('obuma_bootstrap_css', plugins_url("/admin/css/bootstrap.min.css",__FILE__));
        wp_enqueue_style('obuma_bootstrap_css');
    }

    wp_register_script("obuma_js",plugins_url("/admin/js/obuma.js",__FILE__),array("jquery"));

    wp_register_style('obuma_css', plugins_url("/admin/css/obuma.css",__FILE__));
    
    

    wp_enqueue_style('obuma_css');

    wp_enqueue_script("jquery");

    wp_enqueue_script("obuma_js");
    
    wp_localize_script( 
        'obuma_js', 
        'ajax_object', 
        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) 
    );

}


function so_wp_ajax_function(){
    global $wpdb;
    global $pagina;
    require_once "admin/".$_POST["url"].".php";
    die();
}

function load_configuracion(){

    require_once "admin/configuracion.php";

}

function load_sincronizar(){

    require_once "admin/sincronizar_obuma.php";

}

function load_vincular_categorias(){

    global $wpdb;

    require_once "admin/vincular_categorias.php";

}

function load_log_sincronizacion(){

    require_once "admin/log_sincronizacion.php";

}

function load_log_ordenes(){

    require_once "admin/log_ordenes.php";

}

function load_log_webhook(){

    require_once "admin/log_webhook.php";

}


function load_otros(){

    require_once "admin/otros.php";

}


function custom_checkout_field($checkout){


    echo '<div id="custom_checkout_field">';


    /*
    $json = ObumaConector::get(set_url()."pg-comunas.list.json",get_option("api_key"));


    $comuna = [];
    foreach ($json->data as $key => $value) {
         $comuna[$value->pg_comuna_id] = $value->pg_comuna_nombre;
    }


    woocommerce_form_field('obuma_comuna', array(

    'type' => 'select',
    'class' => array('obuma_comuna my-field-class form-row-wide') ,
    'label' => __('COMUNA') ,
    'placeholder' => __('Ingresar COMUNA.') ,
    'required' => true,
     'options'     => $comuna
    ),$checkout->get_value('obuma_comuna'));

    woocommerce_form_field('obuma_rut', array(

    'type' => 'text',
    'class' => array('obuma_rut my-field-class form-row-wide') ,
    'label' => __('R.U.T.') ,
    'placeholder' => __('Ingresar R.U.T.') ,
    'required' => true
    ),$checkout->get_value('obuma_rut'));

    woocommerce_form_field('obuma_giro_comercial', array(

    'type' => 'text',
    'class' => array(
    'my-field-class form-row-wide'
    ) ,
    'label' => __('Giro comercial') ,
    'placeholder' => __('Ingresar giro comercial') ,
    'required' => true
    ),$checkout->get_value('obuma_giro_comercial'));




    $tipo_documento = get_option("tipo_documento");
    $tipo_documento = json_decode($tipo_documento);

    $data_tipo_documento = array();
    $data_tipo_documento[""] = 'Selecciona tipo de documento';

    if (in_array("39",$tipo_documento)) {
        $data_tipo_documento["39"] = "Boleta";
    }

    if (in_array("33",$tipo_documento)) {
        $data_tipo_documento["33"] = "Factura";
    }
    woocommerce_form_field('obuma_tipo_documento', array(

    'type' => 'select',
    'class' => array(
    'obuma_tipo_documento my-field-class form-row-wide'
    ) ,
    'label' => __('Tipo de documento') ,
    'placeholder' => __('Ingresar tipo de documento') ,
    'required' => true,
    'options'     => $data_tipo_documento
    ));
*/
    echo '</div>';



}


function add_section_order_admin(){

    add_meta_box(
        'custom-order-meta-box',
        __( 'Par&aacute;metros de OBUMA', 'webkul' ),
        'add_section_order_admin_callback',
        'shop_order',
        'advanced',
        'core'
    );


}


function add_section_order_admin_callback($post){


    $order_id = $post->ID;

    
    require_once "admin/admin_list_bodegas.php";



}



function save_custom_billingt_fields( $order, $data ) {
    if ( isset( $_POST['obuma_rut'] ) && ! empty( $_POST['obuma_rut'] ) ) {

        $order->update_meta_data('obuma_rut',$_POST['obuma_rut']);
        //update_post_meta( $order->get_id(), 'obuma_rut', $_POST['billing_obuma_rut']);
    }

}

function call_order_status_changed($order_id,$old,$new){
    global $wpdb;

    
    if (strtolower($new) == "completed") {
        $data = array();
        // enviar_orden_venta($order->id);
        $id = $order_id;

        //Obtener la matadata asociada a la orden usando la funcion get_post_meta 
        /*

        $meta_billing_giro_comercial = get_post_meta($id,'_billing_giro_comercial',true);
        $meta_billing_tipo_documento = get_post_meta($id, '_billing_tipo_documento', true);
        $meta_billing_first_name = get_post_meta($id, '_billing_first_name', true);
        $meta_billing_last_name = get_post_meta($id, '_billing_last_name', true);
        $meta_billing_email = get_post_meta($id, '_billing_email', true);
        $meta_billing_phone = get_post_meta($id, '_billing_phone', true);
        $meta_billing_company = get_post_meta($id, '_billing_company', true);
        $meta_billing_address_1 = get_post_meta($id, '_billing_address_1', true);
        $meta_billing_address_2 = get_post_meta($id, '_billing_address_2', true);
        $meta_billing_state = get_post_meta($id, '_billing_state', true);
        
        $meta_obuma_rut = get_post_meta($id, 'obuma_rut', true);
        $meta_obuma_bodega = get_post_meta($id, 'bodega_obuma', true);

        */


        $get_order = wc_get_order($id);

        //obtenemos la metadata asociada a la orden
        $meta_billing_giro_comercial = $get_order->get_meta('_billing_giro_comercial');
        $meta_billing_tipo_documento = $get_order->get_meta('_billing_tipo_documento');
        $meta_billing_first_name = $get_order->get_meta('_billing_first_name');
        $meta_billing_last_name = $get_order->get_meta('_billing_last_name');
        $meta_billing_email = $get_order->get_meta('_billing_email');
        $meta_billing_phone = $get_order->get_meta('_billing_phone');
        $meta_billing_company = $get_order->get_meta('_billing_company');
        $meta_billing_address_1 = $get_order->get_meta('_billing_address_1');
        $meta_billing_address_2 = $get_order->get_meta('_billing_address_2');
        $meta_billing_state = $get_order->get_meta('_billing_state');
        $meta_billing_city = $get_order->get_meta('_billing_city');

        //Metadata de Obuma vinculada a una orden
        
        $meta_obuma_rut = $get_order->get_meta('obuma_rut');
        $meta_obuma_bodega = $get_order->get_meta('bodega_obuma');

        $order_obuma_existe = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."obuma_order WHERE order_woocommerce_id='".$id."'");


        if (count($order_obuma_existe) == 0) {
            $order = new WC_Order($id);
            $productos_orden = $order->get_items();
            $data["orden_id"] = $id;

            if (empty($meta_billing_giro_comercial)) {
                $data["giro_comercial"] = "-";
            }else{
                $data["giro_comercial"] = $meta_billing_giro_comercial;
            }
            
            //update_post_meta( $id, 'obuma_rut', $meta_obuma_rut);

            $data["rut"] = $meta_obuma_rut;



            $obuma_tipo_documento = $meta_billing_tipo_documento;


            $obuma_tipo_documento_value = 39;

            if($obuma_tipo_documento == "_33"){

                $obuma_tipo_documento_value = 33;
            }

                if (get_option("nota_venta_segundo_plano") == 0) {

                    $data["tipo_documento"] = $obuma_tipo_documento_value;

                }elseif(get_option("nota_venta_segundo_plano") == 1){

                    $data["tipo_documento"] = 4;

                }else{

                    if($obuma_tipo_documento_value == 33){

                          $data["tipo_documento"] = 4;

                    }else{

                         $data["tipo_documento"] = $obuma_tipo_documento_value;
                         
                    }
                }
            




                //$data["tipo_documento"] = 4;
                $data["email"] = $meta_billing_email;
                $data["telefono"] = $meta_billing_phone;

                if($obuma_tipo_documento_value == "39"){

                    $data["razon_social"] = $meta_billing_first_name . " " . $meta_billing_last_name;
                    
                }else{
                    if(empty(trim($meta_billing_company))){
                        $data["razon_social"] = $meta_billing_first_name . " " . $meta_billing_last_name;

                    }else{
                        $data["razon_social"] = $meta_billing_company;
                    }
                }
                
            
                $data["contacto"] = $meta_billing_first_name . " " . $meta_billing_last_name;

                $data["direccion"] = $meta_billing_address_1 . " - " . $meta_billing_address_2;

                $data_comuna = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."obuma_comunas WHERE pg_comuna_codigo_chilexpress='".$meta_billing_state."'");
                

                if (isset($data_comuna[0]->pg_comuna_id)) {
                    $data["comuna"] =  $data_comuna[0]->pg_comuna_id;
                    $data["region"] = $data_comuna[0]->rel_pg_region_id;
                }else{
                    $data["comuna"] = $meta_billing_city;
                    $data["region"] = $meta_billing_state;
                }


                $customer_note = $order->get_customer_note();
                // decode entity to regular HTML
                $customer_note = html_entity_decode($customer_note);


                //Obtenemos el valor de la bodega asociada a un pedido mediante un campo personalizado

                $bodega_pedido_campo_personalizado = null;

                if(get_option("cambiar_a_completado") == 0){

                    $bodega_pedido_campo_personalizado = $meta_obuma_bodega;

                }
                

                $data["observacion"] =  $customer_note;
                $data["sucursal"] = get_option("sucursal");
                $data["bodega"] =  (isset($bodega_pedido_campo_personalizado) && !empty($bodega_pedido_campo_personalizado)) ? $bodega_pedido_campo_personalizado : get_option("bodega");
                $data["usuario"] = get_option("usuario");
                $data["canal_venta"] = get_option("canal_venta");
                $data["vendedor"] = get_option("vendedor");
                $data["lista_precio"] = get_option("lista_precio");
                $data["rebajar_stock"] = get_option("rebajar_stock");
                $data["registrar_contabilidad"] = get_option("registrar_contabilidad");
                $data["enviar_email_cliente"] = get_option("enviar_email_cliente");
                $data["registrar_cobro"] = get_option("registrar_cobro");
                $data["cliente_actualizar_datos"] = get_option("cliente_actualizar_datos");

                $payment_method = $order->get_payment_method_title();

                $forma_pago_ = explode('#', $payment_method);
                
                if (isset($forma_pago_[1])) {

                    $venta_forma_pago = $forma_pago_[1];
                    
                }else{

                    $venta_forma_pago = get_option("codigo_forma_pago");

                }
            

                $data["forma_pago"] = $venta_forma_pago;
                $data["total_neto"] = ($order->get_total() / 1.19);

                if(get_option("registrar_cobro") == 1){
                        $data["total_pagado"] = $order->get_total();
                        $data["total_por_pagar"] = 0;
                }else{
                        $data["total_pagado"] = 0;
                        $data["total_por_pagar"] = $order->get_total();
                }

                if ($data["tipo_documento"] != "39") {
                    $data["subtotal"] = ($order->get_total() / 1.19);
                }else{
                    $data["subtotal"] = $order->get_total();
                }
                   
                $data["total_envio"] = $order->get_total_shipping();
                $data["total"] = $order->get_total();
                //Total incluido el envio $order->get_total();
                $indice = 0;
                foreach ($productos_orden as $item) {

                    $producto = wc_get_product($item->get_product_id());



                    /*

                    
                    Por defecto se trabaja con valores brutos , es decir  el iva es descontado del producto en todos los documentos ,excepto en las boletas.

                    Si el plugin está configurado para trabajar con valor neto , entonces el iva es descontado del producto solo cuando el documento es una boleta.


                    */

                    $precio = 0;

                    $subtotal = 0;
                    
                    $item_total = $order->get_item_total($item);

                    $line_total = $order->get_line_total($item);

                    if (get_option("sincronizar_precio") == 1) {

                        // trabaja con precios netos en la web
                        // - Se agrega el IVA a las boletas (cod 39), al precio unitario y subtotal
                
                        $precio = $data["tipo_documento"] == "39" ? ($item_total * 1.19) : $item_total;
                        $subtotal = $data["tipo_documento"] == "39" ? ($line_total * 1.19) : $line_total;
                        
                
                    }else{
                
                        // trabaja con precios brutos en la web
                        // - se le quita el iva a las boletas (cod 39), al precio unitario y subtotal
                
                        $precio = $data["tipo_documento"] == "39" ? $item_total : ($item_total / 1.19);
                        $subtotal = $data["tipo_documento"] == "39" ? $line_total : ($line_total / 1.19);
                
                    }


                    $data["orden_detalle"][$indice]["precio"] = $precio;
                    $data["orden_detalle"][$indice]["subtotal"] = $subtotal;
                    $data["orden_detalle"][$indice]["codigo_comercial"] = $producto->get_sku();
                    $data["orden_detalle"][$indice]["nombre"] = $item['name'];
                    $data["orden_detalle"][$indice]["cantidad"] = $item['qty'];
                        
                    $indice++;

                }

                $data["orden_detalle"][$indice]["codigo_comercial"] = 'envio';
                $data["orden_detalle"][$indice]["nombre"] = empty(trim($order->get_shipping_method())) ? 'envio' : $order->get_shipping_method();
                $data["orden_detalle"][$indice]["cantidad"] = 1;

                if ($data["tipo_documento"] != "39") {
                    $data["orden_detalle"][$indice]["precio"] = ($order->get_total_shipping()  / 1.19);
                    $data["orden_detalle"][$indice]["subtotal"] = ($order->get_total_shipping() / 1.19);
                }else{
                    $data["orden_detalle"][$indice]["precio"] = $order->get_total_shipping();
                    $data["orden_detalle"][$indice]["subtotal"] = $order->get_total_shipping();
                }
                

            
        
                $response = enviar_orden_venta($data);


                $datos_log  = array('data' => $response["peticion"], "response" => $response["respuesta"],"estado" => strtolower($new));
                insert_order_obuma_log($datos_log,$order_id);

                if(isset($response["respuesta"]->result->result_dte[0]->dte_id)){
                 

                    update_post_meta( $data["orden_id"], 'obuma_url_pdf', $response["respuesta"]->result->result_dte[0]->dte_pdf );

                    insert_order_obuma(
                            array('order_woocommerce_id' => $order_id,
                                  'dte_id' => $response["respuesta"]->result->result_dte[0]->dte_id,
                                  'dte_tipo' => $response["respuesta"]->result->result_dte[0]->dte_tipo,
                                  'dte_folio' => $response["respuesta"]->result->result_dte[0]->dte_folio,
                                  'dte_result' => $response["respuesta"]->result->result_dte[0]->dte_result,
                                  'dte_xml' => $response["respuesta"]->result->result_dte[0]->dte_xml,
                                  'dte_pdf' => $response["respuesta"]->result->result_dte[0]->dte_pdf
                                    )
                    );


                }else{


                    if(isset($response["respuesta"]->errors)){

                        foreach ($response["respuesta"]->errors as $key => $error) {

                            
                            $order = wc_get_order($data["orden_id"]);

                            $order->update_status('on-hold');

                            $note = 'La orden no fue registrada en OBUMA : Codigo de error '. $error->error . " -> " . $error->message;

                            $order->add_order_note( $note );

                            $order->save();

                        }
                    }

                        
                }
            



            

            }else{
                $datos_log  = array('data' => [], "response" => [],"estado" => strtolower($new));
                insert_order_obuma_log($datos_log,$order_id);
            }
        
       

     }else{
        
        $datos_log  = array('data' => [], "response" => [],"estado" => strtolower($new));
        insert_order_obuma_log($datos_log,$order_id);
     }


}


function billing_vat_field_process() {
    // Check if set, if its not set add an error.
    if ($_POST['billing_tipo_documento'] == "_33" ){
        if (empty($_POST['billing_company'])) {
            //wc_enqueue_js( "alert('asasasas')" );
            wc_add_notice( __( 'El nombre de la empresa es un campo obligatorio' ), 'error' );

        }
     
   }
}


function custom_override_checkout_fields($fields){


    $fields['billing']['obuma_rut'] = array(
    'label'     => __('RUT', 'woocommerce'),
    'placeholder'   => _x('RUT', 'placeholder', 'woocommerce'),
    'required'  => true,
    'class'     => array('form-row-wide'),
    'clear'     => true
     );
    



    $tipo_documento = get_option("tipo_documento");                           
    $tipo_documento = json_decode($tipo_documento);

    $data_tipo_documento = array();
    $data_tipo_documento[""] = 'Selecciona tipo de documento';

    if (in_array("39",$tipo_documento)) {
        $data_tipo_documento["_39"] = "Boleta";
    }

    if (in_array("33",$tipo_documento)) {
        $data_tipo_documento["_33"] = "Factura";
    }


    $fields['billing']['billing_tipo_documento'] = array(
         'label'     => __('Tipo de documento', 'woocommerce'),
         'type' => 'select',
    'required'  => true,
    'class'     => array('form-row-wide '),
    'clear'     => true,
    'options'     => $data_tipo_documento

     );


    $fields['billing']['billing_giro_comercial'] = array(
        'label'     => __('Giro comercial', 'woocommerce'),
    'placeholder'   => _x('Giro comercial', 'placeholder', 'woocommerce'),
    'required'  => false,
    'class'     => array('form-row-wide'),
    'clear'     => true
     );


    return $fields;

}



function admin_view_order_billing($order){


    //Obtener la matadata asociada a la orden usando la funcion get_post_meta 
    
    /*
        $meta_billing_rut = get_post_meta($order->get_id(),'_billing_rut',true);
        $meta_billing_giro_comercial = get_post_meta($order->get_id(),'_billing_giro_comercial',true);
        $meta_billing_tipo_documento = get_post_meta($order->get_id(), '_billing_tipo_documento', true);        
        $meta_obuma_rut = get_post_meta($order->get_id(), 'obuma_rut', true);
        $meta_billing_url_pdf = get_post_meta($order->get_id(), 'obuma_url_pdf', true);

    */

    
    $get_order = wc_get_order($order->get_id());

    $meta_billing_rut = $get_order->get_meta('_billing_rut');
    $meta_obuma_rut = $get_order->get_meta('obuma_rut');
    $meta_billing_giro_comercial = $get_order->get_meta('_billing_giro_comercial');
    $meta_billing_tipo_documento = $get_order->get_meta('_billing_tipo_documento');
    $meta_billing_url_pdf = $get_order->get_meta('obuma_url_pdf');


    $_billing_rut_obuma = $meta_billing_rut;

    if(!isset($_billing_rut_obuma) || empty($_billing_rut_obuma)){
         echo '<p><strong style="">'.__('R.U.T.').':<br></strong> ' . $meta_obuma_rut . '</p>';
    }else{
         echo '<p><strong style="">'.__('R.U.T.').':<br></strong> ' . $meta_billing_rut . '</p>';
    }


    if(!empty($meta_billing_giro_comercial)){
        echo '<p><strong style="display:flex;">'.__('Giro comercial').':</strong> ' . $meta_billing_giro_comercial . '</p>';
    }
    

    if(!empty($meta_billing_tipo_documento)){

        echo '<p><strong style="">'.__('Tipo de documento').':<br></strong> ' ;

        if($meta_billing_tipo_documento == '_39'){
            echo "Boleta";
        }else{
            echo "Factura";
        }
        echo  '</p>';

    }

    if(!empty($meta_billing_url_pdf)){
        echo "<p><a target='__blank' href='".$meta_billing_url_pdf."'>Ver DTE Generada en OBUMA</a></p>";
    }
    
     
}

function oml_custom_checkout_field_display_admin_order_meta($order){
    
    global $wpdb;

    //Obtener la matadata asociada a la orden usando la funcion get_post_meta 
    
    /*
        $meta_billing_giro_comercial = get_post_meta($order->get_id(),'_billing_giro_comercial',true);
        $meta_billing_tipo_documento = get_post_meta($order->get_id(), '_billing_tipo_documento', true);        
        $meta_obuma_rut = get_post_meta($order->get_id(), 'obuma_rut', true);

    */

    $get_order = wc_get_order($order->get_id());

    $meta_obuma_rut = $get_order->get_meta('obuma_rut');
    $meta_billing_giro_comercial = $get_order->get_meta('_billing_giro_comercial');
    $meta_billing_tipo_documento = $get_order->get_meta('_billing_tipo_documento');

    echo '<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">';
    echo "<tbody>";
    echo '<tr class="woocommerce-table__line-item order_item">';
    echo '<th scope="row">'.__('R.U.T.').'</th>';
    echo '<td><span class="woocommerce-Price-amount amount">'. $meta_obuma_rut . '</span></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">'.__('Giro comercial').'</th>';
    echo '<td><span class="woocommerce-Price-amount amount">'. $meta_billing_giro_comercial . '</span></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row">'.__('Tipo de documento').'</th>';
    echo '<td><span class="woocommerce-Price-amount amount">';
    if($meta_billing_tipo_documento == '_39'){
        echo "Boleta";
    }else{
        echo "Factura";
    }
    echo '</span></td>';
    echo '</tr>';
    echo "</tbody>";
    echo "</table>";
    
    if ($order->get_status() == "completed") {
        $order_obuma = $wpdb->get_results("SELECT  dte_pdf from ".$wpdb->prefix."obuma_order WHERE order_woocommerce_id='".$order->get_id()."'");
        echo "<a href='".$order_obuma[0]->dte_pdf."' class='button wc-backward'>";
        if($meta_billing_tipo_documento == '_39'){
            echo "DESCARGAR BOLETA";
        }else{
            echo "DESCARGAR FACTURA";
        }
         echo "</a><br><br>";
    }

}


function check_thankyou_order_key(){
    if( is_wc_endpoint_url('order-received') && isset($_GET['key']) ) {
        global $wp;

        $order_id  = absint( $wp->query_vars['order-received'] );
        $order     = wc_get_order( $order_id );

        if( $order->get_order_key() != wc_clean($_GET['key']) ){
            // Display a custom error notice
            wc_add_notice( __('Oups! The order key is invalid…', 'woocommerce'), 'error');

        }
    }
}




function change_to_completed($order_id) {

        $order = wc_get_order($order_id);
        $order->update_status('completed');

    
}


?>