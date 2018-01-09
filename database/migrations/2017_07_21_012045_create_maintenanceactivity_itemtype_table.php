<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceactivityItemtypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('maintenanceactivity_itemtype', function(Blueprint $table)
		{
			$table->string('maintenanceactivity_id')
					->references('id')
					->on('maintenanceactivities')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->string('itemtype_id')
					->references('id')
					->on('itemtype')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->primary(['maintenanceactivity_id', 'itemtype_id']);
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
		Schema::drop('maintenanceactivity_itemtype');
	}

}
