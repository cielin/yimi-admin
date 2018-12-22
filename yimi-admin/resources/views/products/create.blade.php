@extends('_layout.default')

@section('title', '添加商品')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/simditor.css') }}">
<style>
	.simditor .simditor-body img, .editor-style img {
		max-width: 100%;
		height: auto;
	}
</style>

@stop

@section('page-content')

{{ Form::open(array('route' => 'products.store', 'files' => true, 'role' => 'form')) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>添加商品</h2>
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
				<div class="form-group">
					<label for="name">商品名称*</label>
					<input name="name" type="text" class="form-control">
				</div>

				<!-- 商品类别 -->
				<div class="form-group mb-3">
					<label for="category">商品类别*</label>
					<select class="custom-select" id="category" name="category">
						<option value="-1" selected>请选择商品类别...</option>
						@foreach ($productcategories as $productcategory)
						<optgroup label="{{ $productcategory->name }}">
							@if (isset($productcategory->children) && sizeof($productcategory->children) > 0)
							@foreach ($productcategory->children as $pcc)
							@if (isset($pcc->children) && sizeof($pcc->children) > 0)
							@foreach ($pcc->children as $pccc)
							<option value="{{ $pccc->id }}">{{ $pccc->name }}</option>
							@endforeach
							@else
							<option value="{{ $pcc->id }}">{{ $pcc->name }}</option>
							@endif
							@endforeach
							@else
							<option value="{{ $productcategory->id }}">{{ $productcategory->name }}</option>
							@endif
						</optgroup>
						@endforeach
					</select>
				</div>

				<!-- 商品所属品牌 -->
				<div class="form-group mb-3">
					<label for="brand">商品品牌*</label>
					<select class="custom-select" id="brand" name="brand">
						<option value="-1" selected>请选择商品品牌...</option>
						@foreach ($brands as $brand)
						<option value="{{ $brand->id }}">{{ $brand->name }} ({{ $brand->country }})</option>
						@endforeach
					</select>
				</div>

				<!-- 商品属性 -->
				<?php if (count($product_basic_attrs) > 0) : ?>
				<div class="form-group">
					<label for="attributes">商品属性</label>
					<div class="row">
					<?php for ($i=0; $i < count($product_basic_attrs); $i++) : ?>
					<?php if ($i != 0 && $i%2 == 0) : ?>
						<div class="w-100"></div>
					<?php endif; ?>
						<div class="col">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">{{ $product_basic_attrs[$i]->name }}</span>
								</div>
								@if ($product_basic_attrs[$i]->name === "空间")
								<select name="attributes[prop_{{ $product_basic_attrs[$i]->id }}]" class="custom-select">
									<option value="卧室">卧室</option>
									<option value="餐厅">餐厅</option>
									<option value="客厅">客厅</option>
									<option value="浴室">浴室</option>
									<option value="办公室">办公室</option>
									<option value="室外">室外</option>
									<option value="书房">书房</option>
									<option value="厨房">厨房</option>
									<option value="玄关">玄关</option>
									<option value="儿童">儿童</option>
								</select>
								@else
								<input name="attributes[prop_{{ $product_basic_attrs[$i]->id }}]" type="text" class="form-control">
								@endif
							</div>
						</div>
					<?php endfor; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- 商品包装规格 -->
				<?php if (count($product_package_attrs) > 0) : ?>
				<div class="form-group">
					<label for="specs">商品包装规格</label>
					<div class="row">
					<?php for ($i=0; $i < count($product_package_attrs); $i++) : ?>
					<?php if ($i != 0 && $i%2 == 0) : ?>
						<div class="w-100"></div>
					<?php endif; ?>
						<div class="col">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">{{ $product_package_attrs[$i]->name }}</span>
								</div>
								<input name="specs[prop_{{ $product_package_attrs[$i]->id }}]" type="text" class="form-control">
							</div>
						</div>
					<?php endfor; ?>
					</div>
				</div>
				<?php endif; ?>

				<?php if (count($product_sale_attrs) > 0) : ?>
				<div class="form-group">
					<label for="sales">商品销售规格</label>
					<?php for ($i=0; $i < count($product_sale_attrs); $i++) : ?>
					<div class="form-group">
						<label for="sale-colors">{{ $product_sale_attrs[$i]->name }}</label>
						<input type="hidden" name="color-id" value="{{ $product_sale_attrs[$i]->id }}">
						<div class="row mb-2">
							<div class="col-md-1 pr-0">
								<input type="checkbox" class="color-select" name="color-select[]" class="form-check">
							</div>
							<div class="col-md-3 pr-0">
								<input type="text" name="sale-colors[]" placeholder="输入主色" maxlength="30" class="form-control">
							</div>
							<div class="col-md-3 pr-0">
								<input type="text" name="color-price[]" placeholder="价格（元）" maxlength="30" class="form-control">
							</div>
							<div class="col-md-2 pr-0">
								<input type="text" name="color-quantity[]" placeholder="库存量" maxlength="30" class="form-control">
							</div>
							<div class="col-md-3 pr-0">
								<div>
									<img width="36" height="36" data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
									<a href="#" class="btn btn-primary btn-sm thumbnail-button" role="button">上传</a>
									<a href="#" class="btn btn-secondary btn-sm clear-thumbnail-button" role="button">删 除</a>
									{{ Form::file('color-images[]', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
								</div>	
							</div>
						</div>
					</div>
					<?php endfor; ?>
				</div>
				<?php endif; ?>

				<!-- 商品一口价 -->
				<div class="form-group">
					<label for="attributes">一口价及库存</label>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">价格</span>
						</div>
						<input name="price" type="text" class="form-control" placeholder="999999">
						<div class="input-group-append">
							<span class="input-group-text">元</span>
						</div>
					</div>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">库存</span>
						</div>
						<input type="text" class="form-control" name="quantity" placeholder="999">
					</div>
				</div>

				<!-- 编辑器 -->
				<div class="post-editor form-group">
					<label for="content">商品描述</label>
					<textarea id="editor" name="content" class="form-control" rows="12"></textarea>
				</div>

				<!-- 商品图 -->
				<div class="card form-group">
					<div class="card-header">商品图片</div>
					<div class="card-body">
						<div class="pro-pic">
						<div class="row">
							<div class="col-md-3">
								<div class="thumbnail">
									<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
									<div class="caption clearfix">
										<div class="radio">
											<label>
											<input type="radio" name="cover" id="cover1" value="1" checked>
											特色图片
											</label>
										</div>
										<p>
										<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
										<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
										{{ Form::file('thumb[]', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="thumbnail">
									<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
									<div class="caption clearfix">
										<div class="radio">
											<label>
											<input type="radio" name="cover" id="cover2" value="2" >
											特色图片
											</label>
										</div>
										<p>
										<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
										<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
										{{ Form::file('thumb[]', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="thumbnail">
									<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
									<div class="caption clearfix">
										<div class="radio">
											<label>
											<input type="radio" name="cover" id="cover3" value="3" >
											特色图片
											</label>
										</div>
										<p>
										<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
										<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
										{{ Form::file('thumb[]', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="thumbnail">
									<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
									<div class="caption clearfix">
										<div class="radio">
											<label>
											<input type="radio" name="cover" id="cover4" value="4" >
											特色图片
											</label>
										</div>
										<p>
										<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
										<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
										{{ Form::file('thumb[]', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
										</p>
									</div>
								</div>
							</div>
						</div>
						</div>
					</div>
				</div>

				<!-- 商品列表页封面图 -->
				<div class="card">
					<div class="card-header">列表页封面图</div>
					<div class="card-body">
						<div class="pro-pic">
							<div class="row">
								<div class="col-md-3">
									<div class="thumbnail">
										<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
										<div class="caption clearfix">
											<p>
												<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
												<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
												{{ Form::file('poster', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<!-- 状态 -->
				<div class="card mb-3">
					<div class="card-header">状态</div>
					<div class="card-body">
						<div class="radio-group">
							<label class="radio-inline">
							<input name="status" type="radio" id="inlineRadio1" value="draft" checked> 待审
							</label>
							<label class="radio-inline">
							<input name="status" type="radio" id="inlineRadio2" value="active"> 通过
							</label>
						</div>
					</div>				
				</div>
				<!-- 首页配置 -->
				<div class="card mb-3">
					<div class="card-header">首页配置</div>
					<div class="card-body">
						<div class="form-check mb-3">
							<input type="checkbox" class="form-check-input" name="is_featured" id="featured-product">
							<label for="featured-product" class="form-check-label">推送到首页买手推荐</label>
						</div>
						<div class="form-check">
							<input type="checkbox" class="form-check-input" name="is_waterfalled" id="waterfalled-product">
							<label for="waterfalled-product" class="form-check-label">推送到首页发现好物</label>
						</div>
					</div>
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
<script type="text/javascript" src="{{ URL::asset('js/module.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/hotkeys.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/uploader.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/simditor.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		(function(){
			var editor = new Simditor({
				textarea: $('#editor'),
				upload: {
					url: '{{ route("admin.products.upload") }}',
					fileKey: 'upload_file',
					connectionCount: 3,
					leaveConfirm: '正在上传图片，确定离开当前页面？'
				},
				pasteImage: true
			});
		})();

		$('.thumbnail-button').on('click', function(e){
			e.preventDefault();
			$(this).parent().find('.thumb').click();
		});

		$('.clear-thumbnail-button').on('click', function(e){
			e.preventDefault();
			$(this).parent().find('.thumb').wrap('<form>').parent('form').trigger('reset');
			$(this).parent().find('.thumb').unwrap();
			$(this).parent().find('.thumb').trigger('change');
		});

		$('.thumb').on('change', function(){
			var _this = $(this);
			var files = !!this.files ? this.files : [];
			if (!files.length || !window.FileReader) {
				_this.parent().parent().parent().find('img').attr('src', "{{ URL::asset('images/pic.png') }}");
				return;
			}

			if (/^image/.test(files[0].type)){
				var reader = new FileReader();
				reader.readAsDataURL(files[0]);

				reader.onloadend = function(){
					_this.parent().parent().parent().find('img').attr('src', this.result);
				}
			}
		});

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

		$('.color-select').on('change', function(e) {
			e.preventDefault();

			if (this.checked) {
				var parent = $(this).parent().parent();
				var clone = $(parent).clone(true);
				$(clone).wrap('<form>').parent('form').trigger('reset');
				$(clone).unwrap();
				$(clone).find('.thumb').trigger('change');
				$(clone).appendTo($(parent).parent());
			}
			else {
				var parent = $(this).parent().parent();
				var grandparent = $(parent).parent();
				var color_quantity = Number($($(parent).find('input[name="color-quantity[]"]')[0]).val());
				if (color_quantity !=='' && color_quantity !== 0) {
					var quantity = Number($('input[name="quantity"]').val());
					quantity -= color_quantity;
					if (quantity <= 0) quantity = '';
					$('input[name="quantity"]').val(quantity);
				}

				$(parent).remove();

				var grandson_input_checks = $(grandparent).find('input.color-select');
				if (grandson_input_checks.length > 1) {
					var grandson_input_prices = $(grandparent).find('input[name="color-price[]"]');
					var input_prices = new Array();

					for (var i = grandson_input_prices.length - 1; i >= 0; i--) {
						if ($(grandson_input_prices[i]).val() !== '') {
							input_prices.push(Number($(grandson_input_prices[i]).val()));
						}
					}
					
					if (input_prices.length > 0) {
						$('input[name="price"]').val(Math.min.apply(Math, input_prices));
					}
					else {
						$('input[name="price"]').val('');
					}
				}
				else {
					$('input[name="price"]').removeAttr('readonly');
					$('input[name="quantity"]').removeAttr('readonly');
				}
			}
		});

		$('input[name="color-price[]"]').on('focusout', function(e) {
			e.preventDefault();

			var parent = $(this).parent().parent();
			var select = $(parent).find('.color-select')[0];

			if (select.checked) {
				$('input[name="price"]').attr('readonly', 'readonly');
				var price = $('input[name="price"]').val();
				if (price === '') {
					price = $(this).val();
					$('input[name="price"]').val(price);
				}
				else {
					if (Number($(this).val()) < Number(price)) {
						price = $(this).val();
						$('input[name="price"]').val(price);
					}
				}
			}
		});

		$('input[name="color-quantity[]"]').on('focusout', function(e) {
			e.preventDefault();

			var parent = $(this).parent().parent();
			var select = $(parent).find('.color-select')[0];

			if (select.checked) {
				$('input[name="quantity"]').attr('readonly', 'readonly');
				var quantity = $('input[name="quantity"]').val();
				if (quantity === '') {
					quantity = $(this).val();
					$('input[name="quantity"]').val(quantity);
				}
				else {
					quantity = Number(quantity);
					quantity += Number($(this).val());
					$('input[name="quantity"]').val(quantity);
				}
			}
		});
	})
</script>

@stop