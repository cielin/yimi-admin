@extends('_layout.default')

@section('title', '编辑Banner')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/simditor.css') }}">

@stop

@section('page-content')

{{ Form::open(array('route' => array('banners.update', $banner), 'files' => true, 'role' => 'form')) }}
{{ Form::hidden('_method', 'PUT') }}
{{ Form::hidden('update-thumbnail', 0) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>编辑Banner</h2>
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
						<input name="title" type="text" class="form-control" placeholder="Banner标题" value="{{ $banner->title }}">
					</div>
					<div class="form-group input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" for="type">Banner类型</label>
						</div>
						<select class="custom-select" id="type" name="type">
							<option value="0" @if ($banner->type == 0) selected @endif>首页头图</option>
							<option value="1" @if ($banner->type == 1) selected @endif>首页二屏左大图</option>
							<option value="2" @if ($banner->type == 2) selected @endif>首页二屏右方图</option>
							<option value="3" @if ($banner->type == 3) selected @endif>首页二屏右长图</option>
						</select>
					</div>
					<div class="form-group">
						<input name="link" type="text" class="form-control" placeholder="图片链接" value="{{ $banner->link }}">
					</div>
				</div>

				<!-- 编辑器 -->
				<div class="post-editor form-group">
					<textarea id="editor" name="description" class="form-control" rows="12">{{ $banner->description }}</textarea>
				</div>

				<!-- 产品图 -->
				<div class="card">
					<div class="card-header">Banner图</div>
					<div class="card-body">
						<div class="pro-pic">
							<div class="row">
								<div class="col-md-3">
									<div class="thumbnail">
                                        @if ($banner->image == "")
										<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
										@else
										{{ Html::image('storage' . config('imgattrs.banner_images.thumbnail') . '/thumb_' . $banner->image, $banner->title) }}
										@endif
										<div class="caption clearfix">
											<p>
												<a href="#" class="btn btn-secondary btn-sm float-left clear-thumbnail-button" role="button">删 除</a>
												<a href="#" class="btn btn-primary btn-sm float-right thumbnail-button" role="button">上 传</a>
												{{ Form::file('image', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
											</p>
										</div>
									</div>
								</div>
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
				placeholder: 'Banner描述',
				toolbar: false,
				pasteImage: false
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
                $('input[name="update-thumbnail"').val(1);
				return;
			}

			if (/^image/.test(files[0].type)){
				var reader = new FileReader();
				reader.readAsDataURL(files[0]);

				reader.onloadend = function(){
					_this.parent().parent().parent().find('img').attr('src', this.result);
				}
			}

            $('input[name="update-thumbnail"').val(1);
		});
	})
</script>

@stop