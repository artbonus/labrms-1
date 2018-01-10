<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon;
use Validator;
use DB;
use Session;
use App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;

class ItemsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{

			/*
			|--------------------------------------------------------------------------
			|
			| 	input->id contains filter for item to display
			|
			|--------------------------------------------------------------------------
			|
			*/
			if(Input::has('id'))
			{
				/*
				|--------------------------------------------------------------------------
				|
				| 	get id and sanitize to prevent sql injection
				|
				|--------------------------------------------------------------------------
				|
				*/
				$id = $this->sanitizeString(Input::get('id'));
				$status = $this->sanitizeString(Input::get('status'));

				/*
				|--------------------------------------------------------------------------
				|
				| 	if id contains nothing or 'all', return all items
				|
				|--------------------------------------------------------------------------
				|
				*/
				if($id == 'All' || $id == '')
				{
					return json_encode([
						'data' => App\ItemProfile::where('status','=',$status)
												->with('inventory.itemtype')
												->with('receipt')
												->get()
					]);
				}
				else
				{

					/*
					|--------------------------------------------------------------------------
					|
					| 	return specific details of id
					|
					|--------------------------------------------------------------------------
					|
					*/
					$itemtype_id = App\ItemType::type($id)->pluck('id');
					return json_encode([
						'data' => App\ItemProfile::whereIn('inventory_id',App\Inventory::type($itemtype_id)->pluck('id'))
												->where('status','=',$status)
												->with('inventory.itemtype')
												->with('receipt')
												->withTrashed()
												->get()
					]);
				}
			}

			/*
			|--------------------------------------------------------------------------
			|
			| 	display all items
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode([
				'data' => App\ItemProfile::with('inventory.itemtype')
										->with('receipt')
										->get()
			]);
		}

		/*
		|--------------------------------------------------------------------------
		|
		| 	display all items
		|	return view for all items profile
		|
		|--------------------------------------------------------------------------
		|
		*/
		$itemtype = App\ItemType::whereIn('category',['equipment','fixtures','furniture'])->get();
		$status = App\ItemProfile::distinct('status')->pluck('status');
		return view('item.profile')
				->with('status',$status)
				->with('itemtype',$itemtype);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		try
		{
			$id = $this->sanitizeString(Input::get('id'));
			$inventory = App\Inventory::find($id);

			/*
			|--------------------------------------------------------------------------
			|
			| 	if existing, returns the form
			|	else redirect to inventory/item
			|
			|--------------------------------------------------------------------------
			|
			*/
			if(count($inventory) > 0)
			{

				$lastprofiled = App\ItemProfile::where('inventory_id','=',$inventory->id)
											->orderBy('created_at','desc')
											->pluck('propertynumber')
											->first();
				return view('inventory.item.profile.create')
					->with('inventory',$inventory)
					->with('lastprofiled',$lastprofiled)
					->with('id',$id);
			}
			else
			{
				return redirect('inventory/item');
			}

		}
		catch( Exception $e )
		{
			return redirect('inventory/item');
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	initialize items
		|
		|--------------------------------------------------------------------------
		|
		*/
		$inventory_id = $this->sanitizeString(Input::get('inventory_id'));
		$receipt_id = $this->sanitizeString(Input::get('receipt_id'));
		$location = $this->sanitizeString(Input::get('location'));
		$datereceived = $this->sanitizeString(Input::get('datereceived'));
		$propertynumber = "";
		$serialnumber = "";

		/*
		|--------------------------------------------------------------------------
		|
		| 	loops through each items
		|
		|--------------------------------------------------------------------------
		|
		*/
		DB::beginTransaction();
		foreach(Input::get('item') as $item)
		{

			$propertynumber = $this->sanitizeString($item['propertynumber']);
			$serialnumber = $this->sanitizeString($item['serialid']);

			/*
			|--------------------------------------------------------------------------
			|
			| 	validates
			|
			|--------------------------------------------------------------------------
			|
			*/
			$validator = Validator::make([
				'Property Number' => $propertynumber,
				'Serial Number' => $serialnumber,
				'Location' => $location,
				'Date Received' => $datereceived,
				'Status' => 'working'
			],App\Itemprofile::$rules,[ 'Property Number.unique' => "The :attribute $propertynumber already exists" ]);

			if($validator->fails())
			{
				DB::rollback();
				return redirect("item/profile/create?id=$inventory_id")
					->withInput()
					->withErrors($validator);
			}

			$itemprofile = new App\ItemProfile;
			$itemprofile->propertynumber = $propertynumber;
			$itemprofile->serialnumber = $serialnumber;
			$itemprofile->location = $location;
			$itemprofile->datereceived = Carbon\Carbon::parse($datereceived)->toDateString();
			$itemprofile->inventory_id = $inventory_id;
			$itemprofile->receipt_id = $receipt_id;
			$itemprofile->profile();
		}
		DB::commit();

		Session::flash('success-message','Item profiled');
		return redirect('inventory/item');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Check if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{

			/*
			|--------------------------------------------------------------------------
			|
			| 	return itemprofile based on inventory_id
			|
			|--------------------------------------------------------------------------
			|
			*/
		 	return json_encode([
				'data' => App\ItemProfile::with('inventory')
									->where('inventory_id','=',$id)
									->get()
			]);
		}

		/*
		|--------------------------------------------------------------------------
		|
		| 	return view for specific item profile
		|
		|--------------------------------------------------------------------------
		|
		*/
		$inventory = App\Inventory::find($id);
		if($inventory == null || $inventory->count() == 0)
		{

			/*
			|--------------------------------------------------------------------------
			|
			| 	return to inventory tab
			|
			|--------------------------------------------------------------------------
			|
			*/
			return view('errors.404');
		}

		return view('inventory.item.profile.index')
								->with('inventory',$inventory);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$item = App\ItemProfile::find($id);
		return view('inventory.item.profile.edit')
			->with('itemprofile',$item);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$receipt_id = $this->sanitizeString(Input::get('receipt_id'));
		$property_number = $this->sanitizeString(Input::get('propertyid'));
		$serial_number = $this->sanitizeString(Input::get('serialid'));
		$location = $this->sanitizeString(Input::get('location'));
		$datereceived = $this->sanitizeString(Input::get('datereceived'));

		//validator
		$validator = Validator::make([
				'Property Number' => $property_number,
				'Serial Number' => $serial_number,
				'Location' => $location,
				'Date Received' => $datereceived,
				'Status' => 'working',
				'Location' => 'Server'
			],App\ItemProfile::$updateRules);

		if($validator->fails())
		{
			return redirect()->back()
				->withInput()
				->withErrors($validator);
		}

		$itemprofile = App\ItemProfile::find($id);
		$itemprofile->propertynumber = $property_number;
		$itemprofile->serialnumber = $serial_number;
		$itemprofile->receipt_id = $receipt_id;
		$itemprofile->location = $location;
		$itemprofile->datereceived = Carbon\Carbon::parse($datereceived);
		$itemprofile->save();

		Session::flash('success-message','Item updated');

		return redirect('inventory/item');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is ajax or not
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax()){

			/**
			*
			*	Try catch to prevent injection
			*
			*/
			try{

				/**
				*
				*	@param id
				*	@return collection
				*
				*/
				$itemprofile = App\ItemProfile::find($id);

				/*
				|--------------------------------------------------------------------------
				|
				| 	Checks if itemprofile is linked to a pc
				|	return 'connected' if linked
				|
				|--------------------------------------------------------------------------
				|
				*/
				if(count(App\Pc::isPc($itemprofile->propertynumber)) > 0)
				{
					return json_encode('connected');

				}

				/**
				*
				*	Call function condemn
				*
				*/
				App\Inventory::condemn($id);
				return json_encode('success');
			} catch ( Exception $e ) {}
		}

		App\Inventory::condemn($item->inventory_id);
		Session::flash('success-message','Item removed from inventory');
		return redirect('inventory/item');
	}

	/**
	*
	*	Display the ticket
	*	@param $id accepts id of item
	*	@return view
	*
	*/
	public function history($id)
	{
		/**
		*
		*	@param id
		*	@return ticket information
		*	@return inventory
		*	@return itemtype
		*
		*/
		$itemprofile = App\ItemProfile::with('itemticket.ticket')->with('inventory.itemtype')->find($id);
		
		return view('item.history')
				->with('itemprofile',$itemprofile);
	}

	/**
	*
	*	uses get method
	*	@param $item accepts item id
	*	@param $room accepts room name
	*	@return error or page
	*
	*/
	public function assign()
	{
		$item = $this->sanitizeString(Input::get('item'));
		$room = App\Room::location($this->sanitizeString(Input::get('room')))->select('id','name')->first();

		/*
		|--------------------------------------------------------------------------
		|
		| 	Validates input
		|
		|--------------------------------------------------------------------------
		|
		*/
		$validator = Validator::make([
			'Item' => $item,
			'Room' => $room->id
		],App\RoomInventory::$rules);

		if($validator->fails())
		{
			Session::flash('error-message','Error occurred while processing your data');
			return redirect('inventory/item');
		}

		/*
		|--------------------------------------------------------------------------
		|
		| 	Check if connected to a pc
		|
		|--------------------------------------------------------------------------
		|
		*/
		$itemprofile = App\Itemprofile::find($item);
		if(count(App\Pc::isPc($itemprofile->propertynumber)) > 0)
		{
			Session::flash('error-message','This item is used in a workstation. You cannot remove it here. You need to proceed to workstation');
			return redirect("item/profile/$id");

		}

		App\ItemProfile::assignToRoom($item,$room);

		Session::flash('success-message',"Item assigned to room $room->name");
		return redirect()->back();
	}

	/**
	*
	*	get receipt based on inventory
	*	uses ajax request
	*	@param inventory id
	*	@return receipt
	*
	*/
	public function getAllReceipt(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$id = $this->sanitizeString(Input::get('id'));

			/*
			|--------------------------------------------------------------------------
			|
			| 	if id is not valid
			|
			|--------------------------------------------------------------------------
			|
			*/
			if($id == -1)
			{
				return json_encode('error');
			}
			else
			{
				$receipt = App\Receipt::where('inventory_id','=',$id)->select('number','id')->get();
				return $receipt;
			}
		}
	}

	/**
	*
	*	get item brand
	*	uses ajax request
	*	@param itemtype
	*	@return item brand
	*
	*/
	public function getItemBrands(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$itemtype = $this->sanitizeString(Input::get('itemtype'));
			if(count($itemtype) > 0)
			{
				$brands = App\Inventory::where('itemtype_id',$itemtype)->select('brand')->get();
			}
			else
			{
				$brands = App\Inventory::select('brand')->get();
			}


			/*
			|--------------------------------------------------------------------------
			|
			| 	return all brand
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode($brands);
		}
	}

	/**
	*
	*	get item model
	*	uses ajax request
	*	@param brand
	*	@return item model
	*
	*/
	public function getItemModels(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$brand = $this->sanitizeString(Input::get('brand'));
			if(count($brand) > 0)
			{
				$models = App\Inventory::where('brand',$brand)->select('model')->get();
			}
			else
			{
				$models = App\Inventory::select('model')->get();
			}

			/*
			|--------------------------------------------------------------------------
			|
			| 	return all models
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode($models);
		}
	}

	/**
	*
	*	get item brand
	*	uses ajax request
	*	@param itemtype
	*	@return item brand
	*
	*/
	public function getPropertyNumberOnServer(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{

			$model = $this->sanitizeString(Input::get('model'));
			$brand = $this->sanitizeString(Input::get('brand'));
			$itemtype = $this->sanitizeString(Input::get('itemtype'));
			if($model == '' || $brand == '')
			{
				return json_encode('');
			}


			/*
			|--------------------------------------------------------------------------
			|
			| 	get inventory information
			|
			|--------------------------------------------------------------------------
			|
			*/
			$inventory = App\Inventory::where('model',$model)
									->where('brand',$brand)
									->where('itemtype_id',$itemtype)
									->select('id')
									->first();

			/*
			|--------------------------------------------------------------------------
			|
			| 	get property number of item
			|
			|--------------------------------------------------------------------------
			|
			*/
			$propertynumber = App\ItemProfile::where('inventory_id',$inventory->id)
											->where('location','Server')
											->select('propertynumber')
											->get();


			/*
			|--------------------------------------------------------------------------
			|
			| 	if item does not exists
			|
			|--------------------------------------------------------------------------
			|
			*/
			if(count($brand) == 0  && count($itemtype) == 0)
			{
				return json_encode('');
			}

			return json_encode($propertynumber);

		}
	}

	/**
	*
	*	get unassigned system unti
	*	uses ajax request
	*	@return lists of property number
	*
	*/
	public function getUnassignedSystemUnit(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			return App\ItemProfile::getUnassignedPropertyNumber('System Unit');
		}
	}

	/**
	*
	*	get unassigned monitor
	*	uses ajax request
	*	@return lists of property number
	*
	*/
	public function getUnassignedMonitor(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			return App\ItemProfile::getUnassignedPropertyNumber('Display');
		}
	}

	/**
	*
	*	get unassigned avr
	*	uses ajax request
	*	@return lists of property number
	*
	*/
	public function getUnassignedAVR(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			return App\ItemProfile::getUnassignedPropertyNumber('AVR');
		}
	}

	/**
	*
	*	get unassigned keyboard
	*	uses ajax request
	*	@return lists of property number
	*
	*/
	public function getUnassignedKeyboard(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			return App\ItemProfile::getUnassignedPropertyNumber('Keyboard');
		}
	}

	/**
	*
	*	get all propertynumber
	*	uses ajax request
	*	@return lists of property number
	*
	*/
	public function getAllPropertyNumber(){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			return json_encode(App\Itemprofile::pluck('propertynumber'));
		}
	}

	/**
	*
	*	get item information
	*	uses ajax request
	*	@param propertynumber
	*	@return item information
	*
	*/
	public function getStatus($propertynumber){

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			try{
				$item = App\ItemProfile::with('inventory.itemtype')
										->propertyNumber($propertynumber)
										->first();

				/*
				|--------------------------------------------------------------------------
				|
				| 	check if item exists
				|
				|--------------------------------------------------------------------------
				|
				*/
				if(count($item) > 0)
				{
					return json_encode($item);
				}
				else
				{
					return json_encode('error');
				}

			}
			catch ( Exception $e )
			{
				return json_encode('error');
			}
		}
	}

	/**
	*
	*	get list of monitor
	*	uses ajax request
	*	@param propertynumber
	*	@return lists of property number
	*
	*/
	public function getMonitorList()
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$monitor = $this->sanitizeString(Input::get('term'));

			/*
			|--------------------------------------------------------------------------
			|
			| 	get lists of unassembled monitor
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode(
				App\ItemProfile::unassembled()
							->whereHas('inventory',function($query){
								$query->whereHas('itemtype',function($query){
									$query->where('name','=','Monitor');
								})->orWhereHas('itemsubtype',function($query){
									$query->where('name','=','Monitor');
								});
							})
							->where('local_id','like','%'.$monitor.'%')
							->pluck('local_id')
			);
		}
	}

	/**
	*
	*	get list of keyboard
	*	uses ajax request
	*	@param propertynumber
	*	@return lists of property number
	*
	*/
	public function getKeyboardList()
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$keyboard = $this->sanitizeString(Input::get('term'));

			/*
			|--------------------------------------------------------------------------
			|
			| 	get keyboard not in pc
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode(
				App\ItemProfile::unassembled()
							->whereHas('inventory',function($query){
								$query->whereHas('itemtype',function($query){
									$query->where('name','=','Keyboard');
								})->orWhereHas('itemsubtype',function($query){
									$query->where('name','=','Keyboard');
								});
							})
							->where('local_id','like','%'.$keyboard.'%')
							->pluck('local_id')
			);
		}
	}

	/**
	*
	*	get list of avr
	*	uses ajax request
	*	@param propertynumber
	*	@return lists of property number
	*
	*/
	public function getAVRList()
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$avr = $this->sanitizeString(Input::get('term'));

			/*
			|--------------------------------------------------------------------------
			|
			| 	get avr not in pc
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode(
				App\ItemProfile::unassembled()
							->whereHas('inventory',function($query){
								$query->whereHas('itemtype',function($query){
									$query->where('name','=','AVR');
								})->orWhereHas('itemsubtype',function($query){
									$query->where('name','=','AVR');
								});
							})
							->where('local_id','like','%'.$avr.'%')
							->pluck('local_id')
			);
		}
	}

	/**
	*
	*	get list of system unit
	*	uses ajax request
	*	@param propertynumber
	*	@return lists of property number
	*
	*/
	public function getSystemUnitList()
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$systemunit = $this->sanitizeString(Input::get('term'));

			/*
			|--------------------------------------------------------------------------
			|
			| 	get system unit not in pc
			|
			|--------------------------------------------------------------------------
			|
			*/
			return json_encode(
				App\Itemprofile::unassembled()
							->whereHas('inventory',function($query){
								$query->whereHas('itemtype',function($query){
									$query->where('name','=','System Unit');
								})->orWhereHas('itemsubtype',function($query){
									$query->where('name','=','System Unit');
								});
							})
							->where('local_id','like','%'.$systemunit.'%')
							->pluck('local_id')
			);
		}
	}

	/**
	*
	*	chec if inventory is existing
	*	uses ajax request
	*	@param item type
	*	@param brand
	*	@param model
	*	@return inventory information
	*
	*/
	public function checkifexisting($itemtype,$brand,$model)
	{
		$itemtype = $this->sanitizeString($itemtype);
		$brand = $this->sanitizeString($brand);
		$model = $this->sanitizeString($model);

		$itemtype = App\ItemType::type($itemtype)->pluck('id')->first();
		/*
		|--------------------------------------------------------------------------
		|
		| 	get inventory information
		|
		|--------------------------------------------------------------------------
		|
		*/
		$inventory = App\Inventory::brand($brand)
								->model($model)
								->type($itemtype)
								->first();

		if(count($inventory) > 0)
		{
			return json_encode($inventory);
		}
		else
		{
			return json_encode('error');
		}
	}

	public function getItemInformation($propertynumber)
	{

		/*
		|--------------------------------------------------------------------------
		|
		| 	Checks if request is made through ajax
		|
		|--------------------------------------------------------------------------
		|
		*/
		if(Request::ajax())
		{
			$propertynumber = $this->sanitizeString($propertynumber);
			/*
			|--------------------------------------------------------------------------
			|
			| 	check if item is linked to pc
			|
			|--------------------------------------------------------------------------
			|
			*/
			$item = App\Pc::isPc($propertynumber);

			/*
			|--------------------------------------------------------------------------
			|
			| 	if not linked to pc, get the item profile information
			|
			|--------------------------------------------------------------------------
			|
			*/
			if(is_null($item) || $item == null)
			{
				$item = App\ItemProfile::propertyNumber($propertynumber)->first();
			}
			else
			{
				$item = App\Pc::with('systemunit')
							->with('keyboard')
							->with('avr')
							->with('monitor')
							->find($item->id);
			}

			if(count($item) == 0)
			{
				return json_encode('error');
			}

			return json_encode($item);
		}
	}


}
