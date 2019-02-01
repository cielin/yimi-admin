@extends('_layout.default')

@section('title', '修改订单')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/simditor.css') }}">
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
	.backbone-modal .select2-container {
		width: 100%!important;
	}
	.order-item-thumbnail {
		width: 38px;
		height: 38px;
		border: 2px solid #e8e8e8;
		background: #f8f8f8;
		color: #ccc;
		position: relative;
		font-size: 21px;
		display: block;
		text-align: center;
	}
	.order-item-thumbnail img {
		width: 100%;
		height: 100%;
		margin: 0;
		padding: 0;
		position: relative;
	}
	.table th, .table td {
		vertical-align: middle;
	}
    #status-remarks {
		border: solid 1px #ced4da;
		border-radius: 3px;
		width: 100%;
		display: block;
	}
</style>

@stop

@section('page-content')

{{ Form::open(array('route' => array('notifys.update', $notify), 'role' => 'form')) }}
{{ Form::hidden('_method', 'PUT') }}
{{ Form::hidden('old-customer', $notify->customer_id) }}
{{ Form::hidden('old-content', $notify->content) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>修改消息</h2>
		</div>
		<div class="operate float-right">
			{{ Form::submit('提 交', array('class'=>'recommend btn btn-sm btn-primary'))}}
		</div>
	</div>
</div>
<!-- End Header Bar -->
<!-- Main Content -->
<div class="row main-content">
	<div class="col-md-12 post-content">
		<div class="row">
			<div class="col-md-8">
				<div class="card mb-3">
					<div class="card-header"></div>
					<div class="card-body">
						<div class="form-group mb-3">
                            <label for="customer">客户</label>
							<select class="custom-select" id="customer" name="customer" data-placeholder="{{ $notify->customer->nickname }}" data-allow_clear="true">
								<option value="" selected="selected"></option>
                            </select>
						</div>
					</div>
				</div>
				<!-- 编辑器 -->
				<div class="form-group">
					<textarea id="editor" name="content" class="form-control" rows="8">{{ $notify->content }}</textarea>
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
<script type="text/javascript" src="{{ URL::asset('js/module.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/hotkeys.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/uploader.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/simditor.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/select2.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){ 
        $('select[name*="customer"]').on('change', function(){
			$('input[name="update-shipping"]').val(1);
        });
		$(document).on('change', 'select[name="customer"]', function(e){
			e.preventDefault();
			var cid = $(this).val();
			$.ajax({
				type: "GET",
				url: "{{ route('admin.customers.getcustomeraddresses') }}",
				dataType: 'json',
				data: {
					cid: cid
				}
			})
			.done(function(data) {
				if (data.errcode == 0) {
					$('#customer-address').select2({
						data: data.data
					})
				}
			})
		});

		(function(){
			var editor = new Simditor({
				textarea: $('#editor'),
				placeholder: '请输入消息',
				toolbar: false,
				pasteImage: false
            });
            
            editor.on('valuechanged', function(e){
                $('input[name="update-remarks"]').val(1);
            });
                
		})();

		$('#customer-address').select2();

		$('#customer').select2({
			ajax: {
				url: "{{ '/api/customers/getdatafromterm/' }}",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;

					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			placeholder: '选取一位客户',
			escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			minimumInputLength: 1,
			templateResult: formatRepo,
			templateSelection: formatRepoSelection
		});

		function formatRepo(repo) {
			if (repo.loading) {
				return repo.text;
			}

			var markup = "<div class='select2-result-repository clearfix'>#" + repo.id + " " + repo.realname + " (" + repo.email + ")</div>"

			return markup;
		}

		function formatRepoSelection(repo) {
			if (repo.realname) {
				return ("#" + repo.id + " " + repo.realname + " (" + repo.email + ")")
			}

			return repo.text;
		}

		$('#products').select2({
			ajax: {
				url: "{{ env('SITE_ADMIN_URL') . 'api/products/getdatafromterm/' }}",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;

					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			placeholder: '选取一件商品',
			escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			minimumInputLength: 3,
			templateResult: formatPRepo,
			templateSelection: formatPRepoSelection
		});

		function formatPRepo(repo) {
			if (repo.loading) {
				return repo.text;
			}

			var markup = "<div class='select2-result-repository clearfix'>#" + repo.id + " " + repo.name + " (" + repo.price + ")</div>"

			return markup;
		}

		function formatPRepoSelection(repo) {
			if (repo.name) {
				return ("#" + repo.id + " " + repo.name)
			}

			return repo.text;
		}
	})
</script>

@stop