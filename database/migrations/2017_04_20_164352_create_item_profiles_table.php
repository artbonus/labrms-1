<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_profiles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('local_id',15)->unique();
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
			$table->string('property_number', 100)->unique()->nullable();
			$table->string('serial_number',100)->nullable();
			$table->string('location', 100)->nullable();
			$table->date('date_received')->nullable();
            $table->string('profiled_by')->nullable();
			$table->string('warranty_details', 100)->nullable();
            $table->dateTime('lent_at')->nullable();
            $table->integer('lent_by')->nullable()->unsigned();
            $table->foreign('lent_by')
            		->references('id')
            		->on('users')
            		->onDelete('cascade')
            		->onUpdate('cascade');
            $table->dateTime('deployed_at')->nullable();
            $table->integer('deployed_by')->nullable()->unsigned();
            $table->foreign('deployed_by')
            		->references('id')
            		->on('users')
            		->onDelete('cascade')
            		->onUpdate('cascade');
			$table->string('status', 20)->default('working')->nullable();
			$table->boolean('for_reservation')->default(0);
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')
					->references('id')
					->on('users')
					->onUpdate('cascade')
					->onDelete('cascade');
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
		Schema::drop('item_profiles');
	}

}
