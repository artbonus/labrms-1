@extends('layouts.master-blue')
@section('title')
Inventory | Create
@stop
@section('navbar')
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('layouts.navbar')
@stop
@section('style')
{{ HTML::style(asset('css/style.css')) }}
{{ HTML::style(asset('css/jquery-ui.min.css')) }}
<style>
	#inventory{
		display:none;
	}

	#podate,#invoicedate{
		background-color:white;
	}

</style>
@stop
@section('script-include')
{{ HTML::script(asset('js/jquery-ui.js')) }}
@stop
@section('content')
<div class="container-fluid" id="page-body">
	<div class='col-md-offset-3 col-md-6'>
		<div class="panel panel-body" style="padding-top: 20px;padding-left: 40px;padding-right: 40px;">
	 		{{ Form::open(['method'=>'post','route'=>'inventory.item.store','class'=>'form-horizontal','id'=>'inventoryForm']) }}
			<legend><h3 style="color:#337ab7;"><span id="form-name">Receipt</span></h3></legend>
			<ul class="breadcrumb">
				<li><a href="{{ url('inventory/item') }}">Item Inventory</a></li>
				<li class="active">Create</li>
			</ul>
			@if (count($errors) > 0)
			 <div class="alert alert-danger alert-dismissible" role="alert">
			  	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <ul class="list-unstyled" style='margin-left: 10px;'>
		          @foreach ($errors->all() as $error)
		              <li class="text-capitalize">{{ $error }}</li>
		          @endforeach
		      </ul>
			  </div>
			@endif
	 		<div id="receipt">
	 			@include('receipt.form')
				<div class="form-group">
					<div class="col-sm-offset-8 col-sm-4">
						<button name="next" id="link-to-inventory" class="btn btn-primary btn-flat btn-block" type="button">Next <span class="glyphicon glyphicon-triangle-right" aria-hidden="true"></span></button>
					</div>
				</div>
	 		</div>
	 		<div id="inventory">
				<div id="page-one">
					<div id="alert-existing"></div>
					@include('inventory.item.form')
					<div class="form-group">
						<div class="col-sm-12">
						<input type="checkbox" id="redirect-profiling" name="redirect-profiling" checked />
						<span class="text-muted" style="font-size:12;">Profile items</span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<button type="submit" value="create" name="action" id="submit" class="btn btn-lg btn-primary btn-flat btn-block"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Submit </button>
						</div>
					</div>
				</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop
@section('script')
<script type="text/javascript">
	$(document).ready(function(){

		$('#brand #itemtype #model').on('change',function(){
			url = "{{ url('get') }}" + '/' + $('#itemtype').val() + '/' + $('#brand').val() + '/' + $('#model').val()
			setValue(url)
		});

		setValue(function(){
			url = "{{ url('get') }}" + '/' + $('#itemtype').val() + '/' + $('#brand').val() + '/' + $('#model').val()
			return url
		})

		function setValue(_url)
		{
			$.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
				type: 'get',
                url: _url,
				dataType: 'json',
				success: function(response){
					if(response != 'error')
					{
						$('#alert-existing').html("<div class='alert alert-success'><strong>Item exists!</strong> The quantity you inputted will be appended to the existing item</div>")
						$('#details').val(response.details)
						$('#unit').val(response.unit)
						$('#warranty').val(response.warranty)
						// $('#details').prop("readonly","readonly")
						// $('#unit').prop("readonly","readonly")
						// $('#warranty').prop("readonly","readonly")
					} else {
						$('#alert-existing').html("<div class='alert alert-warning'><strong>Warning!</strong> This will create a new inventory item</div>")
						// $('#details').removeProp("readonly")
						// $('#unit').removeProp("readonly")
						// $('#warranty').removeProp("readonly")
					}
				}
			})
		}

		$('#brand').autocomplete({
			source: "{{ url('get/inventory/item/brand') }}"
		})

		$('#model').autocomplete({
			source: "{{ url('get/inventory/item/model') }}"
		})

		$('#link-to-inventory').click(function(){
			$('#form-name').text('Inventory')
			$('#page-two').hide(600);
			$('#receipt').hide(600);
			$('#inventory').show();
			$('#page-one').show(600);
		});

		$('#link-to-receipt').click(function(){
			$('#form-name').text('Receipt')
			$('#inventory').hide(600);
			$('#receipt').show(600);
		});

		$( "#podate" ).datepicker({
			  changeMonth: true,
			  changeYear: false,
			  maxAge: 59,
			  minAge: 15,
		});

		$( "#invoicedate" ).datepicker({
			  changeMonth: true,
			  changeYear: false,
			  maxAge: 59,
			  minAge: 15,
		});

		@if(Input::old('podate'))
			$('#podate').val('{{ Input::old('podate') }}');
			setDate("#podate");
		@else
			$('#podate').val("{{ Carbon\Carbon::now()->toFormattedDateString() }}");
			setDate("#podate");
		@endif

		$('#podate').on('change',function(){
			setDate("#podate");
		});

		@if(Input::old('invoicedate'))
			$('#invoicedate').val('{{ Input::old('invoicedate') }}');
			setDate("#invoicedate");
		@else
			$('#invoicedate').val("{{ Carbon\Carbon::now()->toFormattedDateString() }}");
			setDate("#invoicedate");
		@endif

		$('#invoicedate').on('change',function(){
			setDate("#invoicedate");
		});

		function setDate(object){
			var object_val = $(object).val()
			var date = moment(object_val).format('MMM DD, YYYY');
			$(object).val(date);
		}
	})
</script>
@stop
