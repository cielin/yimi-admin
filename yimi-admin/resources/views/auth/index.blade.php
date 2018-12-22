<!DOCTYPE html>
<html>
<head>
	<title>登录页</title>
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/layout.css') }}">
</head>
<body style="padding-top: 0;">
	<div class="login-wrap">
		<div class="container">
		{{ Form::open(array('route' => 'auth.login', 'class' => 'form-signin', 'role' => 'form')) }}
			<h2 class="form-signin-heading">{{ strtoupper(config('app.name')) }}</h2>
			<input name="email" type="text" class="form-control" placeholder="用户名" required="" autofocus="">
			<input name="password" type="password" class="form-control" placeholder="密码" required="">
			<div class="checkbox">
			<label>
			<input name="remember-me" type="checkbox" value="on"> 记住密码
			</label>
			</div>
			{{ Form::submit('登 录', array('class' => 'btn btn-lg btn-primary btn-block')) }}
		{{ Form::close() }}
		</div>
	</div>
</body>
</html>