<?php

namespace App;

use Carbon\Carbon;
use Auth;
use DB;
use App\ItemProfile;
use App\Pc;
use App\Ticket;
use App\Inventory;
use Illuminate\Database\Eloquent\Model;

class Pc extends \Eloquent{
	//Database driver
	/*
		1 - Eloquent (MVC Driven)
		2 - DB (Directly query to SQL database, no model required)
	*/
	//The table in the database used by the model.
	protected $table = 'pc';
	protected $primaryKey = 'id';
	public $timestamps = false;
	public $fillable = ['oskey','mouse','keyboard_id','systemunit_id','monitor_id','avr_id'];
	//Validation rules!
	public static $rules = array(
		'Operating System Key' => 'min:2|max:50|unique:pc,oskey',
		'Workstation Name' => '',
		'avr' => 'exists:itemprofile,propertynumber',
		'Monitor' => 'exists:itemprofile,propertynumber',
		'System Unit' => 'required|exists:itemprofile,propertynumber',
		'Keyboard' => 'exists:itemprofile,propertynumber'
	);

	public static $updateRules = array(
		'Operating System Key' => 'min:2|max:50',
	);

	public function roominventory()
	{
		return $this->hasOne('App\RoomInventory','room_id','systemunit_id');
	}

	public function systemunit()
	{
		return $this->belongsTo('App\ItemProfile','systemunit_id','id');
	}

	public function monitor()
	{
		return $this->belongsTo('App\ItemProfile','monitor_id','id');
	}
	public function keyboard()
	{
		return $this->belongsTo('App\ItemProfile','keyboard_id','id');
	}

	public function avr()
	{
		return $this->belongsTo('App\ItemProfile','avr_id','id');
	}

	public function software()
	{
		return $this->belongsToMany('App\Software','pc_software','pc_id','software_id')
					->withPivot('softwarelicense_id')
					->withTimestamps();
	}

	public function ticket()
	{
		return $this->belongsToMany('App\Ticket','pc_ticket','pc_id','ticket_id');
	}

	public function scopeName($query,$value)
	{
		return $query->where('name','=',$value);
	}

    public static function separateArray($value)
    {
        return explode(',', $value);
    }

    public static function assemble($name,$systemunit,$monitor,$avr,$keyboard,$oskey,$mouse)
    {
		$_systemunit = ItemProfile::propertyNumber($systemunit)->first();
		$_monitor = ItemProfile::propertyNumber($monitor)->first();
		$_avr = ItemProfile::propertyNumber($avr)->first();
		$_keyboard = ItemProfile::propertyNumber($keyboard)->first();

		/*
		*
		*	Get the id of the object
		*	Assign to the variable
		*
		*/
		$systemunit = Pc::getID($_systemunit);
		$monitor =Pc::getID($_monitor);
		$avr = Pc::getID($_avr);
		$keyboard = Pc::getID($_keyboard);

		/*
		*
		*	Transaction used to prevent error on saving
		*
		*/
		DB::beginTransaction();

		Supply::releaseForWorkstation($mouse);
		/*
		*
		*	Create a new pc record
		*	All validation must occur before this point
		*	No more validation at this point
		*
		*/
		$pc = new Pc;
		$pc->systemunit_id = $systemunit;
		$pc->monitor_id = $monitor;
		$pc->avr_id = $avr;
		$pc->keyboard_id = $keyboard;
		$pc->oskey = $oskey;
		$pc->mouse = $mouse;
		$pc->name = $name;
		$pc->save();

		$details = "";

		/*
		*
		*	Create a workstation ticket
		*	The current person who assembles the workstation will receive the ticket
		*	Details are autogenerated by the system
		*
		*/
		if(isset($name))
		{
			$details = 'Workstation ' . $name . ' assembled with the following propertynumber:';			
		}
		else
		{
			$details = 'Workstation assembled with the following propertynumber:';
		}

		if(isset($_systemunit->propertynumber))
		{

			$details = $details . $_systemunit->propertynumber . ' for System Unit. ' ;
		}

		if(isset($_monitor->propertynumber))
		{
			$details = $details . $_monitor->propertynumber . ' for Monitor. ';
		}

		if(isset($_keyboard->propertynumber))
		{
			$details = $details . $_keyboard->propertynumber . ' for Keyboard. ';
		}

		if(isset($_avr->propertynumber))
		{
			$details = $details . $_avr->propertynumber . ' for AVR. ';
		}

		if(isset($mouse))
		{
			$details = $details . $mouse . ' for mouse. ';
		}

		$ticketname = 'Workstation Assembly';
		$staffassigned = Auth::user()->id;
		$author = Auth::user()->firstname . " " . Auth::user()->middlename . " " . Auth::user()->lastname;
		Ticket::generatePcTicket($pc->id,'Receive',$ticketname,$details,$author,$staffassigned,null,'Closed');
		DB::commit();
    }

    public static function condemn($id,$systemunit,$monitor,$keyboard,$avr)
    {

    	$pc = Pc::find($id);

    	if($systemunit)
    	{
    		if(isset($pc->systemunit_id))
    		{	
    			Inventory::condemn($pc->systemunit_id);
    		}	
    	}

    	if($monitor)
    	{
    		if(isset($pc->monitor_id))
    		{
    			Inventory::condemn($pc->monitor_id);
    		}
    	}

    	if($keyboard)
    	{
    		if(isset($pc->keyboard_id))
    		{
    			Inventory::condemn($pc->keyboard_id);
    		}
    	}

    	if($avr)
    	{
    		if(isset($pc->avr_id))
    		{
    			Inventory::condemn($pc->avr_id);
    		}
    	}

		$ticketname = 'Workstation Condemn';
		$staffassigned = Auth::user()->id;
		$author = Auth::user()->firstname . " " . Auth::user()->middlename . " " . Auth::user()->lastname;
    	$details = `Workstation condemned on` . Carbon::now()->toDayDateTimeString() . 'by ' . $author;
    	Ticket::generatePcTicket($pc->id,'condemn',$ticketname,$details,$author,$staffassigned,null,'Closed');
    	$pc->delete();
    }

    /**
    *
    *	@param $object accepts object collection
    *	get the id from object
    *	returns null if no id
    *
    */
    public static function getID($object)
    {
    	if(isset($object->id))
    	{
    		$object = $object->id;
    		return $object;
    	}

		return null;
    }

    /**
    *
    *	@param $propertynumber of item
    *	@return null or pc details
    *
    */
    public static function isPc($tag)
    {
		    
		/*
		|--------------------------------------------------------------------------
		|
		| 	Check if propertynumber exists
		|
		|--------------------------------------------------------------------------
		|
		*/
		$item = ItemProfile::propertyNumber($tag)->first();
    	if( count($item) > 0) 
    	{
		    
			/*
			|--------------------------------------------------------------------------
			|
			| 	get property number id
			|
			|--------------------------------------------------------------------------
			|
			*/
    		$id = Pc::getID($item);
		    
			/*
			|--------------------------------------------------------------------------
			|
			| 	query if id is in pc
			|
			|--------------------------------------------------------------------------
			|
			*/
	    	$pc = Pc::where('systemunit_id', '=', $id)
	    		->orWhere('monitor_id','=',$id)
	    		->orWhere('avr_id','=',$id)
	    		->orWhere('keyboard_id','=',$id)
	    		->first();
		    
			/*
			|--------------------------------------------------------------------------
			|
			| 	Check if pc exists 
			|	If existing return id
			|	return null if not
			|
			|--------------------------------------------------------------------------
			|
			*/
	    	if(count($pc) > 0 )
	    	{
	    		return $pc;
	    	}
	    	else
	    	{

				/*
				|--------------------------------------------------------------------------
				|
				| 	If it doesnt exists
				|	check if the tag is pc name
				|	return null if not
				|
				|--------------------------------------------------------------------------
				|
				*/
				if(count($pc = Pc::name($tag)->first()) > 0)
				{
					return $pc;
				}

	    		return null;
	    	}
    	} 
    	else 
    	{
		    
			/*
			|--------------------------------------------------------------------------
			|
			| 	If it doesnt exists
			|	check if the tag is pc name
			|	return null if not
			|
			|--------------------------------------------------------------------------
			|
			*/
			$pc = Pc::name($tag)->first();
			if(count($pc) > 0)
			{
				return $pc;
			}
			
			return null;
    	}
    }

    /**
    *
    *	@param $id accepts pc id
    *	@param $status accepts status to set 'for repair' 'working' 'condemned'
    *	@param $monitor accepts monitor
    *	@param $keyboard accepts keyboard
    *	@param $avr accepts avr
    *	@param $system unit accepts system unit
    *	@return pc information
    *
    */
    public static function setItemStatus($id,$status,$monitor = true, $keyboard = true, $avr = true, $systemunit = true)
    {
    	$pc = Pc::find($id);
 		DB::transaction(function() use ($pc,$status,$avr,$systemunit,$keyboard,$monitor){
			/*
			|--------------------------------------------------------------------------
			|
			| 	System Unit
			|
			|--------------------------------------------------------------------------
			|
			*/
	    	if($systemunit)
	    	{
	    		if( isset($pc->systemunit_id) )
	    		{
	    			ItemProfile::setItemStatus($pc->systemunit_id,$status);
	    		}
	    	}

	 		/*
			|--------------------------------------------------------------------------
			|
			| 	Monitor
			|
			|--------------------------------------------------------------------------
			|
			*/
	    	if($monitor)
	    	{
	    		if( isset($pc->monitor_id) )
	    		{
	    			ItemProfile::setItemStatus($pc->monitor_id,$status);
	    		}
	    	}

			/*
			|--------------------------------------------------------------------------
			|
			| 	Keyboard
			|
			|--------------------------------------------------------------------------
			|
			*/
	    	if($keyboard)
	    	{
	    		if( isset($pc->keyboard_id) )
	    		{
	    			ItemProfile::setItemStatus($pc->keyboard_id,$status);
	    		}
	    	}

			/*
			|--------------------------------------------------------------------------
			|
			| 	AVR
			|
			|--------------------------------------------------------------------------
			|
			*/
	    	if($avr)
	    	{
	    		if( isset($pc->avr_id) )
	    		{
	    			ItemProfile::setItemStatus($pc->avr_id,$status);
	    		}
	    	}
	    });

		/*
		|--------------------------------------------------------------------------
		|
		| 	PC Information
		|
		|--------------------------------------------------------------------------
		|
		*/
    	return $pc;
    }

    /**
    *
    *	@param $pc is a comma separated id of each pc
    *	@param room accepts room name
    *
    */
    public static function setPcLocation($pc,$room)
    {

		$pc = Pc::find($pc);
		if(isset($pc->systemunit_id))
		{
			ItemProfile::setLocation($pc->systemunit_id,$room);
		}

		if(isset($pc->avr_id))
		{
			ItemProfile::setLocation($pc->avr_id,$room);
		}

		if(isset($pc->keyboard_id))
		{
			ItemProfile::setLocation($pc->keyboard_id,$room);
		}

		if(isset($pc->monitor_id))
		{
			ItemProfile::setLocation($pc->monitor_id,$room);
		}

		/*
		*
		*	create a transfer ticket
		*
		*/
		$details = "Pc location has been set to $room";
		$staffassigned = Auth::user()->id;
		$author = Auth::user()->firstname . " " . Auth::user()->middlename . " " . Auth::user()->lastname;
		Ticket::generatePcTicket(
					$pc->id,
					'Transfer',
					'Set Item Location',
					$details,
					$author,
					$staffassigned,
					null,
					'Closed'
				);
    }
}