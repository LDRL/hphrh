<div class="card-box">
    <div class="panel-heading">
        <button class="btn btn-success waves-effect waves-light btn-nuevoV" id="btnnuevoV" title="Nueva solicitud de vacaciones">
            Nuevo <i class="fa fa-plus"></i>
        </button>
    </div>
    <div><br></div>
    
    @if (!empty($usuarios->idmunicipio))
        <input type="hidden" name="idmunicipio" value="{{$usuarios->idmunicipio}}" id="idmunicipio">
    @else
        <td><input type="hidden" name="" id="municipio" value=""></td>
    @endif

    @if($ausencia != null)
        <input type="hidden" name="idempleado" value="{{$usuarios->idempleado}}" id="idempleado">
        <input type="hidden" name="name" value="{{$usuarios->nombre}}" id="name">
        <input type="hidden" name="solhoras" value="{{$ausencia->totalhoras}}" id="solhoras">
        <input type="hidden" name="soldias" value="{{$ausencia->totaldias}}" id="soldias">
        <input type="hidden" name="idvacadetalle" value="{{$ausencia->idvacadetalle}}" id="idvacadetalle">
    @else
        <input type="hidden" name="idempleado" value="{{$usuarios->idempleado}}" id="idempleado">
        <input type="hidden" name="name" value="{{$usuarios->nombre}}" id="name">
        <input type="hidden" name="solhoras" value="0" id="solhoras">
        <input type="hidden" name="soldias" value="0" id="soldias">
        <input type="hidden" name="idvacadetalle" value="{{$vacaciones->idvacadetalle}}" id="idvacadetalle">
    @endif()
        <input type="hidden" name="emergencia" id="emergencia" value="0">

    <div class="row">
        <div class=class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover" id="dataTableItems">
                    <thead>
                        <th>Solicitud</th>
                        <th>Iniicio</th>
                        <th>Fin</th>
                        <th>Días solicitados</th>
                        <th>Horas solicitados</th>
                        <th>Días tomados</th>
                        <th>Horas tomadas</th>
                        <th>Autorizacion</th>
                        <th>opciones</th>
                    </thead>
                      @if (isset($ausencias))
                        @foreach ($ausencias as $aus)
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $aus->fechasolicitud)->format('d-m-Y')}}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $aus->fechainicio)->format('d-m-Y')}}</td>
                            <td>{{\Carbon\Carbon::createFromFormat('Y-m-d', $aus->fechafin)->format('d-m-Y')}}</td> 
                            <td>{{$aus->totaldias}}</td>
                            <td>{{$aus->totalhoras - 0}}</td>
                            @if(($aus->htomado/10000) == -4)

                                <td>{{$aus->diastomados - 1}}</td>
                                <td>{{abs($aus->htomado)/10000}}</td>
                            @else
                                <td>{{$aus->diastomados}}</td>
                                <td>{{$aus->htomado / 10000}}</td>
                            @endif
                            <td>{{$aus->autorizacion}}</td>
                            @if ( $aus->autorizacion == 'Autorizado')
                                <td> <button type="button" class="btn btn-primary btn-GoceV" id="btnconfirmar">Confirmar goce</button></td>
                            @else
                                <td></td>
                            @endif
                        </tr>                
                        @endforeach
                    @endif()
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <input type="hidden" name="tdias" id="tdias">
            <input type="hidden" name="thoras" id="thoras">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="inputTitle"></h4>
                </div>
              
                    <form role="form" id="formAgregar">
                        <div class="modal-header">
                        <br>                           
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Días acumulados</label>
                                <input id="dacumulado" type="text" class="form-control" name="dias">   
                            </div>
                        </div>
                        <div id="divEmergencia">
                        <br>
                            <input type="checkbox" id="casillaEmergencia" value="1" onclick="desactivarEmergencia()"/> Esta solicitud es por un caso de emergencia
                        </div>

                        <div class="modal-header">
                            <div><p><br></p></div>

                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Fecha inicio</label>
                                <div class="input-group">
                                    <input type="text" id="fecha_inicio" class="form-control" name="fechainicio">
                                    <span class="input-group-addon bg-primary b-0 text-white"><i class="ion-calendar"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Fecha final</label>
                                <div class="input-group">
                                    <input type="text" id="fecha_final" class="form-control" name="fechafin">
                                    <span class="input-group-addon bg-primary b-0 text-white"><i class="ion-calendar"></i></span>
                                </div>
                            </div>
                        </div>


                        <div class="modal-header">
                        <br>
                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                <br>
                                <button type="button" class="btn btn-success btn-datomarV" id="btndatomarV">Calcular Días</button>
                            </div>

                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <p>Usted esta solicitando vacaciones por</p>
                            </div>

                             <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
                                <label for="numerodependientes">Dias</label>
                                <input id="datomar" type="number" name="numerodependientes" min="0" class="form-control" onkeypress="return valida(event)">
                            </div>

                            <div class="col-lg-1 col-md-12 col-sm-12 col-xs-12">
                            <br><br>
                            <p>Y</p>
                            </div>

                            <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12">
                                <label for="horainicio">Hora</label>
                                <select name="hhoras" id="hhoras" class="form-control">
                                    <option value="00">0</option>
                                    <option value="04">4</option>
                                </select>
                            </div>

                        </div>

                        <div id="ModificaActiva">
                        <h4><strong>Si no es correcto haga click aqui para modificar</strong></h4>
                        <input type="checkbox" id="casilla" value="1" onclick="desactivar()"/>Activar campo Dias y Hora
                        </div>

                        <div class="modal-header">
                            <div><p><br></p></div>
                            <div class="form-group">
                                <label>Justificación</label>
                                <textarea class="form-control" placeholder=".........." id="observaciones" rows="3" maxlength="100"></textarea>
                            </div>
                        </div>
                        
                    </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-guardarV" id="btnguardarV">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="erroresModal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="inputError"></h4>
      </div>

      <div class="modal-body">
        <ul style="list-style-type:circle" id="erroresContent"></ul>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12">
    <div class="modal fade" id="formGoce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <input type="hidden" name="tdias" id="tdias">
            <input type="hidden" name="thoras" id="thoras">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="Title"></h4>
                </div>
              
                    <form role="form" id="formModificar">
                        <div class="modal-header">
                        <br>                           
                            
                            <div class="radio radio-danger radio-inline">
                                <input type="radio" id="inlineRadio1" value="No_gozado" name="autorizacion" checked>
                                <label for="inlineRadio1">No Gozadas</label> <!--No se tomo ningun dia solicitado-->
                            </div>
                            
                            <div class="radio radio-success radio-inline">
                                <input type="radio" id="inlineRadio2" value="Goce_temporal" name="autorizacion" checked onclick="mostrar()">
                                <label for="inlineRadio2">Gozados temporalmente</label><!--No se tomo a su totalidad los dias solicitados-->
                            </div>
                            <div class="radio radio-info radio-inline">
                                <input type="radio" id="inlineRadio16" value="Si_gozado" name="autorizacion" checked>
                                <label for="inlineRadio16">Gozadas</label> <!-- Se tomo todos los dias solicitados -->
                            </div>
                        </div>
                       
                        <div class="modal-header" id="oculto">
                            <br>
                            <h4>Agregue los d&iacute;as que no tomo</h4>

                             <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                <label for="numerodependientes">Dias</label>
                                <input id="dtomados" type="number" name="numerodependientes" min="0" class="form-control" onkeypress="return valida(event)">
                            </div>

                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                <label for="horainicio">Hora</label>
                                <select name="hhoras" id="htomadas" class="form-control">
                                    <option value="00">00</option>
                                    <option value="04">04</option>
                                </select>
                            </div>

                        </div>

                        <div class="modal-header">
                            <div><p><br></p></div>
                            <div class="form-group">
                                <label>Justificación</label>
                                <textarea class="form-control" placeholder=".........." id="observaciones" rows="3" maxlength="100"></textarea>
                            </div>
                        </div>          
                    </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-ConfirmarV" id="btnConfirmarV">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

    
    <script>

        function desactivarEmergencia() {
            if($("#casillaEmergencia:checked").val()==1) {
                $("#casillaEmergencia").attr('disabled', 'disabled');
                $("#divJustificacion").show();
                document.getElementById('emergencia').value = 1;
                }
            }

        function desactivar() {
            if($("#casilla:checked").val()==1) {
                $("#casilla").attr('disabled', 'disabled');
                $('#datomar').removeAttr("disabled");
                $('#hhoras').removeAttr("disabled");
                }
            }

        function mostrar() {
            if($("#inlineRadio2:checked").val()=="Goce_temporal") {
                $("#oculto").show();
                $("#inlineRadio16").attr('disabled', 'disabled');
                $("#inlineRadio1").attr('disabled', 'disabled');
            }
        }

    $(document).on('click','.btn-nuevoV',function(e){
        $("#ModificaActiva").hide();
        $("#divJustificacion").hide();


        var errHTML="";
        e.preventDefault();
        $.get('vacaciones/calculardias',function(data){
           
            var horas = '';
            var dias = '';
            var tdh;

            $.each(data,function(){
                horas = data[0];
                dias = data[1];
                autorizacion = data[2];
            })

            if(autorizacion == 'Autorizado' || autorizacion == 'solicitado')
            {
                //alert('No puede realizar una solicitud porque tiene una en proceso');
                swal({
                    title: "Solicitud denegada",
                    text: "No puede realizar una solicitud porque tiene una en proceso",
                    type: "error",
                    confirmButtonClass: 'btn-danger waves-effect waves-light',
                });
            }
            else{
                $('#inputTitle').html("Solicitud de vacaciones");
                $('#formAgregar').trigger("reset");
                $('#formModal').modal('show');
                $('#datomar').attr('disabled', 'disabled');
                $('#hhoras').attr('disabled', 'disabled');
                $('#dacumulado').attr('disabled', 'disabled');
                $('#btnguardarV').attr('disabled', 'disabled'); 

                tdh = (dias + ' ' + 'dias' + ' ' + 'con' +' '+ horas +' '+ 'horas');
                document.getElementById('dacumulado').value = tdh;
                document.getElementById('tdias').value = dias;
                document.getElementById('thoras').value = horas;
            }
        });
    });
    </script>

    <script src="{{asset('assets/js/vacaciones.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js')}}"></script>       
    <script src="{{asset('assets/plugins/bootstrap-datepicker/dist/js/conversion.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-sweetalert/sweet-alert.min.js')}}"></script>
    <script src="{{asset('assets/pages/jquery.sweet-alert.init.js')}}"></script>