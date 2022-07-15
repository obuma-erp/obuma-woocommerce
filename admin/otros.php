<br>
<div class="panel panel-info">

	<div class="panel-heading bg-white">

		<?php _e('ACERCA DEL PLUGIN', 'obuma') ?>

	</div>

	<div class='panel-body'>
		
		<p><strong><?php _e('Informacion importante sobre el funcionamiento del plugin','obuma'); ?></strong></p>
		
		<div class="wrap">

			<ul>
				<li>Versi&oacute;n  de WooCommerce instalada : <code><?php echo WC_VERSION; ?></code></li>
				<li>Versi&oacute;n  de PHP instalada : <code><?php echo phpversion(); ?></code></li>
				<li>Versi&oacute;n de Obuma Sync : <code>1.0.1</code></li>
				<li>Requisitos m&iacute;nimos de Obuma Sync  : <code>Woocommerce 5.0</code>  &oacute; superior , <code>PHP 5.6</code> &oacute; superior</li>

			</ul>


		</div>

	</div>

</div>



<div class="panel panel-info">

	<div class="panel-heading bg-white">

		<?php _e('ACERCA DE LOS WEBHOOKS', 'obuma') ?>

	</div>

	<div class='panel-body'>
		
		<p><strong><?php _e('Estas son los webhooks del plugin para vincular con OBUMA','obuma') ?></strong></p>
		
		<div class="wrap">

			<code>Crear producto</code>

			<pre><input type="text" id="webhook_obuma_crear_producto"  readonly="readonly" style="width: 90%;" name="" value="<?php echo plugin_dir_url(dirname(__FILE__)) . "obuma_webhook_receiver_productoCreated.php"; ?>"> <button class="btn btn-primary" onclick="copiarAlPortapapeles(this,'webhook_obuma_crear_producto')">Copiar</button></pre>

			<code>Actualizar producto</code>

			<pre><input type="text" id="webhook_obuma_actualizar_producto"  readonly="readonly" name="" style="width: 90%;" value="<?php echo plugin_dir_url(dirname(__FILE__)) . "obuma_webhook_receiver_productoUpdated.php"; ?>"> <button class="btn btn-primary" onclick="copiarAlPortapapeles(this,'webhook_obuma_actualizar_producto')">Copiar</button></pre>


			<code>Actualizar precio</code>

			<pre><input type="text" id="webhook_obuma_actualizar_precio"  readonly="readonly" name="" style="width: 90%;" value="<?php echo plugin_dir_url(dirname(__FILE__)) . "obuma_webhook_receiver_precios.php"; ?>"> <button class="btn btn-primary" onclick="copiarAlPortapapeles(this,'webhook_obuma_actualizar_precio')">Copiar</button></pre>

			<code>Actualizar stock</code>

			<pre><input type="text" id="webhook_obuma_actualizar_stock"  readonly="readonly" name="" style="width: 90%;" value="<?php echo plugin_dir_url(dirname(__FILE__)) . "obuma_webhook_receiver_productoStockCreated.php"; ?>"> <button class="btn btn-primary" onclick="copiarAlPortapapeles(this,'webhook_obuma_actualizar_stock')">Copiar</button></pre>


			<code>Crear cliente</code>

			<pre><input type="text" id="webhook_obuma_crear_cliente"  readonly="readonly" name="" style="width: 90%;" value="<?php echo plugin_dir_url(dirname(__FILE__)) . "obuma_webhook_receiver_clienteCreated.php"; ?>"> <button class="btn btn-primary" onclick="copiarAlPortapapeles(this,'webhook_obuma_crear_cliente')">Copiar</button></pre>
			

			<code>Actualizar cliente</code>

			<pre><input type="text" id="webhook_obuma_actualizar_cliente"  readonly="readonly" name="" style="width: 90%;" value="<?php echo plugin_dir_url(dirname(__FILE__)) . "obuma_webhook_receiver_clienteUpdated.php"; ?>"> <button class="btn btn-primary" onclick="copiarAlPortapapeles(this,'webhook_obuma_actualizar_cliente')">Copiar</button></pre>





			
			<?php// echo __FILE__; ?>

		</div>

	</div>

</div>


<script type="text/javascript">
	
function copiarAlPortapapeles(element,id_elemento) {

  element.classList.add("button_black");
  // Crea un campo de texto "oculto"
  var aux = document.createElement("input");

  // Asigna el contenido del elemento especificado al valor del campo
  aux.setAttribute("value", document.getElementById(id_elemento).value);

  // Añade el campo a la página
  document.body.appendChild(aux);

  // Selecciona el contenido del campo
  aux.select();

  // Copia el texto seleccionado
  document.execCommand("copy");

  // Elimina el campo de la página
  document.body.removeChild(aux);

  setInterval(function(){
  	element.classList.remove("button_black");
  },1000)

}

</script>