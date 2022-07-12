<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarDatosPatente extends Mailable
{
    use Queueable, SerializesModels;
    public $vehiculo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vehiculo)
    {
        $this->vehiculo = $vehiculo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.infoPatente ');
    }
}
