<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('item_id')->unsigned();
			$table->foreign('item_id')
					->references('id')
					->on('items');
			$table->string('receipt_number',100);
			$table->string('received_by',100);
			$table->integer('user_id')->unsigned()->nullable();
			$table->foreign('user_id')
					->references('id')
					->on('users')
					->onDelete('set null')
					->onUpdate('cascade');
			$table->string('details',100);
			$table->decimal('total_payment', 8, 2);
			$table->decimal('amount_paid', 8, 2);
			$table->string('status');
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
		Schema::drop('payments');
	}

}
