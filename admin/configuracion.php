<?php
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));



?>


<style type="text/css">
	.form-table td{
		padding-left: 0px;
		padding-right: 0px;
	}
</style>

<?php echo check_version(); ?>


<div class="panel panel-info">
<div class="panel-heading bg-white">
	<?php _e('VARIABLES DE CONFIGURACI&Oacute;N DE OBUMA', 'obuma') ?>
</div>
<div class='panel-body'>
	<p><?php _e('Establece tus variables de Configuraci&oacute;n,para conectar con OBUMA','obuma') ?></p>
	
	<div class="wrap">
		<?php if (isset($_POST["config"])) {
				
				update_option('rut_empresa', trim($_POST["rut_empresa"]));
				update_option('bodega', trim($_POST["bodega"]));
				update_option('id_bodega', trim($_POST["id_bodega"]));
				update_option('api_key', trim($_POST["api_key"]));
				update_option('url_obuma',trim($_POST["url_obuma"]));
				update_option('sucursal',trim($_POST["sucursal"]));
    			update_option('vendedor',trim($_POST["vendedor"]));
			    update_option('usuario',trim($_POST["usuario"]));
			    update_option('canal_venta',trim($_POST["canal_venta"]));
			    update_option('lista_precio',trim($_POST["lista_precio"]));
			    update_option('codigo_forma_pago',trim($_POST["codigo_forma_pago"]));

			    update_option('rebajar_stock',$_POST["rebajar_stock"]);
			    update_option('cliente_actualizar_datos',$_POST["cliente_actualizar_datos"]);
			    update_option('registrar_contabilidad',$_POST["registrar_contabilidad"]);
			    update_option('enviar_email_cliente',$_POST["enviar_email_cliente"]);
			    update_option('registrar_cobro',$_POST["registrar_cobro"]);
			    

			    if (isset($_POST["tipo_documento"])) {
			    	update_option('tipo_documento',json_encode($_POST["tipo_documento"]));

			    }else{
			    	update_option('tipo_documento',json_encode(array()));

			    }

			    update_option('nota_venta_segundo_plano',$_POST["nota_venta_segundo_plano"]);
			    
			    update_option('enviar_ventas_obuma',$_POST["enviar_ventas_obuma"]);
			    update_option('cambiar_a_completado',$_POST["cambiar_a_completado"]);

				update_option("product_info_sync", json_encode($_POST["product_info_sync"] ?? []));


			    update_option('sincronizar_precio',$_POST["sincronizar_precio"]);
				update_option('productos_categorias_sincronizar',$_POST["productos_categorias_sincronizar"]);
			    update_option('seleccionar_taxonomias',trim(trim($_POST["seleccionar_taxonomias"]),","));
				

				echo '<div id="message" class="notice notice-success"><p><strong>Todos los datos fueron guardados.</strong></p></div>';
				} 
		?>
		<form method="post" action="" style="width: 100%;">
			<?php 
				settings_fields("obuma-settings-group"); 
				do_settings_sections("obuma-settings-group");
			?>
			<table class="form-table">
				<tr class="form-field form-required">
					<th><label>RUT EMPRESA</label></th>
					<td width="40%;">
				<input type="hidden" name="config">
			<input type="text" name="rut_empresa" class="form-control" id="rut_empresa" placeholder="Ingrese el RUT de la empresa" required value="<?php echo get_option("rut_empresa"); ?>"></td>
				</tr>


				<tr class="form-field form-required">
					<th><label>API KEY</label></th>
					<td width="40%;">
			<input type="password" name="api_key"  class="form-control" id="api_key" placeholder="Ingrese API KEY" required  value="<?php echo get_option("api_key"); ?>">
<em style='color:#e74c3c;font-size: 0.8em;'>El API KEY es una clave &uacute;nica proporcionada por OBUMA, si a&uacute;n no la tiene, solic&iacute;tela a soporte@obuma.cl</em>
		</td>
				</tr>

				<tr class="form-field form-required">
					<th><label>API URL</label></th>
					<td width="40%;">
			<input type="text" name="url_obuma"  class="form-control" id="url_obuma" placeholder="Ingrese URL" required  value="<?php echo get_option("url_obuma"); ?>">
			<em style='color:#e74c3c;font-size: 0.8em;'>URL para conectarse a la API de  Obuma - ej. https://api.obuma.cl/v1.0 </em>
		</td>
				</tr>

				<tr class="form-field form-required">
					<th><label>SUCURSAL</label></th>
					<td width="40%;">
				<input type="text" name="sucursal" class="form-control" id="sucursal" placeholder="Ingrese el c&oacute;digo de la sucursal" value="<?php echo get_option("sucursal"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo de la sucursal que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

				</td>
				</tr>

					<tr class="form-field form-required">
					<th><label>BODEGA</label></th>
					<td width="40%;">
				<input type="text" name="bodega" class="form-control" id="bodega" placeholder="Ingrese el c&oacute;digo de la bodega" value="<?php echo get_option("bodega"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo de la bodega que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

				</td>
				<td width="40%;">
					<input type="text" name="id_bodega" class="form-control" id="id_bodega" placeholder="Ingrese el id bodega" value="<?php echo get_option("id_bodega"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>ID de la bodega (Este valor se obtiene en OBUMA)</em>
				</td>
				</tr>


				<tr class="form-field form-required">
					<th><label>VENDEDOR</label></th>
					<td width="40%;">
				<input type="text" name="vendedor" class="form-control" id="vendedor" placeholder="Ingrese el c&oacute;digo del vendedor" value="<?php echo get_option("vendedor"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo del vendedor que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

			</td>
				</tr>


				<tr class="form-field form-required">
					<th><label>USUARIO</label></th>
					<td width="40%;">
				<input type="text" name="usuario"  class="form-control" id="usuario" placeholder="Ingrese el c&oacute;digo del usuario" value="<?php echo get_option("usuario"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo del usuario que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

				</td>
				</tr>



				<tr class="form-field form-required">
					<th><label>CANAL DE VENTA</label></th>
					<td width="40%;">
				<input type="text" name="canal_venta"  class="form-control" id="canal_venta" placeholder="Ingrese el codigo del canal de venta" value="<?php echo get_option("canal_venta"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo del canal de venta que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

				</td>
				</tr>


				<tr class="form-field form-required">
					<th><label>LISTA PRECIO</label></th>
					<td width="40%;">
				<input type="text" name="lista_precio" class="form-control" id="lista_precio" placeholder="Ingrese el c&oacute;digo de la lista de precio" value="<?php echo get_option("lista_precio"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo de la lista de precio que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

				</td>
				</tr>

				<tr class="form-field form-required">
					<th><label>CÓDIGO FORMA DE PAGO</label></th>
					<td width="40%;">
				<input type="text" name="codigo_forma_pago" class="form-control" id="codigo_forma_pago" placeholder="Ingrese el c&oacute;digo de la forma de pago" value="<?php echo get_option("codigo_forma_pago"); ?>">
				<em style='color:#e74c3c;font-size: 0.8em;'>C&oacute;digo de la forma de pago que desea vincular a las ventas por woocommerce (Este valor se obtiene en OBUMA)</em>

				</td>
				</tr>

				<tr class="form-field form-required">
					<th><label>ENVIAR VENTAS A OBUMA</label></th>
					<td>
				<input type="radio" value="0" name="enviar_ventas_obuma" class="form-control" id="enviar_ventas_obuma" <?php if(get_option("enviar_ventas_obuma") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="enviar_ventas_obuma" class="form-control" id="enviar_ventas_obuma" value="1" <?php if(get_option("enviar_ventas_obuma") == 1){ echo "checked";} ?>> Si 
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite enviar a OBUMA las ordenes que fueron completadas</em>
				

			</td>
				</tr>
<tr class="form-field form-required">
					<th><label>ENVIAR A OBUMA AUTOMÁTICAMENTE</label></th>
					<td>
				<input type="radio" value="0" name="cambiar_a_completado" class="form-control" id="cambiar_a_completado" <?php if(get_option("cambiar_a_completado") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="cambiar_a_completado" class="form-control" id="cambiar_a_completado" value="1" <?php if(get_option("cambiar_a_completado") == 1){ echo "checked";} ?>> Si 
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite cambiar el estado del pedido a "COMPLETADO" despu&eacute;s de  realizar un pago, para ser enviado automaticamente a OBUMA</em>
				

			</td>
				</tr>

				
				<tr class="form-field form-required">
					<th><label>REBAJAR STOCK</label></th>
					<td >
				<input type="radio" value="0" name="rebajar_stock" class="form-control" id="rebajar_stock" <?php if(get_option("rebajar_stock") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="rebajar_stock" class="form-control" id="rebajar_stock" value="1" <?php if(get_option("rebajar_stock") == 1){ echo "checked";} ?>> Si 
			</td>
				</tr>

				<tr class="form-field form-required">
					<th><label>ACTUALIZAR DATOS DEL CLIENTE</label></th>
					<td>
				<input type="radio" value="0" name="cliente_actualizar_datos" class="form-control" id="cliente_actualizar_datos" <?php if(get_option("cliente_actualizar_datos") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="cliente_actualizar_datos" class="form-control" id="cliente_actualizar_datos" value="1" <?php if(get_option("cliente_actualizar_datos") == 1){ echo "checked";} ?>> Si 
			</td>
				</tr>


				<tr class="form-field form-required">
					<th><label>	REGISTRAR CONTABILIDAD</label></th>
					<td>
				<input type="radio" value="0" name="registrar_contabilidad" class="form-control" id="registrar_contabilidad" <?php if(get_option("registrar_contabilidad") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="registrar_contabilidad" class="form-control" id="registrar_contabilidad" value="1" <?php if(get_option("registrar_contabilidad") == 1){ echo "checked";} ?>> Si 
			</td>
				</tr>


				<tr class="form-field form-required">
					<th><label>ENVIAR EMAIL AL CLIENTE</label></th>
					<td>
				<input type="radio" value="0" name="enviar_email_cliente" class="form-control" id="enviar_email_cliente" <?php if(get_option("enviar_email_cliente") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="enviar_email_cliente" class="form-control" id="enviar_email_cliente" value="1" <?php if(get_option("enviar_email_cliente") == 1){ echo "checked";} ?>> Si
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite enviar email con boleta/factura al cliente</em>
			</td>
				</tr>

<tr class="form-field form-required">
					<th><label>REGISTRAR COBRO</label></th>
					<td>
				<input type="radio" value="0" name="registrar_cobro" class="form-control" id="registrar_cobro" <?php if(get_option("registrar_cobro") == 0){ echo "checked";} ?>> No 
				<input type="radio" name="registrar_cobro" class="form-control" id="registrar_cobro" value="1" <?php if(get_option("registrar_cobro") == 1){ echo "checked";} ?>> Si 
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Registra el cobro en OBUMA</em>

			</td>
				</tr>

<tr class="form-field form-required">
					<th><label>TIPO DE DOCUMENTO</label></th>
					<td>
				<input type="checkbox"  value="39" name="tipo_documento[]" multiple class="form-control" id="option-1" <?php if(in_array('39',json_decode(get_option("tipo_documento")))){ echo "checked";} ?>> Boleta
				<input type="checkbox"  id="option-2" name="tipo_documento[]" multiple class="form-control" value="33" <?php if(in_array('33',json_decode(get_option("tipo_documento")))){ echo "checked";} ?>> Factura
				<br>
				

			</td>
				</tr>

<tr class="form-field form-required">
					<th><label> EMISI&Oacute;N DE NOTA DE VENTA EN SEGUNDO PLANO </label></th>
					<td>
				<input type="radio" required value="0" name="nota_venta_segundo_plano" class="form-control" <?php if(get_option("nota_venta_segundo_plano") == 0){ echo "checked";} ?>> No
				<input type="radio" required  name="nota_venta_segundo_plano"  class="form-control" value="1" <?php if(get_option("nota_venta_segundo_plano") == 1){ echo "checked";} ?>> Si

				<input type="radio" required  name="nota_venta_segundo_plano" class="form-control" value="2" <?php if(get_option("nota_venta_segundo_plano") == 2){ echo "checked";} ?>> Solo si es Factura
				<br>
				

			</td>
</tr>
<tr class="form-field form-required">
	
<th><label>INFORMACIÓN A SINCRONIZAR</label></th>
<td>

<?php
$sync_options = json_decode(get_option("product_info_sync"), true);

if (!is_array($sync_options)) {
    $sync_options = [
        'descripcion_corta',
        'descripcion_larga',
        'ancho',
        'alto',
        'largo',
        'peso',
    ];
}
?>



<label>
    <input type="checkbox" name="product_info_sync[]" value="descripcion_corta"
        <?php if (in_array('descripcion_corta', $sync_options ?? [])) echo 'checked'; ?>>
    Descripción corta
</label><br>

<label>
    <input type="checkbox" name="product_info_sync[]" value="descripcion_larga"
        <?php if (in_array('descripcion_larga', $sync_options ?? [])) echo 'checked'; ?>>
    Descripción larga
</label><br>

<label>
    <input type="checkbox" name="product_info_sync[]" value="ancho"
        <?php if (in_array('ancho', $sync_options ?? [])) echo 'checked'; ?>>
    Ancho
</label><br>

<label>
    <input type="checkbox" name="product_info_sync[]" value="alto"
        <?php if (in_array('alto', $sync_options ?? [])) echo 'checked'; ?>>
    Alto
</label><br>

<label>
    <input type="checkbox" name="product_info_sync[]" value="largo"
        <?php if (in_array('largo', $sync_options ?? [])) echo 'checked'; ?>>
    Largo
</label><br>

<label>
    <input type="checkbox" name="product_info_sync[]" value="peso"
        <?php if (in_array('peso', $sync_options ?? [])) echo 'checked'; ?>>
    Peso
</label><br>


				<em style='color:#e74c3c;font-size: 0.8em;'>Permite seleccionar  qu&eacute; informaci&oacute;n de los productos se va a utilizar al realizar una sincronizaci&oacute;n.</em>
				

			</td>
</tr>



<tr class="form-field form-required">
					<th><label>PRECIO A UTILIZAR</label></th>
					<td>
				<input type="radio" value="0" name="sincronizar_precio" class="form-control" id="sincronizar_precio" <?php if(get_option("sincronizar_precio") == 0){ echo "checked";} ?>> Bruto
				<input type="radio" name="sincronizar_precio" class="form-control" id="sincronizar_precio" value="1" <?php if(get_option("sincronizar_precio") == 1){ echo "checked";} ?>> Neto
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite seleccionar  con qu&eacute; precio ser&aacute;n sincronizados los precios de productos  (La opción seleccionada tambi&eacute;n se utilizar&aacute; en los precios de productos enviados en los documentos emitidos)</em>
				

			</td>
				</tr>

<tr class="form-field form-required">
					<th><label>PRODUCTOS Y CATEGOR&Iacute;AS A SINCRONIZAR</label></th>
					<td>
				<input type="radio" value="0" name="productos_categorias_sincronizar" class="form-control" id="productos_categorias_sincronizar" <?php if(get_option("productos_categorias_sincronizar") == 0){ echo "checked";} ?>> Todos
				<input type="radio" name="productos_categorias_sincronizar" class="form-control" id="productos_categorias_sincronizar" value="1" <?php if(get_option("productos_categorias_sincronizar") == 1){ echo "checked";} ?>> Activos en web
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Al seleccionar "Activos en web" solo se tomarán en cuenta los productos y categorías que en OBUMA tienen habilitada la opción  'Mostrar en la web'</em>
				

			</td>
</tr>

<tr class="form-field form-required">
					<th><label>ELEGIR TAXONOM&Iacute;AS</label></th>
					<td>
					
					<input type="text" name="seleccionar_taxonomias" placeholder="Ingresar taxonomias" value="<?php echo get_option("seleccionar_taxonomias"); ?>">
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite elegir las taxonom&iacute;as para la sincronizaci&oacute;n de productos (Por defecto product_cat), si se quiere seleccionar m&aacute;s de una taxonom&iacute;a se deben separar por comas Ej : (product_cat,taxonomia2,taxonomia3)</em>
				

			</td>
				</tr>


<tr class="form-field form-required">
					<th><label>Sincronizar Comunas</label></th>
					<td>
				<button type="button" id="sincronizar_comunas" class="btn btn-success">Iniciar sincronizaci&oacute;n</button>
				<span id="update_comunas_message">
					<?php if(!empty(get_option("update_comunas_date"))){
					 echo 'Ultima sincronizaci&oacute;n : ';
					}else{
					 echo "";
					}
					?>
				</span> 


				<?php
					echo '<strong  id="update_comunas">' . get_option("update_comunas_date") . '</strong>';
				?>
					
				
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite sincronizar informaci&oacute;n de las comunas con OBUMA</em>
				

			</td>
				</tr>


				<tr class="form-field form-required">
					<th><label>Limpiar registros antiguos</label></th>
					<td>
				<button type="button" id="limpiar_registros" class="btn btn-info">Iniciar limpieza</button>
				<span id="update_limpiar_registros_message">
					<?php if(!empty(get_option("update_limpiar_registros_date"))){
					 echo 'Ultima Limpieza : ';
					}else{
					 echo "";
					}
					?>
				</span> 


				<?php
					echo '<strong  id="update_limpiar_registros">' . get_option("update_limpiar_registros_date") . '</strong>';
				?>
					
				
				<br>
				<em style='color:#e74c3c;font-size: 0.8em;'>Permite limpiar los registros antiguos generados por el plugin Obuma Sync</em>
				

			</td>
				</tr>


			</table>


			<div style="display: flex;justify-content: flex-end;">
				<?php submit_button(); ?>
			</div>
		
			
		</form>
		
	</div>

</div>
</div>
	
<script>

var requiredCheckboxes = jQuery(':checkbox[required]');

if(requiredCheckboxes.is(':checked')) {
	requiredCheckboxes.removeAttr('required');
}else{
	requiredCheckboxes.attr('required','required');
}

requiredCheckboxes.change(function(){

	if(requiredCheckboxes.is(':checked')) {
	    requiredCheckboxes.removeAttr('required');
	}else {
	    requiredCheckboxes.attr('required','required');
	}

});
</script>