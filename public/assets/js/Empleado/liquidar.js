(function( $ ) {

	'use strict';
	var resultadoglobal = "";
	var indice = "";
	var EditableTable = {

		options: {
			addButton: '#Glempleado',
			table: '#tabprueba',
			dialog: {
				wrapper: '#dialog',
				cancelButton: '#dialogCancel',
				confirmButton: '#dialogConfirm',
			}
		},
		
		initialize: function() {
			this
				.setVars()
				.build()
				.events();
		},

		setVars: function() {
			this.$table				= $( this.options.table );
			this.$addButton			= $( this.options.addButton );

			// dialog
			this.dialog				= {};
			this.dialog.$wrapper	= $( this.options.dialog.wrapper );
			this.dialog.$cancel		= $( this.options.dialog.cancelButton );
			this.dialog.$confirm	= $( this.options.dialog.confirmButton );

			return this;
		},

		build: function() {
			this.datatable = this.$table.DataTable({
				"language": {
					"decimal":        "",
				    "emptyTable":     "No hay datos disponibles en la tabla",
				    "info":           "Mostrar _START_ a _END_ de _TOTAL_ registros por pagina",
				    "infoEmpty":      "Mostrando 0 a 0 de 0 registros",
				    "infoFiltered":   "(filtered from _MAX_ total entries)",
				    "infoPostFix":    "",
				    "thousands":      ",",
				    "lengthMenu":     "Mostrar _MENU_ registros",
				    "loadingRecords": "Loading...",
				    "processing":     "Processing...",
				    "search":         "Buscar:",
				    "total": 		  "total",			
				    "zeroRecords":    "No se encontraron registros",
				    "paginate": {
				        "first":      "First",
				        "last":       "Last",
				        "next":       "Siguiente",
				        "previous":   "Anterior"
				    },
                },
				columns: [
					null, //id { "bVisible": false }
					null, //Fecha
					null, //Descripcion
					null, //#Factura
					null, //Empleado
					null, //Cuenta,
					//null, //Cliente
					null, //Evento
					// null, //LOB L10
					null, //Donador L8
					null, //Proyecto L9
					null, //Funcion L2
					null, //Monto
					//null, //Saldo
					{ "bSortable": false }
				]
			});
			window.dt = this.datatable;
			return this;
		},
		
		events: function() {
			var _self = this;

			this.$table
				.on('click', 'a.save-row', function( e ) {
					e.preventDefault();
					_self.rowSave( $(this).closest( 'tr' ) );

				})
				.on('click', 'a.cancel-row', function( e ) {
					e.preventDefault();

					_self.rowCancel( $(this).closest( 'tr' ) );
				})
				.on('click', 'a.edit-row', function( e ) {
					e.preventDefault();

					_self.rowEdit( $(this).closest( 'tr' ) );
					indice = $(this).closest( 'tr' );
				})
				.on( 'click', 'a.remove-row', function( e ) {
					e.preventDefault();

					var $row = $(this).closest( 'tr' );
					var id = $row.children('td').eq(0).html();
					var urlraiz=$("#url_raiz_proyecto").val();

					$.ajaxSetup({
                		headers: {
                    		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                		}
            		});

					swal({
	                	title: "¿Esta seguro?",
	                	text: "No podra recuperar este registro",
		                type: "warning",
		                showCancelButton: true,
		                confirmButtonColor: "#FFFF00",
		                confirmButtonText: "Si, eliminarlo",
		                cancelButtonText: "No, cancelar",
		                closeOnConfirm: false,
		                closeOnCancel: false
	            	}, function (isConfirm) {
	                	if (isConfirm) {
	                		$.ajax({
				                type: "DELETE",
				                url: urlraiz+"/empleado/viaje/liquidar/delete/"+id,
				                success: function (data) {
				                   	_self.rowRemove( $row);
				                    swal("Eliminado", "Se ha eliminado el registro", "success");
				                },
				                error: function (data) {
				                    console.log('Error:', data);
				                }
				            });
	                	} else {
	                    	swal("Cancelado", "No se ha eliminado el registro :)", "error");
	                	}
	            	});
				});

			this.$addButton.on( 'click', function(e) {
				e.preventDefault();
				//_self.rowAdd();
				var idliq=$('#idgastoemp').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                var urlraiz=$("#url_raiz_proyecto").val();
				var miurl;
                var type;
                var state=$("#Glempleado").val();


                var formData = {
                    empleado: $("#emple").val(),
                    factura : $("#factura").val(),
                    fecha_factura: $("#fechafactura").val(),
                    monto: $("#monto").val(),
                    descripcion: $("#descripcion").val(),
                    cuenta: $("#cuenta").val(),
                    proyecto: $("#proyecto").val(),
                    gastoviaje: $("#idgastoviaje").val(),
                    eventos: $("#evento").val(),
                    linea_presupuesto: $("#donador").val(),
                };

                if (state == "update") 
                {
                    type="PUT";
                    miurl = urlraiz+"/empleado/viaje/liquidar/update/"+idliq;
                }
                if (state == "add") 
                {
                    type="POST";
                    miurl = urlraiz+"/empleado/viaje/liquidar/store";
                } 
                $.ajax({
                    type: type,
                    url: miurl,
                    data: formData,
                    dataType: 'json',
             
                	success: function (data) {
                   		var urlraiz=$("#url_raiz_proyecto").val();
	                   	$.get(urlraiz+'/empleado/viaje/liquidar/updatemonto',function(data){
					            $("#disponible").html(data[0]);
					            $("#liquidacion").html(data[1]);
					       		$("#montot").html(data[2]);
					    });

	                   	if(state == "add"){	
	                   		_self.rowAdd(data);
	                   	}
	                   	if(state == "update"){
	                   		_self.rowUpdate(data);
	                   	}
              			
                        $('#formAgregarLiquidar').trigger("reset");
                        $('#formModalLiquidar').modal('hide');                            
                    },
                    
                    error: function (data) {                            
                        var errHTML="";
                        if((typeof data.responseJSON != 'undefined')){
                            for( var er in data.responseJSON){
                                errHTML+="<li>"+data.responseJSON[er]+"</li>";
                            }
                        }else{
                            errHTML+='<li>Error</li>';
                        } 
                        $("#erroresContent").html(errHTML); 
                        $('#erroresModal').modal('show');
                    }
                });
			});

			this.dialog.$cancel.on( 'click', function( e ) {
				e.preventDefault();
				$.magnificPopup.close();
			});

			return this;
		},

		// ==========================================================================================
		// ROW FUNCTIONS
		// ==========================================================================================
		rowAdd: function($data) {
			var _self     = this;
			this.$addButton.attr({ 'disabled': 'disabled' });
			var actions,
				data,
				$row;	

			actions = [
				'<a href="#" class="hidden on-editing save-row"><i class="fa fa-save"></i></a>',
				'<a href="#" class="hidden on-editing cancel-row"><i class="fa fa-times"></i></a>',
				'<a href="#" class="on-default edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="on-default remove-row"><i class="fa fa-trash-o"></i></a>'
			].join(' ');

				var n1 = $data.nombre1, 
				n2 = $data.nombre2, 
				n3 = $data.nombre3, 
				a1 = $data.apellido1,
				a2 = $data.apellido2,
				a3 = $data.apellido3;
				if(n2 == null){n2 = "";}
				if(n3 == null){n3 = "";}
				if(a2 == null){a2 = "";}
				if(a3 == null){a3 = "";}

			data = this.datatable.row.add([ $data.idgastoempleado,
											$data.fecha,
											$data.descripcion,
											$data.factura,
											n1+" "+n2+" "+n3+" "+a1+" "+a2+" "+a3,
											$data.cuenta,$data.evento,$data.donante,
											$data.proyecto,'',
											$data.monto,
											actions
										]);

			$row = this.datatable.row( data[0] ).nodes().to$();
			$row
				.addClass( 'adding' )
				.find('td')
				.addClass('actions');
			this.datatable.order([0,'asc']).draw(); 
			_self.rowSave($row);
		},

		rowCancel: function( $row ) {
			var _self = this,
				$actions,
				i,
				data;

			if ( $row.hasClass('adding') ) {
				this.rowRemove( $row );
			} else {

				data = this.datatable.row( $row.get(0) ).data();
				this.datatable.row( $row.get(0) ).data( data );

				$actions = $row.find('td.actions');
				if ( $actions.get(0) ) {
					this.rowSetActionsDefault( $row );
				}				
				this.datatable.draw();
			}
		},

		rowEdit: function( $row ) {
			var _self = this,
				dat;
			dat = this.datatable.row( $row.get(0) ).data();
            var id = $row.children('td').eq(0).html();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            var urlraiz=$("#url_raiz_proyecto").val();
			var miurl = urlraiz+"/empleado/viaje/liquidar/edit/"+id;
			$('#idgastoemp').val(id);

			$.get(miurl,function(data){
	            // $('#cuenta').append('<option value="opcion_nueva_1" selected="selected">Opción nueva 1</option>')
                $('#inputTitleLiquidar').html("Editar factura");	           
	            $("#modaliq").html(data);
	            $('#formModalLiquidar').modal('show');
	            $('#Glempleado').val('update');
	        });
		},

		rowUpdate: function($data){
			var element = $(this);

			var cont = $("#montot").val();
			var dataRows = element.find('tbody tr');

			//<td>{{$proyecto->monto = $proyecto->monto - $gvi->monto}}</td>

			var _self     = this,
				actions,
				$actions,
				values    = [];

				actions = [
				'<td class="actions">',
				'<a href="#" class="edit-row"><i class="fa fa-pencil"></i></a>',
				'<a href="#" class="remove-row"><i class="fa fa-trash-o"></i></a>',
				'</td>'
				].join(' ');
			var n1 = $data.nombre1, 
				n2 = $data.nombre2, 
				n3 = $data.nombre3, 
				a1 = $data.apellido1,
				a2 = $data.apellido2,
				a3 = $data.apellido3;
			if(n2 == null){n2 = "";}
			if(n3 == null){n3 = "";}
			if(a2 == null){a2 = "";}
			if(a3 == null){a3 = "";}

			values = [	$data.idgastoempleado,
						$data.fecha,
						$data.descripcion,
						$data.factura,
						n1+" "+n2+" "+n3+" "+a1+" "+a2+" "+a3,
						$data.cuenta,$data.evento,$data.donante,
						$data.proyecto,'',
						$data.monto,
						actions
					];

			this.datatable.row(indice).data(values);
			this.datatable.draw();
		},

		rowSave: function( $row ) {
			var _self     = this,
				$actions,
				values    = [];

			if ( $row.hasClass( 'adding' ) ) {
				this.$addButton.removeAttr( 'disabled' );
				$row.removeClass( 'adding' );
			}

			values = $row.find('td').map(function() {
				var $this = $(this);

				if ( $this.hasClass('actions') ) {
					_self.rowSetActionsDefault( $row );
					return _self.datatable.cell( this ).data();
				} else {
					return $.trim( $this.find('input').val() );
				}
			});

			this.datatable.row( $row.get(0) ).data( values );

			$actions = $row.find('td.actions');
			if ( $actions.get(0) ) {
				this.rowSetActionsDefault( $row );
			}

			this.datatable.draw();
		},

		rowRemove: function( $row ) {
			if ( $row.hasClass('adding') ) {
				this.$addButton.removeAttr( 'disabled' );
			}

			this.datatable.row( $row.get(0) ).remove().draw();
		},

		rowSetActionsEditing: function( $row ) {
			$row.find( '.on-editing' ).removeClass( 'hidden' );
			$row.find( '.on-default' ).addClass( 'hidden' );
		},

		rowSetActionsDefault: function( $row ) {
			$row.find( '.on-editing' ).addClass( 'hidden' );
			$row.find( '.on-default' ).removeClass( 'hidden' );
		}
	};
 
 	$(function() {
		EditableTable.initialize();
	});

}).apply( this, [ jQuery ]);


var cont = 0;

$(document).ready(function() {
    $('#btnEnviarL').hide();
    $('#vehiculos tr').each(function(){
        //var id = $(this).closest('tr').find('input[type="hidden"]').val();
        var id = $(this).find('td').eq(0).html();
        var kfin = $(this).find('td').eq(3).html();
        if(kfin == "")
        {
            cont++;   
        }
    });
    if(cont>0)
    {$('#btnEnviarL').hide();}
    else{$('#btnEnviarL').show();}
});

$(document).on('click','.btn-EnviarL',function(e){

	var $f = $(this);
    if($f.data('locked') == undefined && !$f.data('locked'))
    {
		swal({
		    title: "¿Esta seguro?",
		    text: "No podra modificar el registro por el momento",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#FFFF00",
			confirmButtonText: "Si, enviarlo",
			cancelButtonText: "No, cancelar",
			closeOnConfirm: false,
			closeOnCancel: false
		}, function (isConfirm) {
		  	if (isConfirm) {
		  		var urlraiz=$("#url_raiz_proyecto").val();
			    var miurl = urlraiz+"/empleado/viaje/liquidar/envio";

			    $.ajaxSetup({
			        headers: {
			            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			        }
			    });

			    var formData = {
			        gastocabeza: $('#idgastocabeza').val(),
			    };

			    $.ajax({
			        type: "POST",
			        url: miurl,
			        data: formData,
			        dataType: 'json',

			        success: function (data) {
			            $f.data('locked', true);

			            swal({ 
			                title:"Enviado",
			                text: "el registro ha sido enviado correctamente",
			                type: "success",
			                confirmButtonClass: 'btn-success waves-effect waves-light',
			                confirmButtonText: 'OK!'
			            },
			            function(){
			                cargar_formularioviaje(2);
			            });
			            $f.data('locked',false);
			            
			        },
			        error: function (data) {
			            $('#loading').modal('hide');
			            var errHTML="";
			            if((typeof data.responseJSON != 'undefined')){
			                for( var er in data.responseJSON){
			                    errHTML+="<li>"+data.responseJSON[er]+"</li>";
			                }
			            }else{
			                errHTML+='<li>Error.</li>';
			            }
			            $("#erroresContent").html(errHTML); 
			            $('#erroresModal').modal('show');
			        },
			    });
		   	} else {
		       	swal("Cancelado", "No se ha enviado el registro :)", "error");
		    }
		});
	}else{
        swal({
            title:"Envio en espera",
            text: "Se esta enviando su solicitud :)",
            type: "warning",
        });
    }
});

$(document).on('click','.btn-Glvehiculo',function(e){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    var idviajveh=$('#idviajveh').val();
    var urlraiz=$("#url_raiz_proyecto").val();
  	var miurl = urlraiz+"/empleado/viaje/vehiculo/update/"+idviajveh;
	var formData = {
        kilometraje_final: $("#kfinal").val(),
    };
         
    $.ajax({
        type: 'PUT',
        url: miurl,
        data: formData,
        dataType: 'json',

        success: function (data) {
   	    swal({
         	title:"Envio correcto",
            text: "Se ha guardado el registro correctamente",
            type: "success",
        });
        var item = '<tr class="even gradeA" id="vehiculos'+data.idviajevehiculo+'">';
            item +='<td>'+data.idviajevehiculo+'</td>'+'<td>'+data.marca+' '+data.color+''+data.modelo+'</td>'+'<td>'+data.kilometrajeini+'</td>'+'<td>'+data.kilometrajefin+'</td>';
            item += '<td><a href="javascript:void(0);" onclick="vehiculo('+data.idviajevehiculo+');"><i class="fa fa-pencil"></i></a></td></tr>';

            $("#vehiculos"+idviajveh).replaceWith(item);
            $('#formAgregar').trigger("reset");
            $('#formModalVehiculo').modal('hide');           
        },
        error: function (data) {
            $('#loading').modal('hide');
            var errHTML="";
            if((typeof data.responseJSON != 'undefined')){
                for( var er in data.responseJSON){
                    errHTML+="<li>"+data.responseJSON[er]+"</li>";
                }
            }else{
                errHTML+='<li>Error</li>';
            }
            $("#erroresContent").html(errHTML); 
            $('#erroresModal').modal('show');
        }
    });
});