<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username',50)->unique();
			$table->string('password',254);
			$table->integer('accesslevel');
			$table->string('firstname',100);
			$table->string('middlename',50)->nullable();
			$table->string('lastname',50);
			$table->string('contactnumber',50);
			$table->string('email',100);
			$table->string('type',50);
			$table->boolean('status');
			$table->timestamps();
			$table->rememberToken();
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
		Schema::drop('users');
	}

}
