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
			$table->integer('maintenanceactivity_id')->unsigned();
			$table->foreign('maintenanceactivity_id')
					->references('id')
					->on('maintenance_activities')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('itemtype_id')->unsigned();
			$table->foreign('itemtype_id')
					->references('id')
					->on('item_types')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->primary(['maintenanceactivity_id', 'itemtype_id'], 'maintenanceactivity_itemtype_pk');
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
