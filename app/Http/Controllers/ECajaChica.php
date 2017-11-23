<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Illuminate\Support\Facades\Auth;
use App\GastoEncabezado;
use App\GastoViajeEmpleado;
use App\Viaje;
use App\ViajeVehiculo;
use App\GastoViaje;
use App\Vehiculo;
use App\Persona;
use App\Constants;
use Illuminate\Support\Collection as Collection;

class ECajaChica extends Controller
{
    public function add(){
        $proyectos = DB::table('proyectocabeza as pca')
        ->select('pca.idproyecto','pca.nombreproyecto as proyecto')
        ->get();

        $vehiculos = DB::table('vehiculo as veh')
        ->join('vstatus as vst','veh.idvstatus','=','vst.idvstatus')
        ->select('veh.color','veh.marca','veh.modelo','veh.idvehiculo')
        ->where('veh.idvstatus','=',1)
        ->get();

        $eles = DB::table('codigointerno as cin')
        ->join('codigoraiz as cra','cin.idele','=','cra.idele')
        ->select('cin.codigo','cin.nombre','cra.codigo as L','cra.nombre as craiz')
        ->get();

        return view ('empleado.cajachica.create',["eles"=>$eles,"proyectos"=>$proyectos,"vehiculos"=>$vehiculos]);
    }

    public function store(Request $request){
        $this->validateRequest($request);
        try
        {
            DB::beginTransaction();
            $fechainicio = $request->fecha_inicio; 
            $fechafinal = $request->fecha_final;

            $fechainicio = Carbon::createFromFormat('d/m/Y',$fechainicio);
            $fechafinal = Carbon::createFromFormat('d/m/Y',$fechafinal);

            $fini = $fechainicio;
            $ffin = $fechafinal;

            $fechainicio = $fechainicio->toDateString();
            $fechafinal = $fechafinal->toDateString();

            if($fechafinal >= $fechainicio){

                //Gasto encabezado
                $encabezado = new GastoEncabezado;
                $viaje = new Viaje;
                $gastoviaje = new  GastoViaje;

                $days = 1;

                while ($ffin >= $fini) {
                    if($fini != $ffin){
                        $days++;
                        $fini->addDay();
                    }
                    else{
                        break;
                    }
                }

                $mytime = Carbon::now('America/Guatemala');
                $today = Carbon::now();
                $year = $today->format('Y');
                $month = $today->format('m');

                $encabezado-> fechasolicitud = $mytime->toDateString(); 
                $encabezado-> montosolicitado = $request->monto_solicitado;
                $encabezado-> chequetransfe = 'efectivo';
                $encabezado-> montogastado = 0;
                $encabezado-> fechaliquidacion = $mytime->toDateString();
                $encabezado-> moneda = $request->moneda;
                $encabezado-> periodo = $year.'/'.$month;
                $encabezado-> idtipogasto = 1;
                $encabezado-> idproyecto = $request->proyecto;
                $encabezado-> idempleado = $this->empleado()->idempleado;
                $encabezado-> statusgasto = 'solicitado';
                $encabezado-> statuspago = 0;
                $encabezado-> observacion = $request->motivo;

                $encabezado->save();

                $viaje-> fechainicio = $fechainicio;
                $viaje-> fechafin = $fechafinal;
                $viaje-> numerodias = $days;
                $viaje-> motivo = $request->motivo;

                $viaje->save();

                $gastoviaje-> idgastocabeza = $encabezado->idgastocabeza;
                $gastoviaje-> idviaje = $viaje->idviaje;
                $gastoviaje->save();

                if($request->veh === 'Si'){
                    $miArray = $request->vehiculo;
                    if ($miArray == null) {
                        return response()->json(array('error'=>'Debe agregar a la tabla los datos de un vehiculo, dando click en el boton buscar y seguidamente seleccionar el vehiculo y agregar '),404);
                    }
                    else
                    {
                        foreach ($miArray as $key => $value) {
                            $viajeveh = new ViajeVehiculo;

                            $viajeveh->idviaje = $viaje->idviaje;
                            $viajeveh->idvehiculo = $value['0'];
                            $viajeveh->kilometrajeini = $value['1'];
                            $viajeveh->kilometrajefin = 0;
                            $viajeveh->save();
                        }
                    }
                }
            }
            else{
                return response()->json(array('error'=>'la fecha inicio no puede ser mayor que la fecha final'),404);
            }

            DB::commit();
        }catch (\Exception $e) 
        {
            DB::rollback();
            return response()->json(array('error' => 'No se ha podido enviar la solicitud'),404);         
        }
        return response()->json($encabezado);
    }
}
