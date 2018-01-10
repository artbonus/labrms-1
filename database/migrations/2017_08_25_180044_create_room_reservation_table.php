<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_reservation', function (Blueprint $table) {
            $table->integer('reservation_id')->unsigned();
            $table->foreign('reservation_id')
                    ->references('id')
                    ->on('reservations')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('room_id')->unsigned();
            $table->foreign('room_id')
                    ->references('id')
                    ->on('rooms')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->primary([ 'reservation_id', 'room_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_reservation');
    }
}
