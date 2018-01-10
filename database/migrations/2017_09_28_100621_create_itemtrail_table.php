<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemtrailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')
                    ->references('id')
                    ->on('items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('oldlocation',100);
            $table->string('newlocation',100);
            $table->datetime('timetransfered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_transfer');
    }
}
