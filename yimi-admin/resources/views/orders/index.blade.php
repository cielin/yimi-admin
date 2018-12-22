@extends('_layout.default')

@section('title', '后台管理页面')

@section('page-content')

<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>订单列表</h2>
		</div>
		<div class="operate float-right">
			<form class="form-inline" role="form" action="">
				<div class="input-group">
					<input name="s" @if (isset($s) && $s !== '') value="{{ $s }}" @endif type="text" class="form-control" id="header-search" placeholder="输入订单号" aria-label="输入订单号">
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
						<th scope="col">下单客户</th>
                        <th scope="col">状态</th>
                        <th scope="col">总价</th>
						<th scope="col">创建时间</th>
						<th scope="col">更新时间</th>
						<th scope="col"></th>
					</tr>
				</thead>
				<tbody>
					@if (isset($orders) && count($orders) > 0)
					<?php
						$E_ORDER_STATUS = array(
							'pending_payment' => '等待付款',
							'processing' => '处理中',
							'pending_ship' => '等待发货',
							'shipped' => '已发货',
							'on_hold' => '已冻结',
							'cancelled' => '已取消',
							'completed' => '已完成'
						);
					?>
					@foreach ($orders as $order)
					<tr>
						<td>{{ $order->order_code }}</td>
						<td>{{ $order->customer->nickname }} ...</td>
                        <td>{{ $E_ORDER_STATUS[$order->status] }}</td>
                        <td>{{ $order->total_price }}</td>
						<td>{{ $order->created_at }}</td>
						<td>{{ $order->updated_at }}</td>
						<td>
							<a class="btn btn-primary btn-sm" href="{{ URL::route('orders.edit', $order->id) }}">编辑</a>
						</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table>
		</div>	
		<!-- nav footer -->
		<nav class="footer-nav">
			<?php echo $orders->appends(request()->except('page'))->links(); ?>
		</nav>
	</div>
</div>
<!-- End Main Content -->

@stop
