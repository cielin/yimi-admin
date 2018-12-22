@extends('_layout.default')

@section('title', '添加品牌')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/simditor.css') }}">

@stop

@section('page-content')

{{ Form::open(array('route' => 'brands.store', 'files' => true, 'role' => 'form')) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>添加品牌</h2>
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
						<input name="name" type="text" class="form-control" placeholder="品牌名称">
					</div>
					<div class="form-group input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" for="country">品牌所属国家</label>
						</div>
						<select class="custom-select" id="country" name="country">
							<option value="-1" selected>请选择一个国家...</option>
							<option value="阿尔及利亚">阿尔及利亚</option>
							<option value="阿富汗">阿富汗</option>
							<option value="阿根廷">阿根廷</option>
							<option value="阿联酋">阿联酋</option>
							<option value="阿曼">阿曼</option>
							<option value="阿塞拜疆">阿塞拜疆</option>
							<option value="埃及">埃及</option>
							<option value="埃塞俄比亚">埃塞俄比亚</option>
							<option value="爱尔兰">爱尔兰</option>
							<option value="安哥拉">安哥拉</option>
							<option value="奥地利">奥地利</option>
							<option value="澳大利亚">澳大利亚</option>
							<option value="巴布亚新几内亚">巴布亚新几内亚</option>
							<option value="巴基斯坦">巴基斯坦</option>
							<option value="巴林">巴林</option>
							<option value="巴西">巴西</option>
							<option value="白俄罗斯">白俄罗斯</option>
							<option value="保加利亚">保加利亚</option>
							<option value="贝宁">贝宁</option>
							<option value="比利时">比利时</option>
							<option value="冰岛">冰岛</option>
							<option value="波黑">波黑</option>
							<option value="波兰">波兰</option>
							<option value="博茨瓦纳">博茨瓦纳</option>
							<option value="朝鲜">朝鲜</option>
							<option value="赤道几内亚">赤道几内亚</option>
							<option value="大溪地">大溪地</option>
							<option value="丹麦">丹麦</option>
							<option value="德国">德国</option>
							<option value="多哥">多哥</option>
							<option value="俄罗斯">俄罗斯</option>
							<option value="厄瓜多尔">厄瓜多尔</option>
							<option value="厄立特里亚">厄立特里亚</option>
							<option value="法国">法国</option>
							<option value="菲律宾">菲律宾</option>
							<option value="斐济">斐济</option>
							<option value="芬兰">芬兰</option>
							<option value="佛得角">佛得角</option>
							<option value="刚果(布)">刚果(布)</option>
							<option value="刚果(金)">刚果(金)</option>
							<option value="高棉">高棉</option>
							<option value="古巴">古巴</option>
							<option value="圭亚那">圭亚那</option>
							<option value="哈萨克斯坦">哈萨克斯坦</option>
							<option value="韩国">韩国</option>
							<option value="荷兰">荷兰</option>
							<option value="吉尔吉斯斯坦">吉尔吉斯斯坦</option>
							<option value="几内亚">几内亚</option>
							<option value="加拿大">加拿大</option>
							<option value="加纳">加纳</option>
							<option value="柬埔寨">柬埔寨</option>
							<option value="捷克">捷克</option>
							<option value="津巴布韦">津巴布韦</option>
							<option value="喀麦隆">喀麦隆</option>
							<option value="卡塔尔">卡塔尔</option>
							<option value="科威特">科威特</option>
							<option value="肯尼亚">肯尼亚</option>
							<option value="老挝">老挝</option>
							<option value="黎巴嫩">黎巴嫩</option>
							<option value="立陶宛">立陶宛</option>
							<option value="利比亚">利比亚</option>
							<option value="罗马尼亚">罗马尼亚</option>
							<option value="马达加斯加">马达加斯加</option>
							<option value="马来西亚">马来西亚</option>
							<option value="马里">马里</option>
							<option value="马其顿">马其顿</option>
							<option value="毛里求斯">毛里求斯</option>
							<option value="毛里塔尼亚">毛里塔尼亚</option>
							<option value="美国">美国</option>
							<option value="蒙古">蒙古</option>
							<option value="孟加拉国">孟加拉国</option>
							<option value="秘鲁">秘鲁</option>
							<option value="缅甸">缅甸</option>
							<option value="摩洛哥">摩洛哥</option>
							<option value="摩洛哥公国">摩洛哥公国</option>
							<option value="莫桑比克">莫桑比克</option>
							<option value="墨西哥">墨西哥</option>
							<option value="纳米比亚">纳米比亚</option>
							<option value="南非">南非</option>
							<option value="尼泊尔">尼泊尔</option>
							<option value="尼日利亚">尼日利亚</option>
							<option value="葡萄牙">葡萄牙</option>
							<option value="日本">日本</option>
							<option value="瑞典">瑞典</option>
							<option value="瑞士">瑞士</option>
							<option value="沙特阿拉伯">沙特阿拉伯</option>
							<option value="斯里兰卡">斯里兰卡</option>
							<option value="斯洛伐克">斯洛伐克</option>
							<option value="苏丹">苏丹</option>
							<option value="塔吉克斯坦">塔吉克斯坦</option>
							<option value="泰国">泰国</option>
							<option value="坦桑尼亚">坦桑尼亚</option>
							<option value="突尼斯">突尼斯</option>
							<option value="土耳其">土耳其</option>
							<option value="土库曼斯坦">土库曼斯坦</option>
							<option value="委内瑞拉">委内瑞拉</option>
							<option value="文莱">文莱</option>
							<option value="乌干达">乌干达</option>
							<option value="乌克兰">乌克兰</option>
							<option value="乌拉圭">乌拉圭</option>
							<option value="乌兹别克斯坦">乌兹别克斯坦</option>
							<option value="西班牙">西班牙</option>
							<option value="希腊">希腊</option>
							<option value="新加坡">新加坡</option>
							<option value="新西兰">新西兰</option>
							<option value="匈牙利">匈牙利</option>
							<option value="叙利亚">叙利亚</option>
							<option value="牙买加">牙买加</option>
							<option value="亚美尼亚">亚美尼亚</option>
							<option value="也门">也门</option>
							<option value="伊拉克">伊拉克</option>
							<option value="伊朗">伊朗</option>
							<option value="以色列">以色列</option>
							<option value="意大利">意大利</option>
							<option value="印度">印度</option>
							<option value="印尼">印尼</option>
							<option value="英国">英国</option>
							<option value="约旦">约旦</option>
							<option value="越南">越南</option>
							<option value="赞比亚">赞比亚</option>
							<option value="中国">中国</option>
							<option value="中国澳门">中国澳门</option>
							<option value="中国台湾">中国台湾</option>
							<option value="中国香港">中国香港</option>
						</select>
					</div>
				</div>

				<!-- 编辑器 -->
				<div class="post-editor form-group">
					<textarea id="editor" name="description" class="form-control" rows="12"></textarea>
				</div>

				<!-- 产品图 -->
				<div class="card">
					<div class="card-header">品牌Logo</div>
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
												{{ Form::file('logo', array('class'=>'thumb', 'style'=>'width: 0; height: 0; visibility: hidden;')) }}
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
				placeholder: '品牌简介',
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
	})
</script>

@stop