jQuery(document).ready(function(){

  /*
  
  VARIABLES GLOBALES, USADAS EN TODO EL PROCESO DE SINCRONIZACION 
  */

  var resumen = "";
  var log = [];
  var agregados = 0;
  var actualizados = 0;
  var cabecera = "<div class='panel panel-info' style='margin-top:15px;'><div class='panel-heading'>Resumen de sincronización</div><div class='panel-body'><table class='table table-bordered table-striped table-condensed'>";
  var pie = "</table></div></div>";
  var categorias_seleccionadas = "all";
  var url = "";
  var before = {};

  /*
  AL INGRESAR A LA PAGINA DE SINCRONIZACION SE CARGA LA ETIQUETA SECCION DE RESULTADOS CON SU ICONO
  
  */

  jQuery("#cargar_vistas").html('<p class="centrar marTop">Sección de Resultados</p><br><span class="glyphicon glyphicon-signal icono"  aria-hidden="true"></span>')
  

let url_obuma_ajax = ajax_object.ajaxurl ? ajax_object.ajaxurl : ajax_object.ajax_url


  /*
  
  DAMOS CLICK EN EL PANEL DE SINCRONIZACION, SI HAY LA POSIBILIDAD DE SELECCIONAR CATEGORIAS, APARECERÁ EL SELECT, DE LO CONTRARIO EMPEZARÁ A SINCRONIZAR 
  
  */

  jQuery('#myList a').on('click',function(e){
  e.preventDefault();
  removerClaseListas();
  resetear();

  jQuery(this).addClass('active');
  url = jQuery(this).attr("href");
  var pagina = jQuery(this).attr("data-pagina");
  if (pagina == "productos_imagenes" || pagina == "precios" || pagina == "productos"  || pagina == "stock") {
    jQuery.ajax({
    method : "POST",
    url : url_obuma_ajax,
    data : {
      obtener : true,
      action : 'obuma_action',
      url :  "categorias"
    },
    beforeSend:function(response){
      jQuery("#cargar_vistas").html('<p class="centrar marTop" id="m">Sincronizando con la API <br>Por favor, espere ..</p><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');

    },
    complete:function(response){
        var result = JSON.parse(response.responseText);
        var html = "";
        html += '<div class="row" style="margin-top:20px;">'

        html += '<div class="col-lg-4">'
        html += "</div>"

        html += '<div class="col-lg-4">'
        html += '<div class="row">'
        html += '<div class="col-lg-12">'
        html += "<p>Elija una opcion para SINCRONIZAR</p>"
        html += '<div class="form-group">'
        html += '<select id="combo" class="form-control centrar_select" style="width:100%;max-width: 100%">'
        html += '<option value="all">TODAS LAS CATEGORIAS</option>'

        for (var i = 0; i < result.length; i++) {
            html += '<option value="'+result[i].producto_categoria_id+'">'+result[i].producto_categoria_nombre+'</option>'
        }

       html += "</select>"
       html += '</div>'
       html += "</div>"
       html += "</div>"

       html += '<div class="row">'
       html += '<div class="col-lg-12">'
       html += '<div class="form-group"><button class="btn btn-primary form-control" data-pagina="'+pagina+'" id="sincronizar_productos"><span class="glyphicon glyphicon-refresh"  aria-hidden="true"></span> SINCRONIZAR AHORA</button></div>'
       html += "</div>"
       html += "</div>"
       html += "</div>"

       html += '<div class="col-lg-4">'
       html += "</div>"

       html += '</div>'


       //html += '</div>'
       //html += '</div>'
       html += '<div class="row">'
       html += '<div id="loader_producto">'
       html += '</div>'
       html += '<div id="completado_producto">'
       html += '</div>'
       html += '</div>'
      jQuery("#cargar_vistas").html(html)
    }
     });

   
    
   
  
  }else{
    var html = "";
        html += '<div class="row" style="margin-top:20px;">'

        html += '<div class="col-lg-4">'
        html += "</div>"

        html += '<div class="col-lg-4">'
       
       html += '<div class="row">'
       html += '<div class="col-lg-12">'
       html += "<p>Presione para SINCRONIZAR</p>"
       html += '<div class="form-group"><button class="btn btn-primary form-control" data-pagina="'+pagina+'" id="sincronizar_productos"><span class="glyphicon glyphicon-refresh"  aria-hidden="true"></span> SINCRONIZAR AHORA</button></div>'
       html += "</div>"
       html += "</div>"
       html += "</div>"

       html += '<div class="col-lg-4">'
       html += "</div>"

       html += '</div>'


       //html += '</div>'
       //html += '</div>'
       html += '<div class="row">'
       html += '<div id="loader_producto">'
       html += '</div>'
       html += '<div id="completado_producto">'
       html += '</div>'
       html += '</div>'
      

    before = {
      id : "#cargar_vistas",
      content : '<p class="centrar marTop" id="m">Sincronizando con la API <br>Por favor, espere ..</p><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>'
    };

     jQuery(before.id).html(before.content)
    setTimeout(function(){
       jQuery(before.id).html(html)
     },1000);
  }

});

  /*
  
  CONSULTA AJAX AL BACKEND
  
  */


  function consultar(url,numero_pagina,before){
    var pagina = parseInt(numero_pagina) + 1;
    jQuery.ajax({
      method : "POST",
      url : url_obuma_ajax,
      data : { 
        pagina : pagina,
        categorias_seleccionadas : categorias_seleccionadas,
        action : 'obuma_action',
        url :  url

      },
      beforeSend:function(){
        if (pagina == 1) {
          jQuery(before.id).html(before.content)
        }   
      },
      success:function(response){
        comprobarRespuesta(response);
      },
      complete:function(response){
        console.log("completed");
      },
      error:function(error){
        console.log(error)
        alert("Hubo un error al realizar la Sincronización")
      }
    });
  }

  /*
  
  DAMOS CLICK EN EL BOTÓN SINCRONIZAR PRODUCTOS, ESTE BOTON SOLO APARECE CUANDO HAY LA POSIBILIDAD DE SELECCIONAR CATEGORIAS 
  
  */

  jQuery(document).on("click","#sincronizar_productos",function(){
    resetear();
    categorias_seleccionadas = jQuery("#combo").val();
    before = {
      id : '#loader_producto',
      content :'<p class="centrar marTop" id="m">Sincronizando con la API <br>Por favor, espere ..</p><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> <div id="ver" style="margin-top:8px;"><input type="checkbox" class="form-control" id="ver_resumen" style="margin:0px;"> <label style="margin:0px;" id="texto-resumen"> Ver resumen de Sincronización</label><div id="ver_logg" style="margin-top:8px;"> <input type="checkbox" class="form-control" id="ver_log" style="margin:0px;"> <label style="margin:0px;" id="texto-log"> Ver log de Sincronización</label></div></div>'
    };
    consultar(url,0,before);
  });
  

  /*
  
    COMPROBAMOS LA RESPUESTA DESDE EL BACKEND
  
  */

  function comprobarRespuesta(response){
      var result = JSON.parse(response);
      console.log(result);
      if (result.completado == result.total) {
          jQuery(".lds-spinner").html("<img style='width:100%;margin-bottom:5px;' src='../wp-content/plugins/obuma-woocommerce-main/admin/images/notification_done.png'>")
          finalizar();
          jQuery("#texto-resumen").css("color","#337ab7")
          jQuery("#texto-log").css("color","#337ab7")
          acumularResumen(result.resumen.resumen);
          acumularLog(result.log);
          if (comprobarCheckBox()) {
              mostrarResumen();
          }
          if (comprobarCheckBoxLog()) {
            mostrarLog();
          }       
      }else if(result.completado < result.total){
        acumularResumen(result.resumen.resumen);
        acumularLog(result.log);
        if (comprobarCheckBox()) {
            mostrarResumen();
        }

        if (comprobarCheckBoxLog()) {
            mostrarLog();
        }

        consultar(url,result.completado,before);
      }else{
        alert("Hubo un error , el numero de página actual es mayor al total de paǵinas");
      }
  }

 /*
  
  ACUMULAMOS EL LOG EN UN ARRAY DE OBJETOS PARA LUEGO MOSTRARLO EN FORMATO JSON  
  
  */

  function acumularLog(object){
      var tamanio = object.length;
      if (tamanio > 0) {
        for (var i = 0; i < object.length; i++) {

            var dl = {
              page : object[i].page,
              url : object[i].url,
              response : object[i].response
            };

            log.push(dl)
                   
        }
      }  
  }

  /*
  
  ACUMULAMOS EL RESUMEN EN UNA CADENA PARA LUEGO LISTARLO EN UNA TABLA 
  
  */

  function acumularResumen(object){
    var tamanio = object.length;
      if (tamanio > 0) {
        for (var i = 0; i < object.length; i++) {
          if(object[i].action == "agregado"){
            resumen += "<tr><td>Agregado</td><td>"+object[i].name+"</td></tr>"
            agregados++;
          }
          if(object[i].action == "actualizado"){
            resumen += "<tr><td>Actualizado</td><td>"+object[i].name+"</td></tr>"
            actualizados++;
          }
              
        }
      }       
  }

  /*
  
  MOSTRAR RESUMEN EN EL SINCRONIZADOR 
  
  */

  function mostrarResumen(){
    if(agregados == 0 && actualizados == 0){
      jQuery("#completado").html("<p>No hay cambios</p>");
    }else{
      jQuery("#completado").html(cabecera+resumen+pie);
    }
  }

  /*
  
  MOSTRAR LOG EN EL SINCRONIZADOR 
  
  */


  function mostrarLog(){
    jQuery("#completado").html("<pre>" +  JSON.stringify(log,undefined, 2) + "</pre>");
    
  }

  /*
  
  REMOVER LA CLASE ACTIVE DEL PANEL DE SINCRONIZACIÓN 
  
  */

  function removerClaseListas(){
    var opciones = jQuery("#myList a");
    for (var i = 0; i < opciones.length; i++) {
      jQuery(opciones[i]).removeClass("active");
    }
  }

  /*
  
  RESETEAR LOS CONTADORES , LAS VARIABLES DE RESUMEN,LOG Y LAS ETIQUETAS DE COMPLETED 
  
  */

  function resetear(){
    agregados = 0;
    actualizados = 0;
    resumen = "";
    log = [];
    jQuery("#completado").html("");
    jQuery("#completado_producto").html("");
  }


  /*
  
  IMPRIMIR MENSAJE AL FINALIZAR LA SINCRONIZACION 
  
  */

  function  finalizar(){
    jQuery("#m").text("SINCRONIZACIÓN COMPLETADA !");
    
  }

  /*
  
  CHECKBOX DE RESUMEN Y LOG 
  
  */

  //ESCUCHAMOS CAMBIOS EN EL CHECKBOX DE RESUMEN
  
  jQuery(document).on('change',"#ver_resumen", function() {
    if(comprobarCheckBox()) {
      mostrarResumen();
       jQuery("#ver_log").prop("checked", false);
    }else{
      jQuery("#completado").html("");
    }
  });

  //ESCUCHAMOS CAMBIOS EN EL CHECKBOX DE LOG
  
  jQuery(document).on('change',"#ver_log", function() {
    if(comprobarCheckBoxLog()) {
      mostrarLog();
      jQuery("#ver_resumen").prop("checked", false);
    }else{
      jQuery("#completado").html("");
    }
  });



  //Comprobar si el checkbox de resumen está seleccionado

  function comprobarCheckBox(){
    var test = false;
    if(jQuery("#ver_resumen").is(':checked')){
      test = true;
    }
    return test;
  }

  //Comprobar si el checkbox de log está seleccionado

  function comprobarCheckBoxLog(){
    var test = false;
    if(jQuery("#ver_log").is(':checked')){
      test = true;
    }
    return test;
  }


if(document.getElementById("sincronizar_comunas")){
  document.getElementById("sincronizar_comunas").addEventListener("click",function(){
  this.setAttribute("disabled","disabled")


      jQuery.ajax({
    method : "POST",
    url : url_obuma_ajax,
    data : {
      action : 'obuma_action',
      url :  "set_comunas"
    },
    dataType : "json",
    complete:function(response){
      var result = response.responseJSON;
       if(result.result == "true"){
              document.getElementById("update_comunas_message").innerText = "Ultima sincronizacion : "
              document.getElementById("update_comunas").innerText = result.date
              document.getElementById("sincronizar_comunas").removeAttribute("disabled")
       }

    }

    });



    
  })
}



if(document.getElementById("limpiar_registros")){
  document.getElementById("limpiar_registros").addEventListener("click",function(){
  this.setAttribute("disabled","disabled")

    jQuery.ajax({
    method : "POST",
    url : url_obuma_ajax,
    data : {
      action : 'obuma_action',
      url :  "limpiar_registros"
    },
    dataType : "json",
    complete:function(response){
      var result = response.responseJSON;
      
      if(result.result == "true"){
        document.getElementById("update_limpiar_registros_message").innerText = "Ultima Limpieza : "
        document.getElementById("update_limpiar_registros").innerText = result.date
        document.getElementById("limpiar_registros").removeAttribute("disabled")
      }

    }

    });


    
  })
}
});



