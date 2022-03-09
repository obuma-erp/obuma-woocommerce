<?php
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));



    $categorias_woo = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."terms t INNER JOIN " .$wpdb->prefix."term_taxonomy tt ON t.term_id=tt.term_id WHERE tt.taxonomy ='product_cat' AND t.name <> 'Uncategorized' ORDER BY t.name ASC");


    $cantidad = count($categorias_woo);

    if (isset($_POST["guardar_categorias"])) {
    	
    	for ($i=0; $i < count($_POST["term_id"]) ; $i++) { 
    		

    		$categoria_obuma = getObumaIdCategory($_POST["term_id"][$i]);

    		if(isset($categoria_obuma)){
    			$wpdb->query("UPDATE  ". $wpdb->prefix . "obuma_vincular_categorias SET obuma_id_category='".$_POST["obuma_id_category"][$i]."' WHERE categoria_woocommerce_id='".$_POST["term_id"][$i]."'");
    		}else{
    			$wpdb->query("INSERT INTO ". $wpdb->prefix . "obuma_vincular_categorias(categoria_woocommerce_id,categoria_woocommerce_nombre,obuma_id_category) VALUES " . "('".$_POST['term_id'][$i]."','".$_POST['name'][$i]."','".$_POST['obuma_id_category'][$i]."')");
    		}
    		

    	}
    }

    function getObumaIdCategory($term_id){
    	global $wpdb;
    	$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."obuma_vincular_categorias WHERE categoria_woocommerce_id='".$term_id."'",ARRAY_A);
    	return $result;
    }

    function vinculadas(){
    	global $wpdb;
    	$result = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."terms t INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON t.term_id=tt.term_id INNER JOIN  ".$wpdb->prefix."obuma_vincular_categorias vco ON t.term_id = vco.categoria_woocommerce_id  WHERE tt.taxonomy='product_cat'  AND vco.obuma_id_category > 0");
    	return count($result);
    }
?>


<div class="wrap">
	<h1 class="wp-heading-inline"> Vincular categorías con OBUMA </h1>
	<?php if (isset($_POST["guardar_categorias"])) { ?>
		<div id="message" class="notice notice-success"><p><strong>Todos los datos fueron guardados.</strong></p></div>
	<?php } ?>
	
	<p style="color:#0073aa;">Categorías Woocommerce : <b><?php echo $cantidad; ?> </b> - Categorías vinculadas : <b><?php echo vinculadas(); ?></b></p>
	
	<form action="" method="post">
	
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
	<div>
		<button style="display: flex !important;justify-content: flex-end !important;" type="submit" name="guardar_categorias" class="page-title-action">Guardar</button>
	</div>
	
</form>
</div>
