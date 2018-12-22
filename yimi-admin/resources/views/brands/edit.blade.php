@extends('_layout.default')

@section('title', '编辑品牌')

@section('css')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('jq-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/simditor.css') }}">

@stop

@section('page-content')

{{ Form::open(array('route' => array('brands.update', $brand), 'files' => true, 'role' => 'form')) }}
{{ Form::hidden('_method', 'PUT') }}
{{ Form::hidden('update-thumbnail', 0) }}
<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>编辑品牌</h2>
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
						<input name="name" type="text" class="form-control" placeholder="品牌名称" value="{{ $brand->name }}">
					</div>
					<div class="form-group input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" for="country">品牌所属国家</label>
						</div>
						<select class="custom-select" id="country" name="country">
							<option value="-1" @if ($brand->country === "-1") selected @endif>请选择一个国家...</option>
							<option value="阿尔及利亚" @if ($brand->country === "阿尔及利亚") selected @endif>阿尔及利亚</option>
							<option value="阿富汗" @if ($brand->country === "阿富汗") selected @endif>阿富汗</option>
							<option value="阿根廷" @if ($brand->country === "阿根廷") selected @endif>阿根廷</option>
							<option value="阿联酋" @if ($brand->country === "阿联酋") selected @endif>阿联酋</option>
							<option value="阿曼" @if ($brand->country === "阿曼") selected @endif>阿曼</option>
							<option value="阿塞拜疆" @if ($brand->country === "阿塞拜疆") selected @endif>阿塞拜疆</option>
							<option value="埃及" @if ($brand->country === "埃及") selected @endif>埃及</option>
							<option value="埃塞俄比亚" @if ($brand->country === "埃塞俄比亚") selected @endif>埃塞俄比亚</option>
							<option value="爱尔兰" @if ($brand->country === "爱尔兰") selected @endif>爱尔兰</option>
							<option value="安哥拉" @if ($brand->country === "安哥拉") selected @endif>安哥拉</option>
							<option value="奥地利" @if ($brand->country === "奥地利") selected @endif>奥地利</option>
							<option value="澳大利亚" @if ($brand->country === "澳大利亚") selected @endif>澳大利亚</option>
							<option value="巴布亚新几内亚" @if ($brand->country === "巴布亚新几内亚") selected @endif>巴布亚新几内亚</option>
							<option value="巴基斯坦" @if ($brand->country === "巴基斯坦") selected @endif>巴基斯坦</option>
							<option value="巴林" @if ($brand->country === "巴林") selected @endif>巴林</option>
							<option value="巴西" @if ($brand->country === "巴西") selected @endif>巴西</option>
							<option value="白俄罗斯" @if ($brand->country === "白俄罗斯") selected @endif>白俄罗斯</option>
							<option value="保加利亚" @if ($brand->country === "保加利亚") selected @endif>保加利亚</option>
							<option value="贝宁" @if ($brand->country === "贝宁") selected @endif>贝宁</option>
							<option value="比利时" @if ($brand->country === "比利时") selected @endif>比利时</option>
							<option value="冰岛" @if ($brand->country === "冰岛") selected @endif>冰岛</option>
							<option value="波黑" @if ($brand->country === "波黑") selected @endif>波黑</option>
							<option value="波兰" @if ($brand->country === "波兰") selected @endif>波兰</option>
							<option value="博茨瓦纳" @if ($brand->country === "博茨瓦纳") selected @endif>博茨瓦纳</option>
							<option value="朝鲜" @if ($brand->country === "朝鲜") selected @endif>朝鲜</option>
							<option value="赤道几内亚" @if ($brand->country === "赤道几内亚") selected @endif>赤道几内亚</option>
							<option value="大溪地" @if ($brand->country === "大溪地") selected @endif>大溪地</option>
							<option value="丹麦" @if ($brand->country === "丹麦") selected @endif>丹麦</option>
							<option value="德国" @if ($brand->country === "德国") selected @endif>德国</option>
							<option value="多哥" @if ($brand->country === "多哥") selected @endif>多哥</option>
							<option value="俄罗斯" @if ($brand->country === "俄罗斯") selected @endif>俄罗斯</option>
							<option value="厄瓜多尔" @if ($brand->country === "厄瓜多尔") selected @endif>厄瓜多尔</option>
							<option value="厄立特里亚" @if ($brand->country === "厄立特里亚") selected @endif>厄立特里亚</option>
							<option value="法国" @if ($brand->country === "法国") selected @endif>法国</option>
							<option value="菲律宾" @if ($brand->country === "菲律宾") selected @endif>菲律宾</option>
							<option value="斐济" @if ($brand->country === "斐济") selected @endif>斐济</option>
							<option value="芬兰" @if ($brand->country === "芬兰") selected @endif>芬兰</option>
							<option value="佛得角" @if ($brand->country === "佛得角") selected @endif>佛得角</option>
							<option value="刚果(布)" @if ($brand->country === "刚果(布)") selected @endif>刚果(布)</option>
							<option value="刚果(金)" @if ($brand->country === "刚果(金)") selected @endif>刚果(金)</option>
							<option value="高棉" @if ($brand->country === "高棉") selected @endif>高棉</option>
							<option value="古巴" @if ($brand->country === "古巴") selected @endif>古巴</option>
							<option value="圭亚那" @if ($brand->country === "圭亚那") selected @endif>圭亚那</option>
							<option value="哈萨克斯坦" @if ($brand->country === "哈萨克斯坦") selected @endif>哈萨克斯坦</option>
							<option value="韩国" @if ($brand->country === "韩国") selected @endif>韩国</option>
							<option value="荷兰" @if ($brand->country === "荷兰") selected @endif>荷兰</option>
							<option value="吉尔吉斯斯坦" @if ($brand->country === "吉尔吉斯斯坦") selected @endif>吉尔吉斯斯坦</option>
							<option value="几内亚" @if ($brand->country === "几内亚") selected @endif>几内亚</option>
							<option value="加拿大" @if ($brand->country === "加拿大") selected @endif>加拿大</option>
							<option value="加纳" @if ($brand->country === "加纳") selected @endif>加纳</option>
							<option value="柬埔寨" @if ($brand->country === "柬埔寨") selected @endif>柬埔寨</option>
							<option value="捷克" @if ($brand->country === "捷克") selected @endif>捷克</option>
							<option value="津巴布韦" @if ($brand->country === "津巴布韦") selected @endif>津巴布韦</option>
							<option value="喀麦隆" @if ($brand->country === "喀麦隆") selected @endif>喀麦隆</option>
							<option value="卡塔尔" @if ($brand->country === "卡塔尔") selected @endif>卡塔尔</option>
							<option value="科威特" @if ($brand->country === "科威特") selected @endif>科威特</option>
							<option value="肯尼亚" @if ($brand->country === "肯尼亚") selected @endif>肯尼亚</option>
							<option value="老挝" @if ($brand->country === "老挝") selected @endif>老挝</option>
							<option value="黎巴嫩" @if ($brand->country === "黎巴嫩") selected @endif>黎巴嫩</option>
							<option value="立陶宛" @if ($brand->country === "立陶宛") selected @endif>立陶宛</option>
							<option value="利比亚" @if ($brand->country === "利比亚") selected @endif>利比亚</option>
							<option value="罗马尼亚" @if ($brand->country === "罗马尼亚") selected @endif>罗马尼亚</option>
							<option value="马达加斯加" @if ($brand->country === "马达加斯加") selected @endif>马达加斯加</option>
							<option value="马来西亚" @if ($brand->country === "马来西亚") selected @endif>马来西亚</option>
							<option value="马里" @if ($brand->country === "马里") selected @endif>马里</option>
							<option value="马其顿" @if ($brand->country === "马其顿") selected @endif>马其顿</option>
							<option value="毛里求斯" @if ($brand->country === "毛里求斯") selected @endif>毛里求斯</option>
							<option value="毛里塔尼亚" @if ($brand->country === "毛里塔尼亚") selected @endif>毛里塔尼亚</option>
							<option value="美国" @if ($brand->country === "美国") selected @endif>美国</option>
							<option value="蒙古" @if ($brand->country === "蒙古") selected @endif>蒙古</option>
							<option value="孟加拉国" @if ($brand->country === "孟加拉国") selected @endif>孟加拉国</option>
							<option value="秘鲁" @if ($brand->country === "秘鲁") selected @endif>秘鲁</option>
							<option value="缅甸" @if ($brand->country === "缅甸") selected @endif>缅甸</option>
							<option value="摩洛哥" @if ($brand->country === "摩洛哥") selected @endif>摩洛哥</option>
							<option value="摩洛哥公国" @if ($brand->country === "摩洛哥公国") selected @endif>摩洛哥公国</option>
							<option value="莫桑比克" @if ($brand->country === "莫桑比克") selected @endif>莫桑比克</option>
							<option value="墨西哥" @if ($brand->country === "墨西哥") selected @endif>墨西哥</option>
							<option value="纳米比亚" @if ($brand->country === "纳米比亚") selected @endif>纳米比亚</option>
							<option value="南非" @if ($brand->country === "南非") selected @endif>南非</option>
							<option value="尼泊尔" @if ($brand->country === "尼泊尔") selected @endif>尼泊尔</option>
							<option value="尼日利亚" @if ($brand->country === "尼日利亚") selected @endif>尼日利亚</option>
							<option value="葡萄牙" @if ($brand->country === "葡萄牙") selected @endif>葡萄牙</option>
							<option value="日本" @if ($brand->country === "日本") selected @endif>日本</option>
							<option value="瑞典" @if ($brand->country === "瑞典") selected @endif>瑞典</option>
							<option value="瑞士" @if ($brand->country === "瑞士") selected @endif>瑞士</option>
							<option value="沙特阿拉伯" @if ($brand->country === "沙特阿拉伯") selected @endif>沙特阿拉伯</option>
							<option value="斯里兰卡" @if ($brand->country === "斯里兰卡") selected @endif>斯里兰卡</option>
							<option value="斯洛伐克" @if ($brand->country === "斯洛伐克") selected @endif>斯洛伐克</option>
							<option value="苏丹" @if ($brand->country === "苏丹") selected @endif>苏丹</option>
							<option value="塔吉克斯坦" @if ($brand->country === "塔吉克斯坦") selected @endif>塔吉克斯坦</option>
							<option value="泰国" @if ($brand->country === "泰国") selected @endif>泰国</option>
							<option value="坦桑尼亚" @if ($brand->country === "坦桑尼亚") selected @endif>坦桑尼亚</option>
							<option value="突尼斯" @if ($brand->country === "突尼斯") selected @endif>突尼斯</option>
							<option value="土耳其" @if ($brand->country === "土耳其") selected @endif>土耳其</option>
							<option value="土库曼斯坦" @if ($brand->country === "土库曼斯坦") selected @endif>土库曼斯坦</option>
							<option value="委内瑞拉" @if ($brand->country === "委内瑞拉") selected @endif>委内瑞拉</option>
							<option value="文莱" @if ($brand->country === "文莱") selected @endif>文莱</option>
							<option value="乌干达" @if ($brand->country === "乌干达") selected @endif>乌干达</option>
							<option value="乌克兰" @if ($brand->country === "乌克兰") selected @endif>乌克兰</option>
							<option value="乌拉圭" @if ($brand->country === "乌拉圭") selected @endif>乌拉圭</option>
							<option value="乌兹别克斯坦" @if ($brand->country === "乌兹别克斯坦") selected @endif>乌兹别克斯坦</option>
							<option value="西班牙" @if ($brand->country === "西班牙") selected @endif>西班牙</option>
							<option value="希腊" @if ($brand->country === "希腊") selected @endif>希腊</option>
							<option value="新加坡" @if ($brand->country === "新加坡") selected @endif>新加坡</option>
							<option value="新西兰" @if ($brand->country === "新西兰") selected @endif>新西兰</option>
							<option value="匈牙利" @if ($brand->country === "匈牙利") selected @endif>匈牙利</option>
							<option value="叙利亚" @if ($brand->country === "叙利亚") selected @endif>叙利亚</option>
							<option value="牙买加" @if ($brand->country === "牙买加") selected @endif>牙买加</option>
							<option value="亚美尼亚" @if ($brand->country === "亚美尼亚") selected @endif>亚美尼亚</option>
							<option value="也门" @if ($brand->country === "也门") selected @endif>也门</option>
							<option value="伊拉克" @if ($brand->country === "伊拉克") selected @endif>伊拉克</option>
							<option value="伊朗" @if ($brand->country === "伊朗") selected @endif>伊朗</option>
							<option value="以色列" @if ($brand->country === "以色列") selected @endif>以色列</option>
							<option value="意大利" @if ($brand->country === "意大利") selected @endif>意大利</option>
							<option value="印度" @if ($brand->country === "印度") selected @endif>印度</option>
							<option value="印尼" @if ($brand->country === "印尼") selected @endif>印尼</option>
							<option value="英国" @if ($brand->country === "英国") selected @endif>英国</option>
							<option value="约旦" @if ($brand->country === "约旦") selected @endif>约旦</option>
							<option value="越南" @if ($brand->country === "越南") selected @endif>越南</option>
							<option value="赞比亚" @if ($brand->country === "赞比亚") selected @endif>赞比亚</option>
							<option value="中国" @if ($brand->country === "中国") selected @endif>中国</option>
							<option value="中国澳门" @if ($brand->country === "中国澳门") selected @endif>中国澳门</option>
							<option value="中国台湾" @if ($brand->country === "中国台湾") selected @endif>中国台湾</option>
							<option value="中国香港" @if ($brand->country === "中国香港") selected @endif>中国香港</option>
						</select>
					</div>
				</div>

				<!-- 编辑器 -->
				<div class="post-editor form-group">
					<textarea id="editor" name="description" class="form-control" rows="12">{{ $brand->description }}</textarea>
				</div>

				<!-- 产品图 -->
				<div class="card">
					<div class="card-header">品牌Logo</div>
					<div class="card-body">
						<div class="pro-pic">
							<div class="row">
								<div class="col-md-3">
									<div class="thumbnail">
										@if ($brand->logo == "")
										<img data-src="{{ URL::asset('holder.js/300x300') }}" src="{{ URL::asset('images/pic.png') }}">
										@else
										{{ Html::image('storage' . config('imgattrs.brand_logo.root') . '/thumb_' . $brand->logo, $brand->name) }}
										@endif
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