@extends('_layout.default')

@section('title', '编辑商品类别')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/select2.min.css') }}">
<style>
	.select2-container .select2-selection--single {
		height: 38px;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 38px;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 36px;
	}
</style>

@stop

@section('page-content')

{{ Form::open(array('route' => array('productcategories.update', $productCategory), 'role' => 'form')) }}
{{ Form::hidden('_method', 'PUT') }}

<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>编辑商品类别</h2>
		</div>
		<div class="operate float-right">
			{{ Form::submit('更 新', array('class'=>'recommend btn btn-sm btn-primary'))}}
		</div>
	</div>
</div>
<!-- End Header Bar -->
<!-- Main Content -->
<div class="row main-content">
	<div class="col-md-12 post-content">
		<div class="row">
			<div class="col-md-8">

				<!-- 标题 -->
				<div class="post-title">
					<div class="form-group">
						<label for="name">类别名</label>
						<input name="name" type="text" class="form-control" placeholder="类别名" value="{{ $productCategory->name }}">
					</div>
					<div class="form-group mb-3">
						<label for="parent">父类别</label>
						<select class="custom-select" id="parent" name="parent">
							<option value="0" @if ($productCategory->parent_id == 0) selected @endif>无</option>
							@foreach ($productcategories as $pdc)
							<optgroup label="{{ $pdc->name }}">
								<option value="{{ $pdc->id }}" @if ($productCategory->parent_id == $pdc->id) selected @endif>{{ $pdc->name }}</option>
								@if (isset($pdc->children))
								@foreach ($pdc->children as $pcc)
								<option value="{{ $pcc->id }}" @if ($productCategory->parent_id == $pcc->id) selected @endif>{{ $pcc->name }}</option>
								@endforeach
								@endif
							</optgroup>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Main Content -->
{{ Form::close() }}

@stop

@section('js')

<script type="text/javascript" src="{{ URL::asset('jq-ui/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/select2.min.js') }}"></script>

@stop