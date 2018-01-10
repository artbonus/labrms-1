<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maintenance_activities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type');
			$table->string('activity');
			$table->string('details')->nullable();
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
		Schema::drop('maintenance_activities');
	}

}
