<?php

require_once "functions.php";

set_comunas();

update_option('update_comunas_date',date("Y-m-d H:i:s"));

echo json_encode(array("result" => "true","date" => get_option('update_comunas_date')));
