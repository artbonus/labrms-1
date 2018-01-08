<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title',40)->unique();
			$table->string('details')->nullable();
			$table->datetime('date_occurred');

			/**
			 * weekly, daily, monthly, annually
			 * an event can be repeated several times
			 * but some event are good for one use only
			 */
			$table->string('repeating')->nullable();
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
		Schema::drop('events');
	}

}
