@extends('_layout.default')

@section('title', '添加新订单')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/simditor.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-datepicker3.min.css') }}">
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

{{ Form::open(array('route' => 'orders.store', 'role' => 'form')) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>添加新订单</h2>
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
				<!-- 订单内容 -->
				<div class="card mb-3">
					<div class="card-header">订单内容</div>
					<div class="card-body">
						<div class="order-wrapper">
							<table class="table" style="border: solid 1px #dee2e6">
								<thead>
									<tr class="thead-light">
										<th scope="col">商品</th>
										<th scope="col"></th>
										<th scope="col">单价</th>
										<th scope="col">数量</th>
										<th scope="col">商品总价</th>
										<th scope="col"></th>
									</tr>
								</thead>
								<tbody id="order_line_items">
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6">
											<div class="btn btn-sm btn-secondary order-add-item" data-toggle="modal" data-target="#modal-add-item">添加商品</div>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="order-total-items">
							<table class="table float-right" style="width: 50%">
								<tbody>
									<tr>
										<td class="label">订单总价：</td>
										<td width="1%"></td>
										<td class="total">
											<span class="price-amount">
												<span class="price-currency-symbol">¥</span>
												0
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="card-footer">
						<div class="trade-actions">
							<div class="btn btn-sm btn-primary trade-recalculate float-right">重新计算总价</div>
						</div>
					</div>
				</div>
				<div class="card mb-3">
					<div class="card-header">收货信息</div>
					<div class="card-body">
						<div class="form-group mb-3">
							<label for="customer">客户</label>
							<select class="custom-select" id="customer" name="customer" data-placeholder="客户" data-allow_clear="true">
								<option value="" selected="selected"></option>
							</select>
						</div>
						<div class="form-group mb-3">
							<label for="customer-address">收货地址</label>
							<select id="customer-address" class="custom-select" data-placeholder="请先选择一个客户" data-allow_clear="true">
								<option value="" selected="selected"></option>
							</select>
							<input type="hidden" name="customer-address">
						</div>
						<div class="form-group mb-3 date-group">
							<label for="shipped-at">预计送达日期</label>
							<input name="shipped-at" type="text" class="form-control" readonly>
						</div>
					</div>
				</div>
				<!-- 编辑器 -->
				<div class="form-group">
					<textarea id="editor" name="remarks" class="form-control" rows="8"></textarea>
				</div>
			</div>
			<div class="col-md-4">
				<!-- 状态 -->
				<div class="card mb-3">
					<div class="card-header">基本信息</div>
					<div class="card-body">
						<div class="form-group">
							<label for="status">订单状态</label>
							<select name="status" id="status" class="form-control">
								<option value="pending_payment">等待付款</option>
								<option value="processing">处理中</option>
								<option value="pending_ship">等待发货</option>
								<option value="shipped">已发货</option>
								<option value="on_hold">已冻结</option>
								<option value="cancelled">已取消</option>
								<option value="completed">已完成</option>
							</select>
						</div>
						<div class="form-group">
							<label for="status-remark">状态备注</label>
							<textarea name="status-remarks" id="status-remarks" rows="3"></textarea>
						</div>
					</div>
				</div>
				<div class="card mb-3">
					<div class="card-header">订单合并</div>
					<div class="card-body">
						<div class="form-group">
							<label for="order-type"">订单类型</label>
							<select name="order-type" id="order-type" class="form-control">
								<option value="independent">独立订单</option>
								<!-- <option value="united">父子订单</option> -->
							</select>
						</div>
						<!-- <div class="form-group mb-3">
							<label for="order-related">关联订单</label>
							<select class="custom-select" id="order-related" name="order-related" data-placeholder="关联订单" data-allow_clear="true">
								<option value="" selected="selected"></option>
							</select>
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Main Content -->
{{ Form::close() }}

<div class="modal fade backbone-modal" id="modal-add-item" tabindex="-1" role="dialog" aria-labelledby="modal-add-item-label" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="formAddProducts" role="form">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal-add-item-label">添加商品</h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<select name="products[]" id="products" class="custom-select" multiple="multiple"></select>
					</div>
				</div>
				<div class="modal-footer">
					<button id="submit" type="submit" class="btn btn-primary">添 加</button>
				</div>
			</div>
		</form>
	</div>
</div>

@stop

@section('js')

<script type="text/javascript" src="{{ URL::asset('jq-ui/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/module.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/hotkeys.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/uploader.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/simditor.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.date-group input').datepicker({
			todayBtn: true,
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd"
		});

		$(document).on('click', '.order-del-item', function(e){
			$(this).parent().parent().remove();
			$('#formAddProducts').trigger('reset');
		});

		$(document).on('change', '#order-type', function(e){
			e.preventDefault();

			var cid = $('select[name="customer"]').val();
			if (cid !== '') {
				$.ajax({
					type: "GET",
					url: "{{ route('admin.orders.getsiblingorders') }}",
					dataType: 'json',
					data: {
						cid: cid
					}
				})
				.done(function(data) {
					if (data.errcode == 0) {
						$('#order-related').select2({
							data: data.data
						})
					}
				})
			}
		});

		$(document).on('change', '#customer-address', function(){
			$('input[name="customer-address"]').val($(this).text().trim());
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

		$(document).on('change', 'select[name*="products[quantity][]"]', function(){
			var price = parseInt($(this).data('price'));
			var count = parseInt($(this).val());
			var total = price * count;
			total = total.toFixed(2);

			$(this).parent().parent().find('.p_total').text(total);
		});

		$(document).on('submit', '#formAddProducts', function(e) {
			e.preventDefault();

			var productsData = new FormData($('#formAddProducts')[0]);
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				type: "POST",
				url: "{{ route('orders.addproduct') }}",
				dataType: 'json',
				processData: false,
				contentType: false,
				cache: false,
				data: productsData
			})
			.done(function (data) {
				$('#modal-add-item').modal('hide');
				$('#formAddProducts').trigger('reset');
				if(data.errcode == 0) {
					var products = data.products;
					for (var i = products.length - 1; i >= 0; i--) {
						var markup = "<tr>" + 
							"<td><input type='hidden' name='products[id][]' value='" + products[i].id + "'><input type='hidden' name='products[name][]' value='" + products[i].name + "'><input type='hidden' name='products[price][]' value='" + products[i].price + "'><input type='hidden' name='products[featured_image][]' value='" + products[i].featured_image + "'><div class='order-item-thumbnail'><img src='{{ env('SITE_ADMIN_URL') }}storage/thumbs/products/thumb_" + products[i].featured_image + "'></div></td>" + 
							"<td>" + products[i].name + "</td>" + 
							"<td>" + products[i].price + "</td>" + 
							"<td>" + 
								"<select name='products[quantity][]' data-price='" + products[i].price + "' class='form-control'>" + 
									"<option value='1' selected>1</option>" +
									"<option value='2'>2</option>" +
									"<option value='3'>3</option>" +
									"<option value='4'>4</option>" +
									"<option value='5'>5</option>" +
									"<option value='6'>6</option>" +
									"<option value='7'>7</option>" +
									"<option value='8'>8</option>" +
									"<option value='9'>9</option>" +
								"</select>" +
							"</td>" + 
							"<td class='p_total'>" + products[i].total + "</td>" + 
							"<td><a class='btn btn-danger btn-sm order-del-item' href='javascript:void(0)'>删除</a></td>" + 
							"</tr>";
						$('#order_line_items').append(markup);
					}
				}
			});
		});

		(function(){
			var editor = new Simditor({
				textarea: $('#editor'),
				placeholder: '订单备注',
				toolbar: false,
				pasteImage: false
			});
		})();

		$('#customer-address').select2();

		$('#customer').select2({
			ajax: {
				url: "{{ env('SITE_ADMIN_URL') . 'api/customers/getdatafromterm/' }}",
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