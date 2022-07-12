<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\EnviarDatosPatente;

use Log;
use Exception;

use App\Models\LogEmailSend;

class EnvioDeMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $correo, $vehiculo, $logEmailSend;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($vehiculo, $correo, $logEmailSend)
    {
        $this->vehiculo      = $vehiculo;
        $this->correo        = $correo;
        $this->logEmailSend  = $logEmailSend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel("envioDeMailJob")->info("**************************************************************************************************");
        Log::channel("envioDeMailJob")->info("Incio Job EnvioDeMailJob");

        try{
            Log::channel("envioDeMailJob")->info("Correo: ". $this->correo );
            Log::channel("envioDeMailJob")->info("Vehiculo: ". json_encode($this->vehiculo) );
            Log::channel("envioDeMailJob")->info("Log: ". $this->logEmailSend);
            
            Mail::to($this->correo)->send(new EnviarDatosPatente($this->vehiculo));
            $logEmailSend = LogEmailSend::find($this->logEmailSend);
            $logEmailSend->estado_id = 3;
            $logEmailSend->save();
            
        }catch(Exception $e){
            Log::channel("envioDeMailJob")->error("Error en la linea: ".$request->getLine());
            Log::channel("envioDeMailJob")->error("Mensaje: ".$request->getMessage());
            Log::channel("envioDeMailJob")->info("Fin Job EnvioDeMailJob");
        }

        Log::channel("envioDeMailJob")->info("Fin Job EnvioDeMailJob");
    }
}
