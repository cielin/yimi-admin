@extends('_layout.default')

@section('title', '后台管理页面')

@section('page-content')

<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>Banner列表</h2>
		</div>
		<div class="operate float-right">
			<form class="form-inline" role="form" action="">
				<div class="input-group">
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
						<th scope="col"></th>
						<th scope="col">标题</th>
						<th scope="col">类型</th>
						<th scope="col">创建时间</th>
						<th scope="col">更新时间</th>
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($banners as $banner)
					<tr>
						<td width="120">
							<a href="">
								<span class="img-avatar">
									<img src="{{ asset('storage/thumbs/banners/thumb_' . $banner->image) }}" alt="{{ $banner->title }}">
								</span>
							</a>
						</td>
						<td>{{ $banner->title }}</td>
						<td>
							<?php
								switch ($banner->type) {
									case '0':
										echo "首页头图";
										break;
									case '1':
										echo "首页二屏左大图";
										break;
									case '2':
										echo "首页二屏右方图";
										break;
									case '3':
										echo "首页二屏右长图";
										break;
									
									default:
										echo "";
										break;
								}
							?>
						</td>
						<td>{{ $banner->created_at }}</td>
						<td>{{ $banner->updated_at }}</td>
						<td>
							<a class="btn btn-primary btn-sm" href="{{ URL::route('banners.edit', $banner->id) }}">编辑</a>
							<a class="btn btn-danger btn-sm remove-record" href="javascript:void(0);" data-toggle="modal" data-url="{{ route('banners.destroy', $banner) }}" data-id="{{ $banner->id }}" data-target="#custom-width-modal">删除</a>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>	
		<!-- nav footer -->
		<nav class="footer-nav">
			<?php echo $banners->appends(request()->except('page'))->links(); ?>
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
