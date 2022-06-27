<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;

use Exception;
use Log;

class PatenteController extends Controller
{
    public function getDataByPatent (Request $request){
        
        Log::channel("getDataByPatent")->info("Inicio Bucar Datos por patente");
        Log::channel("getDataByPatent")->info("Request: ".json_encode($request->all()));

        $aReqData = array();
        $aReqData["request"]   = isset($request->patent) ? $request->patent : "";
        $aReqData["result"]    = false;
        $aReqData["message"]   = "";
        $aReqData["response"]  = [];
        
        try{
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
        }catch(Exception $e){
            $aReqData["message"]   = "Error no controlado";
            Log::channel("getDataByPatent")->error("Error en la linea: ".$request->getLine());
            Log::channel("getDataByPatent")->error("Mensaje: ".$request->getMessage());
            return response()->json($aReqData, 500);
        }

        if(count($vehiculo) <= 0){
            $aReqData["message"]   = "Patente sin resultados";
            return response()->json($aReqData, 404);
        }

        $aReqData["result"]   = true;
        $aReqData["response"] = $vehiculo;

        Log::channel("getDataByPatent")->info("Response: ".json_encode($aReqData));
        Log::channel("getDataByPatent")->info("Fin Bucar Datos por patente");
        return $aReqData;
    }
}