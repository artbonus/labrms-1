<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoftwaresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('softwares', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name',100);
			$table->string('type',100);
			$table->string('license_type',100);
			$table->string('company',100);
			$table->string('minimum_requirements',100)->nullable();
			$table->string('recommended_requirements',100)->nullable();
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
		Schema::drop('softwares');
	}

}
