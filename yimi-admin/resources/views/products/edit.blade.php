@extends('_layout.default')

@section('title', '编辑商品')

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

{{ Form::open(array('route' => array('products.update', $product), 'files' => true, 'role' => 'form')) }}
{{ Form::hidden('_method', 'PUT') }}
{{ Form::hidden('update-thumbnail', 0) }}
{{ Form::hidden('update-attributes', 0) }}
{{ Form::hidden('update-specs', 0) }}
{{ Form::hidden('update-sales', 0) }}
{{ Form::hidden('covername', '') }}
{{ Form::hidden('update-poster', 0) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>编辑商品</h2>
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
				<div class="form-group">
					<label for="name">商品名称</label>
					<input name="name" type="text" class="form-control" value="{{ $product->name }}">
				</div>

				<!-- 商品类别 -->
				<div class="form-group mb-3">
					<label for="category">商品类别</label>
					<select class="custom-select" id="category" name="category">
						<option value="-1" @if (null === $product->category_id || 0 === $product->category_id) selected @endif>请选择商品类别...</option>
						@foreach ($productcategories as $productcategory)
						<optgroup label="{{ $productcategory->name }}">
							@if (isset($productcategory->children) && sizeof($productcategory->children) > 0)
							@foreach ($productcategory->children as $pcc)
							@if (isset($pcc->children) && sizeof($pcc->children) > 0)
							@foreach ($pcc->children as $pccc)
							<option value="{{ $pccc->id }}" @if ($product->category_id === $pccc->id) selected @endif>{{ $pccc->name }}</option>
							@endforeach
							@else
							<option value="{{ $pcc->id }}" @if ($product->category_id === $pcc->id) selected @endif>{{ $pcc->name }}</option>
							@endif
							@endforeach
							@else
							<option value="{{ $productcategory->id }}" @if ($product->category_id === $productcategory->id) selected @endif>{{ $productcategory->name }}</option>
							@endif
						</optgroup>
						@endforeach
					</select>
				</div>

				<!-- 商品所属品牌 -->
				<div class="form-group mb-3">
					<label for="brand">商品品牌</label>
					<select class="custom-select" id="brand" name="brand">
						<option value="-1" @if (null === $product->brand_id) selected @endif>请选择商品品牌...</option>
						@foreach ($brands as $brand)
						<option value="{{ $brand->id }}" @if ($product->brand_id === $brand->id) selected @endif>{{ $brand->name }} ({{ $brand->country }})</option>
						@endforeach
					</select>
				</div>

                <?php $p_attrs = $product->product_attributes()->get(); ?>
				<!-- 商品属性 -->
                <?php if (count($product_basic_attrs) > 0) : ?>
                <?php 
                    $attrs = array();
                    foreach ($p_attrs as $p_attr) {
                        if ($p_attr->product_attr_key->is_package_attr == 0 && $p_attr->product_attr_key->is_sale_attr == 0) {
                            $attrs[$p_attr->product_attr_key->name] = $p_attr->product_attr_value->value;
                            $attr_ids[$p_attr->product_attr_key->name] = $p_attr->product_attr_value->id;
                        }
                    }
                ?>
				<div class="form-group">
					<label for="attributes">商品属性</label>
					<div class="row">
					<?php for ($i = 0; $i < count($product_basic_attrs); $i++) : ?>
					<?php if ($i != 0 && $i % 2 == 0) : ?>
						<div class="w-100"></div>
                    <?php endif; ?>
                        @if (array_key_exists($product_basic_attrs[$i]->name, $attrs))
						<div class="col">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">{{ $product_basic_attrs[$i]->name }}</span>
								</div>
								@if ($product_basic_attrs[$i]->name === "空间")
								<select name="attributes[prop_{{ $attr_ids[$product_basic_attrs[$i]->name] }}]" class="custom-select">
									<option value="卧室" @if ('卧室' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>卧室</option>
									<option value="餐厅" @if ('餐厅' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>餐厅</option>
									<option value="客厅" @if ('客厅' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>客厅</option>
									<option value="浴室" @if ('浴室' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>浴室</option>
									<option value="办公室" @if ('办公室' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>办公室</option>
									<option value="室外" @if ('室外' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>室外</option>
									<option value="书房" @if ('书房' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>书房</option>
									<option value="厨房" @if ('厨房' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>厨房</option>
									<option value="玄关" @if ('玄关' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>玄关</option>
									<option value="儿童" @if ('儿童' === $attrs[$product_basic_attrs[$i]->name]) selected @endif>儿童</option>
								</select>
                                @else
								<input name="attributes[prop_{{ $attr_ids[$product_basic_attrs[$i]->name] }}]" type="text" class="form-control" value="{{ $attrs[$product_basic_attrs[$i]->name] }}">
                                @endif
							</div>
                        </div>
                        @endif
					<?php endfor; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- 商品包装规格 -->
                <?php if (count($product_package_attrs) > 0) : ?>
                <?php 
                    $attrs = array();
                    foreach ($p_attrs as $p_attr) {
                        if ($p_attr->product_attr_key->is_package_attr == 1 && $p_attr->product_attr_key->is_sale_attr == 0) {
                            $attrs[$p_attr->product_attr_key->name] = $p_attr->product_attr_value->value;
                            $attr_ids[$p_attr->product_attr_key->name] = $p_attr->product_attr_value->id;
                        }
                    }
                ?>
				<div class="form-group">
					<label for="specs">商品包装规格</label>
					<div class="row">
					<?php for ($i = 0; $i < count($product_package_attrs); $i++) : ?>
					<?php if ($i != 0 && $i % 2 == 0) : ?>
						<div class="w-100"></div>
                    <?php endif; ?>
                        @if (array_key_exists($product_package_attrs[$i]->name, $attrs))
						<div class="col">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">{{ $product_package_attrs[$i]->name }}</span>
								</div>
								<input name="specs[prop_{{ $attr_ids[$product_package_attrs[$i]->name] }}]" type="text" class="form-control" value="{{ $attrs[$product_package_attrs[$i]->name] }}">
							</div>
                        </div>
                        @endif
					<?php endfor; ?>
					</div>
				</div>
				<?php endif; ?>

                <?php if (count($product_sale_attrs) > 0) : ?>
				<div class="form-group">
                    <label for="sales">商品销售规格</label>
                    <?php for ($i = 0; $i < count($product_sale_attrs); $i++) : ?>
                    <div class="form-group">
                        <label for="sale-colors">{{ $product_sale_attrs[$i]->name }}</label>
                        <input type="hidden" name="color-id" value="{{ $product_sale_attrs[$i]->id }}">
                        <?php $skus = $product->skus()->get(); ?>
                        @if (null !== $skus && sizeof($skus) > 0)
                        @foreach ($skus as $sku)
                        <div class="row mb-2">
                            {{ Form::hidden('color-skus[]', $sku->id) }}
                            <div class="col-md-1 pr-0">
                                <input type="checkbox" class="color-select" name="color-select[]" class="form-check" value="on" checked>
                            </div>
                            <div class="col-md-3 pr-0">
                                <input type="text" name="sale-colors[]" placeholder="输入主色" maxlength="30" class="form-control" value="{{ $sku->name }}">
                            </div>
                            <div class="col-md-3 pr-0">
                                <input type="text" name="color-price[]" placeholder="价格（元）" maxlength="30" class="form-control" value="{{ $sku->price }}">
                            </div>
                            <div class="col-md-2 pr-0">
                                <input type="text" name="color-quantity[]" placeholder="库存量" maxlength="30" class="form-control" value="{{ $sku->quantity }}">
                            </div>
                            <div class="col-md-3 pr-0">
                                <div>
                                    @if ($sku->featured_image == "")
                                    <img width="36" height="36" data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
                                    @else
                                    {{ Html::image('storage' . config('imgattrs.product_images.thumbnail') . '/thumb_' . $sku->featured_image, $sku->name, array('style'=>'width: 36px; height: 36px;')) }}
                                    @endif
                                    <a href="#" class="btn btn-primary btn-sm color-thumbnail-button" role="button">上传</a>
                                    <a href="#" class="btn btn-secondary btn-sm clear-color-thumbnail-button" role="button">删 除</a>
                                    {{ Form::file('color-images[]', array('class'=>'color-thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
                                    {{ Form::hidden('color-image-remarks[]', 0) }}
                                </div>	
                            </div>
                        </div>
                        @endforeach
                        @endif
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
                                    <a href="#" class="btn btn-primary btn-sm color-thumbnail-button" role="button">上传</a>
                                    <a href="#" class="btn btn-secondary btn-sm clear-color-thumbnail-button" role="button">删 除</a>
                                    {{ Form::file('color-images[]', array('class'=>'color-thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
                                    {{ Form::hidden('color-image-remarks[]', 0) }}
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
						<input name="price" type="text" class="form-control" value="{{ $product->price }}">
						<div class="input-group-append">
							<span class="input-group-text">元</span>
						</div>
					</div>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">库存</span>
						</div>
						<input type="text" class="form-control" name="quantity" value="{{ $product->quantity }}">
					</div>
				</div>

				<!-- 编辑器 -->
				<div class="post-editor form-group">
					<label for="content">商品描述</label>
					<textarea id="editor" name="content" class="form-control" rows="12">{!! html_entity_decode($product->description, ENT_QUOTES, 'UTF-8') !!}</textarea>
				</div>

				<!-- 商品图 -->
				<div class="card form-group">
					<div class="card-header">商品图片</div>
					<div class="card-body">
						<div class="pro-pic">
                            <?php 
                                $images = $product->images()->get();
                                $image_count = count($images);
                                $image_index = 1;
                                $image_checked = false;
                            ?>
                            <div class="row">
								@if ($image_count > 0)
								@foreach ($images as $image)

								<div class="col-md-3">
									<div class="thumbnail">
                                        {{ Html::image('storage' . config('imgattrs.product_images.thumbnail') . '/thumb_' . $image->path, $product->name) }}
                                        <div class="caption clearfix">
											<div class="radio">
												<label>
													<input type="radio" name="cover" id="cover{{ $image_index }}" value="{{ $image_index }}" <?php if ($product->featured_image == $image->path) { $image_checked = true; echo 'checked'; } ?>>
													特色图片
												</label>
											</div>
											<p>
                                                <a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
                                                <a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
                                                {{ Form::file('thumb[]', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
												{{ Form::hidden('thumbname[]', $image->path) }}
												{{ Form::hidden('thumbflag[]', 0) }}
											</p>
										</div>
									</div>
								</div>
								
								<?php $image_index++; ?>
								@endforeach
								@endif

								@for ($i = 0; $i < 4 - $image_count; $i++)

								<div class="col-md-3">
									<div class="thumbnail">
										<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
										<div class="caption clearfix">
											<div class="radio">
												<label>
													<input type="radio" name="cover" id="cover{{ $image_count + $i + 1}}" value="{{ $image_count + $i + 1}}" <?php if (!$image_checked) { $image_checked = true; echo 'checked'; } ?>>
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

								@endfor
							</div>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-header">列表页封面图</div>
					<div class="card-body">
						<div class="pro-pic">
							<div class="row">
								<div class="col-md-3">
									<div class="thumbnail">
										@if ($product->poster == "")
										<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
										@else
										{{ Html::image('storage' . config('imgattrs.product_images.thumbnail') . '/thumb_' . $product->poster, $product->name) }}
										@endif
										<div class="caption clearfix">
											<p>
												<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
												<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
												{{ Form::file('poster', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;', 'data-type'=>'poster')) }}
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
							<input name="status" type="radio" id="inlineRadio1" value="draft" @if ('draft' === $product->state) checked @endif> 待审
							</label>
							<label class="radio-inline">
							<input name="status" type="radio" id="inlineRadio2" value="active" @if ('active' === $product->state) checked @endif> 通过
							</label>
						</div>
					</div>				
				</div>
				<!-- 首页配置 -->
				<div class="card mb-3">
					<div class="card-header">首页配置</div>
					<div class="card-body">
						<div class="form-check mb-3">
							<input type="checkbox" class="form-check-input" name="is_featured" id="featured-product" @if (1 === $product->is_featured) checked @endif>
							<label for="featured-product" class="form-check-label">推送到首页买手推荐</label>
						</div>
						<div class="form-check">
							<input type="checkbox" class="form-check-input" name="is_waterfalled" id="waterfalled-product" @if (1 === $product->is_waterfalled) checked @endif>
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
				if (_this.data('type') == 'poster') {
					$('input[name="update-poster"]').val(1);
				}
				else {
					$('input[name="update-thumbnail"]').val(1);
					 _this.siblings('input[name="thumbflag[]"]').val(1);
				}

				var cover = _this.parent().parent().find('input[name="cover"]').filter(':checked').val();
				if (cover !== undefined) {
					$('input[name="covername"]').val('');
				}

				return;
			}

			if (/^image/.test(files[0].type)){
				var reader = new FileReader();
				reader.readAsDataURL(files[0]);

				reader.onloadend = function(){
					_this.parent().parent().parent().find('img').attr('src', this.result);
				}
			}

			if (_this.data('type') == 'poster') {
				$('input[name="update-poster"]').val(1);
			}
			else {
				$('input[name="update-thumbnail"]').val(1);
				_this.siblings('input[name="thumbflag[]"]').val(1);
			}

			var cover = _this.parent().parent().find('input[name="cover"]').filter(':checked').val();
			if (cover !== undefined) {
				$('input[name="covername"]').val($(this).val().split('\\').pop());
			}
		});

        $('.color-thumbnail-button').on('click', function(e){
			e.preventDefault();
			$(this).parent().find('.color-thumb').click();
		});

		$('.clear-color-thumbnail-button').on('click', function(e){
			e.preventDefault();
			$(this).parent().find('.color-thumb').wrap('<form>').parent('form').trigger('reset');
			$(this).parent().find('.color-thumb').unwrap();
			$(this).parent().find('.color-thumb').trigger('change');
		});

		$('.color-thumb').on('change', function(){
			var _this = $(this);
			var files = !!this.files ? this.files : [];
			if (!files.length || !window.FileReader) {
				_this.parent().parent().parent().find('img').attr('src', "{{ URL::asset('images/pic.png') }}");
                $('input[name="update-sales"]').val(1);
                _this.parent().find('input[name*="color-image-remarks"]').val(1);

				return;
			}

			if (/^image/.test(files[0].type)){
				var reader = new FileReader();
				reader.readAsDataURL(files[0]);

				reader.onloadend = function(){
					_this.parent().parent().parent().find('img').attr('src', this.result);
				}
			}

            $('input[name="update-sales"]').val(1);
            _this.parent().find('input[name*="color-image-remarks"]').val(1);
		});

        $('input[name="cover"]').on('change', function(){
			$('input[name="update-product"]').val(1);
			
			var filename = $(this).parent().parent().parent().find('.thumb').val().split('\\').pop();
			if (filename == "") {
				var thumbname = $(this).parent().parent().parent().find('input[name="thumbname[]"]');
				var thumbflag = $(this).parent().parent().parent().find('input[name="thumbflag[]"]');
				if (thumbname.length > 0 && thumbflag.val() != 1) {
					filename = thumbname.val();
					// filename = temp.substring(8, temp.length);
				}
			}

			$('input[name="covername"]').val(filename);
		});

        $('input[name*="attributes"]').on('change', function(){
			$('input[name="update-attributes"]').val(1);
		});

        $('select[name*="attributes"]').on('change', function(){
			$('input[name="update-attributes"]').val(1);
		});

        $('input[name*="specs"]').on('change', function(){
			$('input[name="update-specs"]').val(1);
		});

        $('input[name*="color"]').on('change', function(){
			$('input[name="update-sales"]').val(1);
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