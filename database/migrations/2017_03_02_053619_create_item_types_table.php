<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name',50)->unique();
			$table->string('description',450)->nullable();
			$table->string('category',450)->nullable();
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
		Schema::drop('item_types');
	}

}
