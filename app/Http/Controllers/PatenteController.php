<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;


class PatenteController extends Controller
{
    public function getDataByPatent (Request $request){
        
        $aReqData = array();
        $aReqData["request"]   = isset($request->patent) ? $request->patent : "";
        $aReqData["result"]    = false;
        $aReqData["message"]   = "";
        $aReqData["response"]  = [];
        
        $vehiculo = Vehiculo::select([
                                        'patente',
                                        'tipo_vehiculo AS tipoVehiculo',
                                        'marca_vehiculo AS marcaVehiculo',
                                        'modelo_vehiculo AS modeloVehiculo',
                                        'anio_vehiculo AS anioVehiculo',
                                        'numero_motor_vehiculo AS numeroMotorVehiculo',
                                        'rut_propietario AS rutPropietario',
                                        'nombre_propietario AS nombreApellidoPropietario'
                                    ])
                                    ->where("patente","like", "%".$request->patent."%")
                                    ->take(10)
                                    ->get();

        if(count($vehiculo) <= 0){
            $aReqData["message"]   = "Patente sin resultados";
            return response()->json($aReqData, 404);
        }

        $aReqData["result"]   = true;
        $aReqData["response"] = $vehiculo;
        return $aReqData;
    }
}
