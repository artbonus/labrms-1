<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePcSoftwareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pc_software', function(Blueprint $table)
		{
			$table->integer('pc_id')->unsigned();
			$table->foreign('pc_id')
					->references('id')
					->on('pc')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('softwarelicense_id')->unsigned()->nullable();
			$table->foreign('softwarelicense_id')
					->references('id')
					->on('software_licenses')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->primary(['pc_id', 'softwarelicense_id']);
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
		Schema::drop('pc_software');
	}

}
