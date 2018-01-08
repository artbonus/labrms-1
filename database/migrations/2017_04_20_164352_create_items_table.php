<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('local_id')->unique();
			$table->integer('inventory_id')->unsigned();
			$table->foreign('inventory_id')
					->references('id')
					->on('inventories')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->integer('receipt_id')->unsigned()->nullable();
			$table->foreign('receipt_id')
					->references('id')
					->on('receipts')
					->onUpdate('cascade')
					->onDelete('cascade');
			$table->string('property_number',100)->unique()->nullable();
			$table->string('serial_number',100)->unique()->nullable();
			$table->string('location',100)->nullable();
			$table->date('date_received')->nullable();
            $table->string('profiled_by')->nullable();
			$table->string('warranty',100)->nullable();
            $table->dateTime('lent_at')->nullable();
            $table->string('lent_by')->nullable();
            $table->dateTime('deployed_at')->nullable();
            $table->string('deployed_by')->nullable();
			$table->string('status')->default('working')->nullable();
			$table->boolean('for_reservation')->default(0);
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
		Schema::drop('items');
	}

}
