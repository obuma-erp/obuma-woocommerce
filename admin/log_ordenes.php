<?php
require_once "../wp-load.php";
require_once RAI_RUTA.'/admin/obuma-list-table.php'; 


class LogOrdenTable extends Obuma_List_Table {

	private $order;
  private $orderby;
  private $items_per_page = 10;
  private $desde;
  private $hasta;


  public function __construct(){
    parent::__construct(array(
            'singular' => "obuma_log_order",
            'plural' => "obuma_log_order",
            'ajax' => true
            ));

    $this->set_order();
    $this->set_orderby();
    $this->limit_paginacion();
  }

public function get_sql_results(){
    global $wpdb;
    $search = (isset($_REQUEST['s'])) ? trim($_REQUEST['s']) : false;
    	
    $args = array('id','order_woocommerce_id','fecha','hora','peticion','respuesta','estado');
    $sql_select = implode(', ', $args);
    $sql = "";
    $sql .= "SELECT " . $sql_select . " FROM " . $wpdb->prefix . "obuma_log_order";

    if($search){
    	$sql .= " WHERE id  LIKE '%".$search."%' OR order_woocommerce_id LIKE '%".$search."%' OR fecha LIKE '%".$search."%' OR peticion LIKE '%".$search."%'  OR respuesta LIKE '%".$search."%'  OR estado LIKE '%".$search."%'"; 
    }
    	
    $sql .= " ORDER BY " . $this->orderby ." " . $this->order;

    $sql_results = $wpdb->get_results($sql,ARRAY_A);
      return $sql_results;
}

public function set_order(){
  $order = 'DESC';
  if (isset($_GET['order']) AND $_GET['order']){
    $order = $_GET['order'];
  }
                   
  $this->order = esc_sql($order);
}

public function set_orderby(){
  $orderby = 'fecha';
  if (isset($_GET['orderby']) AND $_GET['orderby']){
    $orderby = $_GET['orderby'];
  }
  $this->orderby = esc_sql($orderby);
}

public function limit_paginacion(){
  $current_page = $this->get_pagenum();
  $this->desde = $current_page - 1;
  $this->hasta = $this->desde + $this->items_per_page;
}


function get_columns(){ 
  $columns = array (
      'id' => 'ID', 
      'order_woocommerce_id' => 'Woocomerce ID', 
      'fecha' => 'Fecha' ,
      'hora' => 'Hora' ,
    	'peticion' => 'Peticion',
    	'respuesta' => 'Respuesta',
    	'estado' => 'Estado' ); 

  return $columns; 
}
    
function prepare_items() {

  $columns = $this->get_columns(); 
  $hidden = array(); 
  $sortable = $this->get_sortable_columns(); 
  $this->_column_headers = array($columns ,$hidden , $sortable);
 
  $total_items = count($this->get_sql_results());

  $current_page = $this->get_pagenum();
  $this->set_pagination_args(array(
    'total_items' => $total_items,  // DEBEMOS calcular el número total de elementos
    'per_page'    => $this->items_per_page, // DEBEMOS determinar el número de elementos en cada página
    'total_pages' => ceil($total_items / $this->items_per_page)
  ));

  $data = array_slice($this->get_sql_results(),(($current_page-1)*$this->items_per_page),$this->items_per_page);
  $this->items =  $data;
 
} 

function column_default($item, $column_name) {
  return $item[$column_name];
}


function get_sortable_columns(){
  $sortable_columns = array(
    
    'id'  => array('id', true),
    'order_woocommerce_id' => array('order_woocommerce_id', true),
   	'fecha' => array('fecha',true),
   	'estado' => array('estado',true)
  );

  return $sortable_columns;
}

}  
function my_render_list_page(){
  $myListTable = new LogOrdenTable();
  echo '<div class="wrap"><h2>LOG DE &Oacute;RDENES</h2>'; 
  $myListTable->prepare_items(); 
  echo '<form method="GET">
  <input type="hidden" name="page" value="'.esc_attr($_REQUEST['page']).'" />';
  $myListTable->search_box("search", "search_id"); 
  echo '</form>';
  $myListTable->display(); 
  echo '</div>'; 

  

}

 my_render_list_page();

?>

