
<hr>
<div class="panel panel-info" style="height: auto;">
<div class="panel-heading bg-white">
  <?php _e('Panel de Sincronización con la API OBUMA','obuma') ?>
</div>
<div class='panel-body'>
  
  <?php

  $json = ObumaConector::get(set_url()."empresa.findByAPIKey.json/".get_option("api_key"),get_option("api_key"));

 
if (isset($json->data[0]->empresa_id) && $json->data[0]->empresa_id > 0) {
 echo "<div class='alert alert-success'>";
 echo "<strong>Se conectó a la API de Obuma correctamente </strong><br>";
 echo "Id de la empresa : {$json->data[0]->empresa_id}<br>";
 echo "Rut de la empresa : {$json->data[0]->empresa_rut}<br>";
 echo "Razón social : {$json->data[0]->empresa_razon_social}<br>";
 echo "Nombre de fantasia : {$json->data[0]->empresa_nombre_fantasia}<br>";
 echo "</div>";
}else{
 echo "<div class='alert alert-danger'>";
 echo "<strong>Hubo un error  al conectar con la API de Obuma,verifique el API KEY registrado en la configuración del plugin . <a class='btn btn-primary' href='admin.php?page=obuma/admin/configuracion.php'>Ir a la configuraci&oacute;n</a></strong><br>";

 echo "</div>";
}

  ?>
  <p><?php _e('Pulse en una de las opciones para sincronizar con la API de Obuma','obuma') ?></p>
<div class="row">

<div class="col-lg-3 col-xl-3 col-md-3">
  <!-- List group -->

<div class="list-group" id="myList" role="tablist">
  <a class="list-group-item list-group-item-action" data-toggle="list" href="obuma_clientes.php" data-pagina="clientes"  role="tab">
  <span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;  Clientes</a>
  <a class="list-group-item list-group-item-action" data-toggle="list" href="obuma_productos.php" data-pagina="productos" role="tab">
<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> 
  &nbsp; Productos
  </a>
<a class="list-group-item list-group-item-action" data-toggle="list" href="obuma_categorias_productos.php"  data-pagina="categorias_productos"  role="tab">
  <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>&nbsp; Categorias de  Productos</a>
  <a class="list-group-item list-group-item-action" data-toggle="list" href="obuma_precios.php" data-pagina="precios" role="tab">
  <span class="glyphicon glyphicon-usd" aria-hidden="true"></span>&nbsp;  Precios</a>
  <a class="list-group-item list-group-item-action" data-toggle="list" href="obuma_stock.php" data-pagina="stock" role="tab">
  <span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp;  Stock</a>

  <a class="list-group-item list-group-item-action" data-toggle="list" href="obuma_productos_imagenes.php" data-pagina="productos_imagenes" role="tab">
  <span class="glyphicon glyphicon-picture" aria-hidden="true"></span>&nbsp;  Imagenes de Productos</a>
</div>

</div>
<div class="col-xl-9 col-lg-9 col-md-9 centrar" id="r">
<div  id="cargar_vistas">

</div>

<div id="completado" style="overflow-y: scroll;height: 350px;">

</div>
</div>
</div>

</div>

</div>
