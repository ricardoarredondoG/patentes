<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('patente');
            $table->string('tipo_vehiculo');
            $table->string('marca_vehiculo');
            $table->string('modelo_vehiculo');
            $table->string('rut_propietario');
            $table->string('numero_motor_vehiculo');
            $table->integer('anio_vehiculo');
            $table->string('nombre_propietario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehiculos');
    }
}
