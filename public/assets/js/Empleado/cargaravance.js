function cargar_formularioviaje(arg){
  var urlraiz=$("#url_raiz_proyecto").val();

   $("#capa_modal").show();
   $("#capa_formularios").show();
   var screenTop = $(document).scrollTop();
   $("#capa_formularios").css('top', screenTop);
   $("#capa_formularios").html($("#cargador1").html());
   //if(arg==1){ var miurl=urlraiz+"/form_nuevo_usuario"; }

   if(arg==0){ var miurl=urlraiz+"/empleado/viaje/retornaindex"; }
   if(arg==1){ var miurl=urlraiz+"/empleado/viaje/add"; }
   if(arg==2){ var miurl=urlraiz+"/empleado/viaje/solicitar"; }
   if(arg==3){ var miurl=urlraiz+"/empleado/viaje/liquidar"; }
   if(arg==4){ var miurl=urlraiz+"/empleado/viaje/indexhistorial"; }
   if(arg==5){ var miurl=urlraiz+"/empleado/cajachica/add"; }

   /*

      if(arg==1){ var miurl=urlraiz+"/empleado/viaje/add"; }
   if(arg==2){ var miurl=urlraiz+"/empleado/viaje/liquidar"; }
   if(arg==3){ var miurl=urlraiz+"/empleado/viaje/solicitar"; }

   */
   //Listado de Jefe Inmediato Autorizaciones Vacacioens Y permisos


   if(arg==20){ var miurl=urlraiz+"/ji/viajejf/solicitados"; }
   if(arg==21){ var miurl=urlraiz+"/ji/viajejf/autorizados"; }
   if(arg==22){ var miurl=urlraiz+"/ji/viajejf/rechazados"; }
   if(arg==23){ var miurl=urlraiz+"/asistete/viaje/avance"; }
   if(arg==24){ var miurl=urlraiz+"/ji/viajejf/revisionji"; }
   if(arg==25){ var miurl=urlraiz+"/asistete/viaje/avancesol"; }

    $.ajax({
    url: miurl
    }).done( function(resul) 
    {
    	$("#capa_formularios").html(resul);
    }).fail( function() 
    {
    	$("#capa_formularios").html('<span>...Ha ocurrido un error, revise su conexión y vuelva a intentarlo...</span>');
    });
}

function cargar_liquidacion(arg,$id){
    var urlraiz=$("#url_raiz_proyecto").val();
    $("#capa_modal").show();
    $("#capa_formularios").show();
    var screenTop = $(document).scrollTop();
    $("#capa_formularios").css('top', screenTop);
    $("#capa_formularios").html($("#cargador1").html());
    if(arg==3){ var miurl=urlraiz+"/empleado/viaje/liquidar/"+$id; }
    if(arg==4){ var miurl=urlraiz+"/empleado/cajachica/liquidar/"+$id; }

    $.ajax({
        url: miurl
    }).done( function(resul) 
    {
        $("#capa_formularios").html(resul);
    }).fail( function() 
    {
        $("#capa_formularios").html('<span>...Ha ocurrido un error, revise su conexión y vuelva a intentarlo...</span>');
    });
}

function cargar_formularioasistente(arg){
    var urlraiz=$("#url_raiz_proyecto").val();
    $("#capa_modal").show();
    $("#capa_formularios").show();
    var screenTop = $(document).scrollTop();
    $("#capa_formularios").css('top', screenTop);
    $("#capa_formularios").html($("#cargador1").html());

    if(arg==0){ var miurl=urlraiz+"/asistente/viaje/retornaindex"; }
    if(arg==1){ var miurl=urlraiz+"/asistente/cajachica/create"; }
    if(arg==2){ var miurl=urlraiz+"/asistente/cajachica/indexliquidar"; }
    if(arg==3){ var miurl=urlraiz+"/asistente/viaje/liquidar"; }
    if(arg==4){ var miurl=urlraiz+"/asistente/viaje/indexhistorial"; }
    if(arg==5){ var miurl=urlraiz+"/asistente/cajachica/add"; }

    $.ajax({
    url: miurl
    }).done( function(resul) 
    {
      $("#capa_formularios").html(resul);
    }).fail( function() 
    {
      $("#capa_formularios").html('<span>...Ha ocurrido un error, revise su conexión y vuelva a intentarlo...</span>');
    });
}