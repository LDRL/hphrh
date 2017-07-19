$(document).ready(function(){
   	$('#btnprecalguardar').click(function(){
   		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
		//alert('prueba');
		var formData = {
        identificacion:$("#identificacion").val(),
        identrevista:$("#identrevista").val(),
   			idempleado:$("#idempleado").val(),
        aportefamilia:$("#aportefamilia").val(),
   			fechaentre:$("#fechaentre").val(),
   			vivecompania:$("#vivecompania").val(),
   			mcorto:$("#mcorto").val(),
   			mmediano:$("#mmediano").val(),
   			mlargo:$("#mlargo").val(),
   			descpersonal:$("#descpersonal").val(),
   			trabajoequipo:$("#trabajoequipo").val(),
   			bajopresion:$("#bajopresion").val(),
   			atencionpublico:$("#atencionpublico").val(),
   			ordenado:$("#ordenado").val(),
   			entrevistadores:$("#entrevistadores").val(),
   			puntual:$("#puntual").val(),
   			presentacion:$("#presentacion").val(),
   			disponibilidad:$("#disponibilidad").val(),
   			dispfinsemana:$("#dispfinsemana").val(),
   			dispoviajar:$("#dispoviajar").val(),
   			bajopresion:$("#bajopresion").val(),
   			pretensionminima:$("#pretensionminima").val(),
        dedicanpadres:$("#dedicanpadres").val(),
        lugar:$("#lugar").val(),
        comunicar:$("#comunicar").val(),
        };
        console.log(formData);
       	$.ajax({
            type: "POST",
            url: "prentrevista",
            data: formData,
            dataType: 'json',

            success: function (data) {
            	  
				swal({ 
                    title:"Envio correcto",
                    text: "Información actualizada correctamente",
                    type: "success"
                });
            },
            error: function (data) {
                
            }
        });

	});
});