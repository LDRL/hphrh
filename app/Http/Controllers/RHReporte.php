<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Storage;
use DB;
use Carbon\Carbon;  // para poder usar la fecha y hora
use Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use App\Empleado;
use App\Puesto;
use App\Afiliado;
use App\Nomytras;
use App\Vacadetalle;
use App\Asignajefe;
use App\Persona;
use App\Constants;

class RHReporte extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function vpempleado(Request $request)
    {
        /*
    	$empleado=Empleado::join('nomytras as nt','empleado.idempleado','=','nt.idempleado')
        ->join('status as st','empleado.idstatus','=','st.idstatus')
        ->join('puesto as pu','nt.idpuesto','=','pu.idpuesto')
        ->join('afiliado as af','nt.idafiliado','=','af.idafiliado')
        ->join('caso as c','c.idcaso','=','nt.idcaso')
        ->select('empleado.idempleado','empleado.identificacion','empleado.nit','st.statusemp as statusn','pu.nombre as puesto','af.nombre as afiliado','c.idcaso',DB::raw('max(nt.idnomytas) as idnomytas'))
        ->where('c.idcaso','=',4)
        ->orwhere('c.idcaso','=',6)
        ->orwhere('c.idcaso','=',7)
        ->groupBy('empleado.idempleado')
        ->orderBy('af.nombre','asc')
        ->orderBy('pu.nombre','asc')
        ->get();*/

        $empleado = new Empleado();

        $empleado = $empleado->selectQuery(Constants::RH_vempleado,array());


        return view("rrhh.reporte.vpempleado",["empleado"=>$empleado]);

    }

    public function vpindex(Request $request)
    {
        /*
        $empleado=Empleado::join('nomytras as nt','empleado.idempleado','=','nt.idempleado')
        ->join('status as st','empleado.idstatus','=','st.idstatus')
        ->join('puesto as pu','nt.idpuesto','=','pu.idpuesto')
        ->join('afiliado as af','nt.idafiliado','=','af.idafiliado')
        ->join('caso as c','c.idcaso','=','nt.idcaso')
        ->select('empleado.idempleado','empleado.identificacion','empleado.nit','st.statusemp as statusn','pu.nombre as puesto','af.nombre as afiliado','c.idcaso',DB::raw('max(nt.idnomytas) as idnomytas'))
        ->where('empleado.idstatus','=', 2)
        ->groupBy('empleado.idempleado')      
        ->orderBy('af.nombre','asc')
        ->orderBy('pu.nombre','asc')
        ->get();*/

        $empleado = new Empleado();

        $empleado = $empleado->selectQuery(Constants::RH_vempleado,array());

        return view("rrhh.reporte.vpindex",["empleado"=>$empleado]);
    }


    public function calculardias(request $request, $id)
    {

        $dias =DB::table('vacadetalle as va')
            ->join('empleado as emp','va.idempleado','=','emp.idempleado')
            ->join('persona as per','emp.identificacion','=','per.identificacion')
            ->select('va.idempleado','va.idausencia','va.acuhoras','va.acudias','va.fecharegistro','va.idvacadetalle','va.solhoras','va.soldias') 
            ->where('emp.idempleado','=',$id)
            ->where('va.estado','=','1')
            ->orderBy('va.idvacadetalle','desc')
            ->first();

        $ausencia= DB::table('ausencia as a')
            ->join('empleado as emp','a.idempleado','=','emp.idempleado')
            ->join('persona as per','emp.identificacion','=','per.identificacion')
            ->join('users as U','per.identificacion','=','U.identificacion')
            ->select('a.autorizacion')
            ->orderBy('a.idausencia','DESC')
            ->where('idtipoausencia','=','3')
            ->where('U.id','=',$id)
            ->first();

        if($ausencia === null)
        {   $autorizacion = "ninguno";}
        else
        {   $autorizacion = $ausencia->autorizacion;    }

        $fecharegistro = $dias->fecharegistro;    
        $diasactual = $dias->acudias;   //obtiene la ultima fecha en donde se registro un nuevo registro
        $horasactual = $dias->acuhoras;
        $diasol = $dias->soldias;
        $horasol = $dias->solhoras;

        $dt = Carbon::parse($fecharegistro);  // convertimos la fecha en el formato Y-mm-dddd h:i:s
        $today = Carbon::now();
        $year = $today->format('Y');

        if((($year%4 == 0) && ($year%100)) || $year%400 == 0)
        {$year = 366;}
        else{$year = 365;}

        $ftoday = $today->toDateString();
       
        if($fecharegistro >= $ftoday)
        {
            $thoras = $horasactual + $horasol;
            $dias = $diasactual + $diasol; 
            if($thoras >= 8)
            {
                $thoras = $thoras -8;
                $dias = $dias +1;
            }
        }
        else
        {
            $add = $today->dayOfYear;  //obtiene los dias transcurridos hasta la fecha actual
            $dias = (strtotime($today)-strtotime($fecharegistro))/86400;
            $dias   = abs($dias); $dias = floor($dias); 
           
            $dias = $dias * 20;

            $dias = $dias / $year;
            $dias = round($dias, 2);

            $tdia = explode(".",$dias);

            $dias = $tdia[0];

            if (empty($tdia[1])) {
                $thoras =0;
                $thoras = $horasactual + $thoras + $horasol;
                $dias = $diasactual + $dias + $diasol; 
            }
            else{ 
                $thoras = $tdia[1];

                $thoras = '0.'.$thoras;
                $thoras = $thoras * 8;

                $thora = explode(".",$thoras);
                $thoras = $thora[0];

                $thoras = $horasactual + $thoras + $horasol;
                $dias = $diasactual + $dias + $diasol; 
            }

            if($thoras >= 8)
            {
                $thoras = $thoras -8;
                $dias = $dias +1;
            }      
        }
        $calculo = array($thoras,$dias,$autorizacion);
        return response()->json($calculo);
    }

    public function vempleado(Request $request,$id)
    {
    	$today = Carbon::now();
    
	   
	    $year1 = $today->format('Y');

    	$inicioaño = $year1.'-01-01';      // se concatena el año actual con un texto determinado para obtener el incio del año actual
    	$finaño = $year1.'-12-31';         // se concatena el año actual con un texto determinado para obtener el fin del año actual

	    $historialvacaciones =DB::table('ausencia as a')
	        ->join('empleado as emp','a.idempleado','=','emp.idempleado')
	        ->join('persona as per','emp.identificacion','=','per.identificacion') 
	        ->join('tipoausencia as ta','a.idtipoausencia','=','ta.idtipoausencia')
	        ->join('vacadetalle as vd','a.idausencia','=','vd.idausencia')
	        ->select('a.fechainicio','a.fechafin','a.autorizacion','a.fechasolicitud','a.totaldias','a.totalhoras',DB::raw('sum(a.totaldias - vd.soldias) as diastomados'),DB::raw('sum(a.totalhoras - vd.solhoras) as htomado'))
	        ->where('ta.ausencia','=','Vacaciones')
	        ->where('emp.idempleado','=',$id)
	        ->where('a.fechasolicitud', '>=', $inicioaño)
	        ->where('a.fechasolicitud', '<=', $finaño)
	        ->groupBy('a.fechainicio','a.fechafin','a.autorizacion','a.fechasolicitud','a.totaldias','a.totalhoras')
	        ->orderBy('a.fechasolicitud','desc')
	        ->get();

        $dias =DB::table('vacadetalle as va')
            ->join('empleado as emp','va.idempleado','=','emp.idempleado')
            ->join('persona as per','emp.identificacion','=','per.identificacion')
            ->select('va.idempleado','va.idausencia','va.acuhoras','va.acudias','va.fecharegistro','va.idvacadetalle','va.solhoras','va.soldias') 
            ->where('emp.idempleado','=',$id)
            ->where('va.estado','=','1')
            ->orderBy('va.idvacadetalle','desc')
            ->first();

        $ausencia= DB::table('ausencia as a')
            ->join('empleado as emp','a.idempleado','=','emp.idempleado')
            ->join('persona as per','emp.identificacion','=','per.identificacion')
            ->join('users as U','per.identificacion','=','U.identificacion')
            ->select('a.autorizacion')
            ->orderBy('a.idausencia','DESC')
            ->where('idtipoausencia','=','3')
            ->where('U.id','=',$id)
            ->first();

        if($ausencia === null)
        {   $autorizacion = "ninguno";}
        else
        {   $autorizacion = $ausencia->autorizacion;    }

        $fecharegistro = $dias->fecharegistro;    
        $diasactual = $dias->acudias;   //obtiene la ultima fecha en donde se registro un nuevo registro
        $horasactual = $dias->acuhoras;
        $diasol = $dias->soldias;
        $horasol = $dias->solhoras;

        $dt = Carbon::parse($fecharegistro);  // convertimos la fecha en el formato Y-mm-dddd h:i:s
        $today = Carbon::now();
        $year = $today->format('Y');

        if((($year%4 == 0) && ($year%100)) || $year%400 == 0)
        {$year = 366;}
        else{$year = 365;}

        $ftoday = $today->toDateString();
       
        if($fecharegistro >= $ftoday)
        {
            $thoras = $horasactual + $horasol;
            $dias = $diasactual + $diasol; 
            if($thoras >= 8)
            {
                $thoras = $thoras -8;
                $dias = $dias +1;
            }
        }
        else
        {
            $add = $today->dayOfYear;  //obtiene los dias transcurridos hasta la fecha actual
            $dias = (strtotime($today)-strtotime($fecharegistro))/86400;
            $dias   = abs($dias); $dias = floor($dias); 
           
            $dias = $dias * 20;

            $dias = $dias / $year;
            $dias = round($dias, 2);

            $tdia = explode(".",$dias);

            $dias = $tdia[0];

            if (empty($tdia[1])) {
                $thoras =0;
                $thoras = $horasactual + $thoras + $horasol;
                $dias = $diasactual + $dias + $diasol; 
            }
            else{ 
                $thoras = $tdia[1];

                $thoras = '0.'.$thoras;
                $thoras = $thoras * 8;

                $thora = explode(".",$thoras);
                $thoras = $thora[0];

                $thoras = $horasactual + $thoras + $horasol;
                $dias = $diasactual + $dias + $diasol; 
            }

            if($thoras >= 8)
            {
                $thoras = $thoras -8;
                $dias = $dias +1;
            }      
        }
        $calculo = array($thoras,$dias,$autorizacion);

        return view("rrhh.reporte.empleadov",["historialvacaciones"=>$historialvacaciones,"year"=>$year1,"calculo"=>$calculo]);
    }

    public function pempleado(Request $request,$id)
    {
        $today = Carbon::now();
    
        $year = $today->format('Y');
        $year1 = $today->format('Y');

        $inicioaño = $year1.'-01-01';      // se concatena el año actual con un texto determinado para obtener el incio del año actual
        $finaño = $year1.'-12-31';         // se concatena el año actual con un texto determinado para obtener el fin del año actual

        $ausencias=DB::table('ausencia as a')
        ->join('empleado as emp','a.idempleado','=','emp.idempleado')
        ->join('persona as per','emp.identificacion','=','per.identificacion')
        ->join('tipoausencia as ta','a.idtipoausencia','=','ta.idtipoausencia')
        ->select('a.fechainicio','a.fechafin','a.horainicio','a.horafin','a.juzgadoinstitucion','a.tipocaso','a.autorizacion','a.fechasolicitud','ta.ausencia as tipoausencia')
        ->where('emp.idempleado','=',$id)
        ->where('ta.ausencia','!=','Vacaciones')
        ->where('a.fechasolicitud', '>=', $inicioaño)
        ->where('a.fechasolicitud', '<=', $finaño)
        ->orderBy('a.fechasolicitud','desc')
        ->get();

        return view('rrhh.reporte.empleadop',["ausencias"=>$ausencias,"year"=>$year]);
    }
}
