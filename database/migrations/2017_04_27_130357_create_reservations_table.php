<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function(Blueprint $table)
		{	
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')
					->references('id')
					->on('users')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->datetime('time_start');
			$table->datetime('time_end');
			$table->string('purpose',100);
			$table->string('location',100);
			$table->boolean('approval');
			$table->integer('faculty')->unsigned();
			$table->foreign('user_id')
					->references('id')
					->on('users')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->string('remarks',100)->nullable();
            $table->string('status')->nullable()->default('unclaimed');
			$table->string('created_by')->nullable();
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
		Schema::drop('reservations');
	}

}
