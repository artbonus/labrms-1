<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomSoftwareTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('room_software', function(Blueprint $table)
		{
			$table->integer('room_id')->unsigned();
			$table->foreign('room_id')
					->references('id')
					->on('rooms');
			$table->integer('software_id')->unsigned();
			$table->foreign('software_id')
					->references('id')
					->on('softwares')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->primary(array('room_id','software_id'));
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
		Schema::drop('room_software');
	}

}
