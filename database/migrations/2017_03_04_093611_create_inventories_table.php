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
			$table->integer('itemtype_id')->unsigned();
			$table->foreign('itemtype_id')
					->references('id')
					->on('itemtype')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('itemsubtype_id')->unsigned()->nullable();
			$table->foreign('itemsubtype_id')
					->references('id')
					->on('itemsubtype')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->string('brand',100)->nullable();
			$table->string('model',100)->nullable();
			$table->string('details',1000)->nullable();
			$table->string('unit',20);
			$table->integer('quantity');
			$table->integer('profiled_items');
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
