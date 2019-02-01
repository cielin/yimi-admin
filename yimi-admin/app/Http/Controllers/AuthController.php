<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Krucas\Notification\Facades\Notification;

class AuthController extends Controller
{
	/**
	 * Display a listing of the resource
	 * GET login page
	 *
	 * @return  Response
	 */
	public function index()
	{
		return View::make('auth.index');
	}

	/**
	 * Login
	 */
	public function doLogin(Request $request) {
		$credentials = $request->only('email', 'password');
		$remember_me = $request->input('remember-me');

		try {
			if (Auth::attempt($credentials)) {
				return redirect()->intended('products');
			}
			return "wooha";
			// return Redirect::route('admin.products.index');
		}
		catch (Exception $e) {
			echo var_dump($e);
		}
		// catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
		// 	Notification::error("用户名为空。");
		// }
		// catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
		// 	Notification::error("密码为空。");
		// }
		// catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
		// 	Notification::error("用户未被激活。");
		// }
		// catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
		// 	Notification::error("用户名不存在。");
		// }
		// catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
		// 	Notification::error("用户名或密码错误。");
		// }

		return "woops";
		// return Redirect::back()->withInput(Input::except('password'));
	}

	/**
	 * Logout
	 */
	public function doLogout() {
		Auth::logout();

		return View::make('auth.index');
	}
}
