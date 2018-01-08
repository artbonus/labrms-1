<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsubtypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itemsubtypes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('itemtype_id')->unsigned();
            $table->foreign('itemtype_id')
                    ->references('id')
                    ->on('itemtype')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('details')->nullable();
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
        Schema::dropIfExists('itemsubtypes');
    }
}
