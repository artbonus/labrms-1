<!-- item name -->
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('receipt','Receipt List') }}
	{{ Form::select('receipt', (isset($receipts) && count($receipts) > 0 ) ? $receipts : [] ,Input::old('receipt'),[
		'class' => 'form-control',
		'placeholder' => 'None',
		'id' => 'receipt'
	]) }}
	</div>
</div>
<!-- item name -->
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('number','Property Acknowledgement Receipt') }}
	{{ Form::text('number',isset($receipt->number) ? $receipt->number : Input::old('number'),[
		'class' => 'form-control',
		'placeholder' => 'Receipt',
		'id' => 'number'
	]) }}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('ponumber','Purchase Order Number') }}
	{{ Form::number('ponumber',isset($receipt->ponumber) ? $receipt->ponumber : Input::old('ponumber'),[
		'class' => 'form-control',
		'placeholder' => 'P.O. Number',
		'id' => 'ponumber'
	]) }}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('podate','Purchase Order Date') }}
	{{ Form::text('podate',isset($receipt->podate) ? $receipt->podate : Input::old('podate'),[
		'class' => 'form-control',
		'placeholder' => 'P.O. Date',
		'id' => 'podate',
		'readonly'
	]) }}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('invoicenumber','Invoice Number') }}
	{{ Form::number('invoicenumber',isset($receipt->invoicenumber) ? $receipt->invoicenumber : Input::old('invoicenumber'),[
		'class' => 'form-control',
		'placeholder' => 'Invoice Number',
		'id' => 'ponumber'
	]) }}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('invoicedate','Invoice Date') }}
	{{ Form::text('invoicedate',isset($receipt->invoicedate) ? $receipt->invoicedate : Input::old('invoicedate'),[
		'class' => 'form-control',
		'placeholder' => 'Invoice Date',
		'id' => 'invoicedate',
		'readonly'
	]) }}
	</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
	{{ Form::label('fundcode','Fund Code') }}
	{{ Form::text('fundcode',isset($receipt->fundcode) ? $receipt->fundcode : Input::old('fundcode'),[
		'class' => 'form-control',
		'placeholder' => 'Fund Code',
		'id' => 'fundcode'
	]) }}
	</div>
</div>