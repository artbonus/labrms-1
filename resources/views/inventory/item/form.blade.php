
					<!-- item name -->
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('brand','Brand') }}
						{{ Form::text('brand',isset($brand) ? $brand : Input::old('brand'),[
							'class' => 'form-control',
							'placeholder' => 'Brand',
							'id' => 'brand'
						]) }}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('model','Model') }}
						{{ Form::text('model',isset($model) ? $model : Input::old('model'),[
							'class' => 'form-control',
							'placeholder' => 'Model',
							'id' => 'model'
						]) }}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('itemtype','Type') }}
						{{ Form::select('itemtype',$itemtypes,isset($itemtype) ? $itemtype : Input::old('itemtype'),[
							'class' => 'form-control',
							'id' => 'itemtype'
						]) }}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('itemsubtype','Sub Type') }}
						{{ Form::select('itemsubtype',$itemsubtypes,isset($itemsubtype) ? $itemsubtype : Input::old('itemsubtype'),[
							'class' => 'form-control',
							'id' => 'itemsubtype',
							'placeholder' => 'None'
						]) }}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('details','Item Details') }}
						{{ Form::textarea('details',Input::old('details'),[
							'class' => 'form-control',
							'placeholder' => 'Item Details',
							'id' => 'details'
						]) }}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('unit','Unit') }}
						{{ Form::select('unit',$units,Input::old('unit'),[
							'class' => 'form-control'
						]) }}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
						{{ Form::label('quantity','Quantity') }}
						{{ Form::number('quantity',Input::old('quantity'),[
							'class' => 'form-control',
							'placeholder' => 'Quantity'
						]) }}
						</div>
					</div>