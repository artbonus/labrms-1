<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inventories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 10)->nullable();
			$table->integer('itemtype_id')->unsigned();
			$table->foreign('itemtype_id')
					->references('id')
					->on('item_types')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('itemsubtype_id')->unsigned()->nullable();
			$table->foreign('itemsubtype_id')
					->references('id')
					->on('item_subtypes')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->string('brand', 100)->nullable();
			$table->string('model', 100)->nullable();
			$table->longtext('details')->nullable();
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
		Schema::drop('inventories');
	}

}
