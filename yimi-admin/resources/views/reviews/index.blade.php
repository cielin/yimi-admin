@extends('_layout.default')

@section('title', '后台管理页面')

@section('page-content')

<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>评论列表</h2>
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
			<table class="table base-table table-striped">
				<thead>
					<tr>
                        <th scope="col">订单号</th>
                        <th scope="col">商品名</th>
				    	<th scope="col">内容</th>
						<th scope="col">作者</th>
						<th scope="col">状态</th>
						<th scope="col">发布时间</th>
						<th scope="col">更新时间</th>
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					@if (isset($reviews) && count($reviews) > 0)
					@foreach ($reviews as $review)
					<tr>
                        <td>{{ $review->order_id }}</td>
                        <td><a href="{{ env('SITE_BASE_URL') . 'products/' . $review->product->slug }}" target="_blank">{{ $review->product->name }}</a></td>
						@if ($review->images !== "" && $review->images !== null)
                        <td>
                            <div>{{ $review->content }}</div>
                            <div>
                                <style>
                                    .discuss-img-list {
                                        margin-top: 20px;
                                    }
                                    .discuss-img-list li {
                                        float: left;
                                        height: 48px;
                                        margin-right: 10px;
                                        overflow: hidden;
                                    }
                                    .discuss-img-list li img {
                                        width: auto;
                                        height: 48px;
                                    }
                                </style>
                                <ul class="discuss-img-list clearfix">
                                    <?php $rimgs = explode(",", $review->images); ?>
                                    @foreach ($rimgs as $rimg)
                                    <li>
                                        <img src="{{ asset('storage/thumbs/comments/thumb_' . $rimg) }}">
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                        @else
                        <td>{{ $review->content }}</td>
                        @endif
						<td>{{ $review->customer->nickname }}</td>
						<td class="review-status">@if ($review->status === 0) 未通过 @else 已通过 @endif</td>
						<td>{{ $review->created_at }}</td>
						<td>{{ $review->updated_at }}</td>
						<td>
                            @if ($review->status === 0)
							<div class="btn btn-primary btn-sm btn-do-review" data-id="{{ $review->id }}" data-action="unban">通过</div>
                            @else
							<div class="btn btn-danger btn-sm btn-do-review" data-id="{{ $review->id }}" data-action="ban">禁止</div>
                            @endif
                        </td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table>
		</div>	
		<!-- nav footer -->
		<nav class="footer-nav">
			<?php echo $reviews->appends(request()->except('page'))->links(); ?>
		</nav>
	</div>
</div>
<!-- End Main Content -->

@stop

@section('js')

<script>
	$(document).ready(function() {
		$('.btn-do-review').on('click', function(e){
            e.preventDefault();
            var url = "{{ route('admin.reviews.do') }}";
            var id = $(this).data('id');
            var action = $(this).data('action');
            
            $.ajax({
                type: "POST",
                url: url,
                dataType : "json",
                async:false,
                data: {
                    id: id,
                    action: action
                },
                context: this,
                success: function(response) {
                    if (response.errcode == 0) {
                        if (action == "unban") {
                            $(this).html("禁止");
                            $(this).removeClass('btn-primary').addClass('btn-danger');
                            $(this).data('action', 'ban');
                            $(this).parent().parent().find('.review-status').html('已通过');
                        } else {
                            $(this).html("通过");
                            $(this).removeClass('btn-danger').addClass('btn-primary');
                            $(this).data('action', 'unban');
                            $(this).parent().parent().find('.review-status').html('未通过');
                        }

                        return false;
                    }
                }
            });
        })
	});
</script>

@stop
