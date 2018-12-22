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

{{ Form::open(array('route' => array('orders.update', $order), 'role' => 'form')) }}
{{ Form::hidden('_method', 'PUT') }}
{{ Form::hidden('update-items', 0) }}
{{ Form::hidden('update-status', 0) }}
{{ Form::hidden('update-shipping', 0) }}
{{ Form::hidden('update-remarks', 0) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>修改订单</h2>
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
                                        @if ($order->status == 'pending_payment')
                                        <th scope="col"></th>
                                        @endif
									</tr>
								</thead>
								<tbody id="order_line_items">
                                    @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="products[id][]" value="{{ $item->product_id }}">
                                            <input type="hidden" name="products[name][]" value="{{ $item->name }}">
                                            <input type="hidden" name="products[price][]" value="{{ $item->price }}">
                                            <input type="hidden" name="products[featured_image][]" value="{{ $item->featured_image }}">
                                            <div class="order-item-thumbnail">
                                                <img src="/storage/thumbs/products/thumb_{{ $item->featured_image }}">
                                            </div>
                                        </td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>
                                            @if ($order->status == 'pending_payment')
                                            <select name="products[quantity][]" data-price="{{ $item->price }}" class="form-control">
                                                @for ($i = 1; $i < 10; $i++)
                                                <option value="{{ $i }}" @if ($item->quantity == $i) selected @endif>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @else
                                            {{ $item->quantity }}
                                            @endif
                                        </td>
                                        <td class="p_total">{{ number_format($item->quantity * $item->price, 2) }}</td>
                                        @if ($order->status == 'pending_payment')
                                        <td><a class="btn btn-danger btn-sm order-del-item" href="javascript:void(0)">删除</a></td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if ($order->status == 'pending_payment')
								<tfoot>
									<tr>
										<td colspan="6">
											<div class="btn btn-sm btn-secondary order-add-item" data-toggle="modal" data-target="#modal-add-item">添加商品</div>
										</td>
									</tr>
                                </tfoot>
                                @endif
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
												{{ number_format($order->total_price, 2) }}
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
                    </div>
                    @if ($order->status == 'pending_payment')
					<div class="card-footer">
						<div class="trade-actions">
							<div class="btn btn-sm btn-primary trade-recalculate float-right">重新计算总价</div>
						</div>
                    </div>
                    @endif
				</div>
				<div class="card mb-3">
					<div class="card-header">收货信息</div>
					<div class="card-body">
						<div class="form-group mb-3">
                            <label for="customer">客户</label>
                            @if ($order->status == 'pending_payment')
							<select class="custom-select" id="customer" name="customer" data-placeholder="{{ $order->customer->nickname }}" data-allow_clear="true">
								<option value="" selected="selected"></option>
                            </select>
                            @else
                            <div style="color: #999; width: 100%; border: 1px solid #aaa; padding: 6px 8px; border-radius: 3px;">{{ $order->customer->nickname }}</div>
                            @endif
						</div>
						<div class="form-group mb-3">
                            <label for="customer-address">收货地址</label>
                            @if ($order->status == 'pending_payment')
							<select id="customer-address" class="custom-select" data-placeholder="{{ $order->shipping_address }}" data-allow_clear="true">
								<option value="" selected="selected"></option>
							</select>
                            <input type="hidden" name="customer-address">
                            @else
                            <div style="color: #999; width: 100%; border: 1px solid #aaa; padding: 6px 8px; border-radius: 3px;">{{ $order->shipping_address }}</div>
                            @endif
                        </div>
                        <div class="form-group mb-3 date-group">
							<label for="shipped-at">预计送达日期</label>
							<input name="shipped-at" type="text" class="form-control" readonly value="@if ($order->shipped_at !== ""){{ $order->shipped_at }}@endif">
						</div>
					</div>
				</div>
				<!-- 编辑器 -->
				<div class="form-group">
					<textarea id="editor" name="remarks" class="form-control" rows="8">{{ $order->remarks }}</textarea>
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
								<option value="pending_payment" @if ($acient_status->state == 'pending_payment') selected @endif>等待付款</option>
								<option value="processing" @if ($acient_status->state == 'processing') selected @endif>处理中</option>
								<option value="pending_ship" @if ($acient_status->state == 'pending_ship') selected @endif>等待发货</option>
								<option value="shipped" @if ($acient_status->state == 'shipped') selected @endif>已发货</option>
								<option value="on_hold" @if ($acient_status->state == 'on_hold') selected @endif>已冻结</option>
								<option value="cancelled" @if ($acient_status->state == 'cancelled') selected @endif>已取消</option>
								<option value="completed" @if ($acient_status->state == 'completed') selected @endif>已完成</option>
							</select>
                        </div>
                        <textarea name="status-remarks" id="status-remarks" rows="3">{{ $acient_status->remark }}</textarea>
					</div>
                </div>
                <div class="card mb-3" style="visibility: hidden;">
					<div class="card-header">订单合并</div>
					<div class="card-body">
						<div class="form-group">
                            <label for="order-type">订单类型</label>
                            @if ($order->status == 'pending_payment')
							<select name="order-type" id="order-type" class="form-control">
								<option value="independent" @if ($order->type == 'independent') selected @endif>独立订单</option>
								<!-- <option value="united" @if ($order->type == 'united') selected @endif>父子订单</option> -->
                            </select>
                            @else
                            <div style="color: #999; width: 100%; border: 1px solid #aaa; padding: 6px 8px; border-radius: 3px;">
                                @if ($order->type == 'independent')独立订单@else父子订单@endif
                            </div>
                            @endif
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
<script type="text/javascript">
	$(document).ready(function(){
        $('.date-group input').datepicker({
			todayBtn: true,
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd"
        });
        
        $('select[name*="customer"]').on('change', function(){
			$('input[name="update-shipping"]').val(1);
        });

        $('select[name="status"]').on('change', function(){
            $('input[name="update-status"]').val(1);
        });

        $('select[name="products[quantity][]"]').on('change', function(){
            $('input[name="update-items"]').val(1);
        });

		$('input[name="shipped-at"]').on('change', function(){
			$('input[name="update-shipping"]').val(1);
		});
        
        $(document).on('click', '.order-del-item', function(e){
			$(this).parent().parent().remove();
			$('#formAddProducts').trigger('reset');
			$('input[name="update-items"]').val(1);
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
						$('input[name="update-items"]').val(1);
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
            
            editor.on('valuechanged', function(e){
                $('input[name="update-remarks"]').val(1);
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