<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('receipts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('number',25);
			$table->string('purchaseorder_number',25)->nullable();
			$table->date('purchaseorder_date')->nullable();
			$table->string('invoice_number',25)->nullable();
			$table->date('invoice_date')->nullable();
			$table->string('fund_code',25)->nullable();
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
		Schema::drop('receipts');
	}

}
