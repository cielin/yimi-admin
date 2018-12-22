@extends('_layout.default')

@section('title', '添加商品类别')

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

{{ Form::open(array('route' => 'productcategories.store', 'files' => true, 'role' => 'form')) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>添加商品类别</h2>
		</div>
		<div class="operate float-right">
			{{ Form::submit('发 布', array('class'=>'recommend btn btn-sm btn-primary'))}}
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
						<input name="name" type="text" class="form-control" placeholder="类别名">
					</div>
					<div class="form-group mb-3">
						<label for="parent">父类别</label>
						<select class="custom-select" id="parent" name="parent">
							<option value="0" selected>无</option>
							@foreach ($productcategories as $productcategory)
							<optgroup label="{{ $productcategory->name }}">
								<option value="{{ $productcategory->id }}">{{ $productcategory->name }}</option>
								@if (isset($productcategory->children))
								@foreach ($productcategory->children as $pcc)
								<option value="{{ $pcc->id }}">{{ $pcc->name }}</option>
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
<script type="text/javascript">
	$(document).ready(function(){
		$('#parent').select2();
		$('input[name="name"]').on('focusout', function(e){
			e.preventDefault();

			var _title = $.trim($(this).val());

			if (_title == "") {
				return;
			}
			else {
				var data = "";
				var url = "";
				$.ajax({
					type: "GET",
					url: url + "/" + _title,
					async: false,
					success: function(response) {
						data = jQuery.parseJSON(response);
						if (data.status == "success") {
							$('input[name="slug"]').val(data.slug);
						}
					}
				});
			}
		});
	})
</script>

@stop