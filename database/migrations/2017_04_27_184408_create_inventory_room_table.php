<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryRoomTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inventory_room', function(Blueprint $table)
		{
			$table->integer('room_id')->unsigned();
			$table->foreign('room_id')
					->references('id')
					->on('rooms')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('item_id')->unsigned();
			$table->foreign('item_id')
					->references('id')
					->on('items')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->primary([ 'room_id', 'item_id' ]);
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
		Schema::drop('inventory_room');
	}

}
