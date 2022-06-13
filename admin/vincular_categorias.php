<?php
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));


require_once "functions.php";


	$taxonomia_configurar = "product_cat";

	$option_seleccionar_taxonomias = get_option("seleccionar_taxonomias");
	$taxonomias_seleccionadas = empty(trim($option_seleccionar_taxonomias)) ? [$taxonomia_configurar] : explode(",",$option_seleccionar_taxonomias);

	if(isset($_GET['option'])){
		$taxonomia_configurar = $_GET['option'];
		
	}

	

    $categorias_woo = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."terms t INNER JOIN " .$wpdb->prefix."term_taxonomy tt ON t.term_id=tt.term_id WHERE tt.taxonomy ='".$taxonomia_configurar."' AND t.name <> 'Uncategorized' ORDER BY t.name ASC");


    $cantidad = count($categorias_woo);

    if (isset($_POST["guardar_categorias"])) {
    	
    	for ($i=0; $i < count($_POST["term_id"]) ; $i++) { 
    		

    		$categoria_obuma = getObumaIdCategory($_POST["term_id"][$i]);

    		if(isset($categoria_obuma)){
    			$wpdb->query("UPDATE  ". $wpdb->prefix . "obuma_vincular_categorias SET obuma_id_category='".$_POST["obuma_id_category"][$i]."',woocommerce_taxonomy='".$_POST["taxonomia_seleccionada"]."' WHERE categoria_woocommerce_id='".$_POST["term_id"][$i]."'");
    		}else{
    			$wpdb->query("INSERT INTO ". $wpdb->prefix . "obuma_vincular_categorias(categoria_woocommerce_id,categoria_woocommerce_nombre,obuma_id_category,woocommerce_taxonomy) VALUES " . "('".$_POST['term_id'][$i]."','".$_POST['name'][$i]."','".$_POST['obuma_id_category'][$i]."','".$_POST["taxonomia_seleccionada"]."')");
    		}
    		

    	}
    }

    $vinculadas =  vinculadas($taxonomia_configurar);

?>

<?php echo check_version(); ?>

<div class="wrap">
	<h1 class="wp-heading-inline"> Vincular categorías con OBUMA </h1>

	<select onchange="window.location.href='?page=vincular_categorias&option='+this.value">
	<?php foreach ($taxonomias_seleccionadas as $key => $ts) { ?>
		echo "<option value='<?php echo $ts; ?>' <?php if(isset($_GET["option"])){if($_GET["option"] == $ts){echo "selected";}} ?>><?php echo $ts; ?></option>
	<?php } ?>
</select>

<br>
	<?php if (isset($_POST["guardar_categorias"])) { ?>
		<div id="message" class="notice notice-success"><p><strong>Todos los datos fueron guardados.</strong></p></div>
	<?php } ?>
	
	


	<p style="color:#0073aa;">Categorías Woocommerce : <b><?php echo $cantidad; ?> </b> - Categorías vinculadas : <b><?php echo $vinculadas; ?></b></p>
	
	<form action="" method="post">
	<input type="hidden" name="taxonomia_seleccionada" value="<?php echo $taxonomia_configurar; ?>">
	<table class="wp-list-table widefat fixed striped pages">
		<thead>
			<tr>
				<th>ID Woocommerce</th>
				<th>Nombre Woocommerce</th>
				<th>ID Categoria OBUMA</th>
			</tr>
		</thead>
		<tbody id="the-list">

			<?php foreach ($categorias_woo as $categoria): ?>
			<tr>
				<td><?php echo $categoria->term_id; ?> <input type="hidden" name="term_id[]" value="<?php echo $categoria->term_id; ?>"></td>
				<td><?php echo $categoria->name; ?> <input type="hidden" name="name[]" value="<?php echo $categoria->name; ?>"></td>
				<td><input type="text" placeholder="ID Obuma" name="obuma_id_category[]" value="<?php echo isset(getObumaIdCategory($categoria->term_id)['obuma_id_category']) ? getObumaIdCategory($categoria->term_id)['obuma_id_category'] : ''; ?>"></td>
			</tr>
			<?php endforeach ?>
			
		</tbody>
	</table>
	<br>

	<?php if($cantidad > 0){ ?>
		<div>
			<button style="display: flex !important;justify-content: flex-end !important;" type="submit" name="guardar_categorias" class="page-title-action">Guardar</button>
		</div>
	<?php } ?>
	
	
</form>
</div>
