@extends('_layout.default')

@section('title', '后台管理页面')

@section('page-content')

<!-- Header Bar -->
<div class="row header">
	<div class="col-md-12">
		<div class="meta float-left">
			<h2>客户列表</h2>
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
						<th scope="col">头像</th>
						<th scope="col">用户名</th>
						<th scope="col">姓名</th>
						<th scope="col">电子邮箱</th>
						<th scope="col">手机号</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($customers as $customer)
					<tr>
						<td width="120">
							<a href="">
								<span class="img-avatar">
									@if (!is_null($customer->avatar))
									<img src="{{ asset('storage/thumbs/avatars/thumb_' . $customer->avatar) }}" alt="{{ $customer->nickname }}">
									@else
									<img src="{{ URL::asset('images/userNew.png') }}" alt="{{ $customer->nickname }}">
									@endif
								</span>
							</a>
						</td>
						<td>{{ $customer->nickname }}</td>
						<td>{{ $customer->realname }}</td>
						<td>{{ $customer->email }}</td>
						<td>{{ $customer->phone }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>	
		<!-- nav footer -->
		<nav class="footer-nav">
			<?php echo $customers->appends(request()->except('page'))->links(); ?>
		</nav>
	</div>
</div>
<!-- End Main Content -->

@stop
