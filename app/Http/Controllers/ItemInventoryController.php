<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App;
use Session;
use Validator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;

class ItemInventoryController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Request::ajax())
		{
			return json_encode([
					'data' => App\Inventory::with('itemtype')
									->get()
					]);
		}

		return view('inventory.item.index')
						->with('title','Inventory');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$brand = null;
		$model = null;
		$itemtype = null;
		$itemsubtype = null;

		$itemsubtypes = App\ItemSubType::pluck('name','id');

		$itemtypes = App\ItemType::pluck('name','id');

		$units = App\Unit::pluck('abbreviation','abbreviation');

		if(Input::has('brand'))
		{
			$brand = $this->sanitizeString(Input::get('brand'));
		}

		if(Input::has('model'))
		{
			$model = $this->sanitizeString(Input::get('model'));
		}

		if(Input::has('itemtype'))
		{
			$itemtype = $this->sanitizeString(Input::get('itemtype'));
		}

		if(Input::has('itemsubtype'))
		{
			$itemsubtype = $this->sanitizeString(Input::get('itemsubtype'));
		}

		$itemtypes = App\ItemType::category('equipment')->pluck('name','name');
		$receipts = App\Receipt::pluck('number', 'id');

		return view('inventory.item.create')
					->with('itemtypes',$itemtypes)
					->with('brand',$brand)
					->with('model',$model)
					->with('itemtype',$itemtype)
					->with('itemsubtype',$itemsubtype)
					->with('itemtypes',$itemtypes)
					->with("units",$units)
					->with('itemsubtypes',$itemsubtypes)
					->with('receipts', $receipts)
					->with('title','Inventory::add');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//receipt
		$number = $this->sanitizeString(Input::get('number'));
		$ponumber = $this->sanitizeString(Input::get('ponumber'));
		$podate = $this->sanitizeString(Input::get('podate'));
		$invoicedate = $this->sanitizeString(Input::get('invoicedate'));
		$invoicenumber = $this->sanitizeString(Input::get('invoicenumber'));
		$fundcode = $this->sanitizeString(Input::get('fundcode'));
		$receipt = $this->sanitizeString(Input::get('receipt'));

		if(!(isset($receipt) && Input::has('receipt')) && $receipt == '')
		{
			$validator = Validator::make([
					'Property Acknowledgement Receipt' => $number,
					'Purchase Order Number' => $ponumber,
					'Purchase Order Date' => $podate,
					'Invoice Number' => $invoicenumber,
					'Invoice Date' => $invoicedate,
					'Fund Code' => $fundcode
				],App\Receipt::$rules);

			if($validator->fails())
			{
				return redirect('inventory/item/create')
					->withInput()
					->withErrors($validator);
			}
		}

		//inventory
		$brand = $this->sanitizeString(Input::get('brand'));
		$itemtype = $this->sanitizeString(Input::get('itemtype'));
		$itemsubtype = $this->sanitizeString(Input::get('itemsubtype'));
		$model = $this->sanitizeString(Input::get('model'));
		$quantity = $this->sanitizeString(Input::get('quantity'));
		$unit = $this->sanitizeString(Input::get('unit'));
		$details = $this->sanitizeString(Input::get('details'));

		//validator
		$validator = Validator::make([
				'Item Type' => $itemtype,
				'Brand' => $brand,
				'Model' => $model,
				'Details' => $details,
				'Unit' => $unit,
				'Quantity' => $quantity,
				'Profiled Items' => 0
			],App\Inventory::$rules);

		if($validator->fails())
		{
			return redirect('inventory/item/create')
				->withInput()
				->withErrors($validator);
		}

		$itemtype = App\ItemType::type($itemtype)->pluck('id')->first();

		$inventory = App\Inventory::createRecord([
			'brand' => $brand,
			'itemtype' => $itemtype,
			'itemsubtype' => $itemsubtype,
			'model' => $model,
			'quantity' => $quantity,
			'unit' => $unit,
			'details' => $details
		],[
			'number' => $number,
			'ponumber' => $ponumber,
			'podate' => $podate,
			'invoicenumber' => $invoicenumber,
			'invoicedate' => $invoicedate,
			'fundcode' => $fundcode,
			'receipt' => $receipt
		]);

		Session::flash('success','Items added to Inventory');

		if(Input::has('redirect-profiling'))
		{
			return redirect("item/profile/create?id=$inventory->id");
		}

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

		if($id == 'search')
		{
			return $this->searchView();
		}


		if(Request::ajax())
		{
			return json_encode(
				App\Inventory::where('id','=',$id)
								->with('itemtype')
								->first()
			);
		}

		return view('inventory.item.show');
	}

	public function edit($id)
	{
		try
		{
			$inventory = App\Inventory::find($id);
			return view('inventory.item.edit')
					->with('inventory',$inventory);
		} catch ( Exception $e ) {
			Session::flash('success-message','Problems occur while sending your data to the server');
			return redirect('inventory/item');
		}
	}

	public function update($id)
	{

		//inventory
		$brand = $this->sanitizeString(Input::get('brand'));
		$itemtype = $this->sanitizeString(Input::get('itemtype'));
		$model = $this->sanitizeString(Input::get('model'));
		$unit = $this->sanitizeString(Input::get('unit'));
		$warranty = $this->sanitizeString(Input::get('warranty'));
		$details = $this->sanitizeString(Input::get('details'));

		//validator
		$validator = Validator::make([
				'Item Type' => $itemtype,
				'Brand' => $brand,
				'Model' => $model,
				'Details' => $details,
				'Warranty' => $warranty,
				'Unit' => $unit,
				'Quantity' => 0,
				'Profiled Items' => 0
			],App\Inventory::$rules);

		if($validator->fails())
		{
			return redirect("inventory/item/$id/edit")
				->withInput()
				->withErrors($validator);
		}

		try {

			$inventory = App\Inventory::find($id);
			$inventory->brand = $brand;
			$inventory->model = $model;
			$inventory->itemtype_id = $itemtype;
			$inventory->details = $details;
			$inventory->warranty = $warranty;
			$inventory->unit = $unit;
			$inventory->save();
		} catch(Exception $e) {
			Session::flash('error-message','Unknown Error Encountered');
			return redirect('inventory/item');
		}

		Session::flash('success-message','Inventory content updated');
		return redirect('inventory/item');

	}

	public function importView()
	{
		return view('inventory.item.import');
	}

	public function import()
	{
		$file = Input::file('file');
		// $filename = str_random(12);
		//$filename = $file->getClientOriginalName();
		//$extension =$file->getClientOriginalExtension();
		$filename = 'inventory.'.$file->getClientOriginalExtension();
		$destinationPath = public_path() . '\files';
		$file->move($destinationPath, $filename);

		$excel = Excel::load($destinationPath . "/" . $filename, function($reader) {

		    // reader methods

		})->get();


		return $excel;
		Session::flash('success-message','Items Imported to Inventory');
		return redirect('inventory/item/view/import');
	}

	public function getBrands()
	{
		if(Request::ajax())
		{
			$brand = $this->sanitizeString(Input::get('term'));
			return json_encode(
				App\Inventory::where('brand','like','%'.$brand.'%')->distinct()->pluck('brand')
			);
		}
	}

	public function getModels()
	{
		if(Request::ajax())
		{
			$model = $this->sanitizeString(Input::get('term'));
			return json_encode(
				App\Inventory::where('model','like','%'.$model.'%')->distinct()->pluck('model')
			);
		}
	}

	public function searchView()
	{
		$brand = App\Inventory::distinct('brand')->pluck('brand','brand');
		$model = App\Inventory::distinct('brand')->pluck('model','model');
		$itemtype = App\ItemType::distinct()->pluck('name','name');
		return view('inventory.item.search')
					->with('brand',$brand)
					->with('model',$model)
					->with('itemtype',$itemtype)
					->with('inventory',[]);
	}

	public function search()
	{
		// return Input::all();
		$keyword = $this->sanitizeString(Input::get('keyword'));
		$total = $this->sanitizeString(Input::get('total'));
		$brand = $this->sanitizeString(Input::get('brand'));
		$model = $this->sanitizeString(Input::get('model'));
		$itemtype = $this->sanitizeString(Input::get('itemtype'));
		$profiled = $this->sanitizeString(Input::get('profiled'));

		$inventory = new App\Inventory;

		if($this->hasData($keyword))
		{
			$inventory = $inventory->where(function($query) use ($keyword){
				$query->where('brand','like','%'.$keyword.'%')
						->orWhere('model','like','%'.$keyword.'%')
						->orWhere('details','like','%'.$keyword.'%')
						->orWhere('warranty','like','%'.$keyword.'%')
						->orWhere('unit','like','%'.$keyword.'%')
						->orWhere('quantity','like','%'.$keyword.'%')
						->orWhere('profileditems','like','%'.$keyword.'%');
			});
		}

		if(Input::get('include-total') == 'on')
		{
			$inventory = $inventory->where('quantity','like','%'.$total.'%');
		}

		if(Input::get('include-profiled') == 'on')
		{
			$inventory = $inventory->where('quantity','like','%'.$profiled.'%');
		}

		if(Input::get('include-brand') == 'on')
		{
			$inventory = $inventory->where('quantity','like','%'.$brand.'%');
		}

		if(Input::get('include-model') == 'on')
		{
			$inventory = $inventory->where('quantity','like','%'.$model.'%');
		}

		if(Input::get('include-itemtype') == 'on')
		{
			$inventory = $inventory->where('quantity','like','%'.$itemtype.'%');
		}

		$count = $inventory->count();

		Session::flash('success-message',"Search Result: $count");

		$brand = App\Inventory::distinct('brand')->pluck('brand','brand');
		$model = App\Inventory::distinct('brand')->pluck('model','model');
		$itemtype = App\ItemType::distinct()->pluck('name','name');
		return view('inventory.item.search')
					->with('brand',$brand)
					->with('model',$model)
					->with('itemtype',$itemtype)
					->with('inventory',$inventory->get());

	}
}
