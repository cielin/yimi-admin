@extends('_layout.default')

@section('title', '后台管理页面')

@section('page-content')

<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>商品列表</h2>
		</div>
		<div class="operate float-right">
			<form class="form-inline" role="form" action="">
				<div class="input-group">
					<div class="input-group-prepend">
						<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">
						查看
						</button>
						<div class="dropdown-menu">
							<a href="{{ URL::route('products.index') }}" class="dropdown-item">所有</a>
							<a href="{{ URL::route('products.getunprovedproducts') }}" class="dropdown-item">待审</a>
							<a href="{{ URL::route('products.getpublishedproducts') }}" class="dropdown-item">通过</a>
						</div>
					</div>
					<input name="s" @if (isset($s) && $s !== '') value="{{ $s }}" @endif type="text" class="form-control" id="header-search" placeholder="输入关键字" aria-label="输入关键字">
					<div class="input-group-append">
						<button type="submit" class="btn btn-outline-secondary">搜 索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Header Bar -->
<!-- Main Content -->
<div class="row main-content">
	<div class="col-md-12">
		<div class="card">
			<table class="table demo-table base-table table-striped">
				<thead>
					<tr>
						<th scope="col">缩略图</th>
						<th scope="col">商品名</th>
						<th scope="col">所属类别</th>
						<th scope="col">品牌</th>
						<th scope="col">状态</th>
						<th scope="col">发布时间</th>
						<th scope="col">更新时间</th>
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($products as $product)
					<tr>
						<td width="120">
							<a href="">
								<span class="img-avatar">
									<img src="{{ asset('storage/thumbs/products/thumb_' . $product->featured_image) }}" alt="{{ $product->name }}">
								</span>
							</a>
						</td>
						<td>{{ $product->name }}</td>
						<td>@if ($product->category_id !== 0) {{ $product->category->name }} @else 其他 @endif</td>
						<td>{{ $product->brand->name }}</td>
						<td>{{ $product->state }}</td>
						<td>{{ $product->created_at }}</td>
						<td>{{ $product->updated_at }}</td>
						<td>
							<a class="btn btn-primary btn-sm" href="{{ URL::route('products.edit', $product->id) }}">编辑</a>
							<a class="btn btn-danger btn-sm remove-record" href="javascript:void(0);" data-toggle="modal" data-url="{{ route('products.destroy', $product) }}" data-id="{{ $product->id }}" data-target="#custom-width-modal">删除</a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>	
		<!-- nav footer -->
		<nav class="footer-nav">
			<?php echo $products->appends(request()->except('page'))->links(); ?>
		</nav>
	</div>
</div>
<!-- End Main Content -->

<!-- Delete Model -->
<form action="" method="POST" class="remove-record-model">
    <div id="custom-width-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="width:55%;">
            <div class="modal-content">
                <div class="modal-header">
					<span class="modal-title" id="custom-width-modalLabel">删除记录</span>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <p>确认删除该条记录吗？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default remove-data-from-delete-form" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">删除</button>
                </div>
            </div>
        </div>
    </div>
</form>

@stop

@section('js')

<script>
	$(document).ready(function() {
		$('.remove-record').click(function() {
			var id = $(this).attr('data-id');
			var url = $(this).attr('data-url');
			var token = "{{ csrf_token() }}";
			$(".remove-record-model").attr("action",url);
			$('body').find('.remove-record-model').append('<input name="_token" type="hidden" value="'+ token +'">');
			$('body').find('.remove-record-model').append('<input name="_method" type="hidden" value="DELETE">');
			$('body').find('.remove-record-model').append('<input name="id" type="hidden" value="'+ id +'">');
		});

		$('.remove-record-model').on('hide.bs.modal', function (event) {
			var modal = $(this);
			modal.find( "input" ).remove();
		});
	});
</script>

@stop
