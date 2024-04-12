<?php
    


    if($_SERVER['REQUEST_METHOD'] == "POST"){

        $bodega = $_POST['bodega'];
        $order_id = $_POST['order_id'];



        if(update_post_meta($order_id, 'bodega_obuma', $bodega)){


            echo json_encode(array("result" => true , "html" => "<p class='notice notice-success' style='height:50px;display:flex;align-items:center;'><b>La bodega de OBUMA fue asociada a la orden</b></p>"));
            exit();
        }


    }else{

        $get_bodegas = get_bodegas();

        $data_get_bodegas = $get_bodegas["data"];


        $bodega_actual = get_post_meta($order_id, 'bodega_obuma',true);

        echo "<p><b>Seleccione la bodega que desea vincular a esta orden</b></p>";

        echo "<p><b>Si selecciona una bodega, esta se asociar&aacute; a la orden cuando sea enviada a OBUMA, de lo contrario se asociar&aacute; la bodega registrada en la configuraci&oacute;n del plugin</b></p>";

        echo "<p id='message_set_bodega'></p>";
        echo "<select id='list_obuma_bodegas' style='width:25%;'>";

        echo "<option>Seleccione la bodega</option>";

        foreach ($data_get_bodegas as $key => $value) {

            $selected = "";
            if($bodega_actual == $value["empresa_bodega_codigo"]){

                $selected = "selected";
            }
            echo "<option ".$selected." value='".$value["empresa_bodega_codigo"]."'>".$value["empresa_bodega_nombre"]."</option>";

        }


        


        echo "<select>";



    }



?>



<script type="text/javascript">


    jQuery(document).ready(function(){

    let url_obuma_ajax = ajax_object.ajaxurl ? ajax_object.ajaxurl : ajax_object.ajax_url


    let obuma_order_id = document.getElementById("post_ID").value
    if(document.getElementById("list_obuma_bodegas")){

        document.getElementById("list_obuma_bodegas").addEventListener("change",save_obuma_bodega);


    }
    
    function save_obuma_bodega(){



            jQuery.ajax({
    method : "POST",
    url : url_obuma_ajax,
    data : {
      action : 'obuma_action',
      url :  "admin_list_bodegas",
      bodega : this.value,
      order_id : obuma_order_id
    },
    dataType : "json",
    complete:function(response){
      
      var result = response.responseJSON;

      if(result.result){

        document.getElementById("message_set_bodega").innerHTML = result.html
      }

    }

    });


}

    });


/*

        const options = {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({ bodega : this.value,
                action : 'obuma_action',
                url : 'admin_list_bodegas'})
        };


        fetch(url_obuma_ajax, options)
          .then(response => response.text())
          .then(data => {
            
            console.log(data)
          });
        

    
*/

</script>