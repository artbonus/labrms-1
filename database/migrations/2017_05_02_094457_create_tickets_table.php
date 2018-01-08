<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tickets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tickettype',100);
			$table->string('ticketname',100);
			$table->string('details',500);
			$table->string('author',100);
			$table->integer('staff_assigned')->unsigned()->nullable();
			$table->foreign('staff_assigned')
					->references('id')
					->on('users')
					->onDelete('cascade')
					->onUpdate('cascade');
			$table->integer('created_by')->unsigned();
			$table->foreign('created_by')
					->references('id')
					->on('users')
					->onDelete('cascade')
					->onUpdate('cascade');
			$table->integer('ticket_id')->unsigned()->nullable();
			$table->foreign('ticket_id')
					->references('id')
					->on('ticket')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->string('comments')->nullable();
	        $table->string('closed_by',254)->nullable();
	        $table->string('validated_by',254)->nullable();
	        $table->datetime('deadline')->nullable();
	        $table->boolean('trashable')->nullable();

	        /**
	         * severity of ticket from 1 - 100
	         * the lesser the number, the lesser the severity
	         * of ticket
	         */
	        $table->integer('severity')->default(1);
	        $table->string('nature')->nullable();
			$table->string('status');
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
		Schema::drop('tickets');
	}

}
