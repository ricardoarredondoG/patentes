<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Vehiculo;
use App\Models\Contacto;
use App\Models\LogEmailSend;

use Exception;
use Log;

use App\Jobs\EnvioDeMailJob;


class PatenteController extends Controller
{
    public function getDataByPatent (Request $request){
        Log::channel("getDataByPatent")->info("************************************************************************************************************************");
        Log::channel("getDataByPatent")->info("Inicio Bucar Datos por patente");
        Log::channel("getDataByPatent")->info("Request: ".json_encode($request->all()));

        $aReqData = array();
        $aReqData["request"]   = isset($request->patent) ? $request->patent : "";
        $aReqData["result"]    = false;
        $aReqData["message"]   = "";
        $aReqData["response"]  = [];
        
        if(!isset($request->patent)){
            $aReqData["message"]   = "Sin Patente";
            return response()->json($aReqData, 404);
        }

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
            Log::channel("getDataByPatent")->error("Error en la linea: ".$e->getLine());
            Log::channel("getDataByPatent")->error("Mensaje: ".$e->getMessage());
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

    public function sendPatentByEmail(Request $request){

        Log::channel("sendPatentByEmail")->info("*****************************************************************************************************************************");
        Log::channel("sendPatentByEmail")->info("Inicio Enviar Correo Electronico REST por patente");
        Log::channel("sendPatentByEmail")->info("Request: ".json_encode($request->all()));

        $aReqData = array();
        $aReqData["request"]   = $request->all();
        $aReqData["result"]    = false;
        $aReqData["message"]   = "";

        $rules = [
            "correo"  => 'required|email',
            "patente" => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );

        if($validator->fails()){
            $aReqData["result"]    = false;
            $aReqData["message"]   = $validator->errors()->all()[0];
            Log::channel("sendPatentByEmail")->error($aReqData["message"]);
            Log::channel("sendPatentByEmail")->info("Fin Enviar Correo Electronico REST por patente");
            return response()->json($aReqData, 404);
        }

        try{
            Log::channel("sendPatentByEmail")->info("Patente: ".$request->patente);
            Log::channel("sendPatentByEmail")->info("Correo electronico: ".$request->correo);

            $vehiculo = Vehiculo::where("patente", $request->patente )->first();
    
            if(!isset($vehiculo->patente)){
                $aReqData["message"]   = "La patente ingresada no es valida";
                Log::channel("sendPatentByEmail")->info("La patente ingresada no es valida");
                Log::channel("sendPatentByEmail")->info("Fin Enviar Correo Electronico REST por patente");
                return response()->json($aReqData, 404);
            }
    
            $aReqData["result"]    = true;
            $aReqData["message"]   = "Información Enviada";
    
            
            $contacto = Contacto::firstOrCreate([
                "correo" => $request->correo
            ],[
                "correo" => $request->correo
            ]);
    
            $logEmailSend = new LogEmailSend;
            $logEmailSend->vehiculo_id = $vehiculo->id;
            $logEmailSend->contacto_id = $contacto->id;
            $logEmailSend->estado_id   = 1;
            $logEmailSend->save();
    
            Log::channel("sendPatentByEmail")->info("Correo enviado a Job");
            EnvioDeMailJob::dispatch($vehiculo, $request->correo, $logEmailSend->id);

        }catch(Exception $e){
            $aReqData["message"]   = "Error no controlado";
            Log::channel("sendPatentByEmail")->error("Error en la linea: ".$e->getLine());
            Log::channel("sendPatentByEmail")->error("Mensaje: ".$e->getMessage());
            Log::channel("sendPatentByEmail")->info("Fin Enviar Correo Electronico REST por patente");
            return response()->json($aReqData, 500);
        }

        Log::channel("sendPatentByEmail")->info("Response: ".json_encode($aReqData));
        Log::channel("sendPatentByEmail")->info("Fin Enviar Correo Electronico REST por patente");
        return response()->json($aReqData, 200);
    }
}