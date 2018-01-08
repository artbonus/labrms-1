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
			$table->integer('software_id')->unsigned();
			$table->foreign('software_id')
					->references('id')
					->on('software')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('softwarelicense_id')->unsigned()->nullable();
			$table->foreign('softwarelicense_id')
					->references('id')
					->on('softwarelicense')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->primary(['pc_id', 'software_id']);
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
