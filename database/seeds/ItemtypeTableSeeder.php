<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Itemtype;

class ItemtypeTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Itemtype::truncate();
		//delete users table records
		DB::table('item_types')->delete();
		//insert some dummy records
		Itemtype::insert(array(
			[
			   'name' => 'System Unit',
			   'description' => 'Computer set',
			   'category' => 'equipment'
			],
			[
			 'name' => 'Display',
			 'description' => 'Visual aids',
			 'category' => 'equipment'
			],
			[
			 'name' => 'AVR',
			 'description' => 'Power Regulator',
			 'category' => 'equipment'
			],
			[
			 'name' => 'Aircon',
			 'description' => 'Cooling appliance',
			 'category' => 'equipment'
			],
			[
			 'name' => 'Projector',
			 'description' => 'Visual aids',
			 'category' => 'equipment'
			],
			[
			 'name' => 'Extension',
			 'description' => 'Extension cord or any other power source',
			 'category' => 'supply'
			],

		));
		
	}
}
