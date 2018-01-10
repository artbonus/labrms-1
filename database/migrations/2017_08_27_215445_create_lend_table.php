<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lend', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->integer('lent_to')->unsigned();
            $table->foreign('lent_to')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('course',100)->nullable();
            $table->string('faculty',100)->nullable();
            $table->string('location');
            $table->datetime('timein');
            $table->datetime('timeout')->nullable();
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')
                    ->references('id')
                    ->on('items')
                    ->onUpdate('cascade')
                    ->onUpdate('cascade');
            $table->integer('created_by')
                    ->foreign('created_by')
                    ->references('id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('lend');
    }
}
