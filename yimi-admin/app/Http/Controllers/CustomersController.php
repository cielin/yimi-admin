<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Customer;

class CustomersController extends Controller
{
	/**
	 * 默认列表页
	 */
	public function index(Request $request)
	{
		if ($request->has('s') && ($s = $request->input('s')) !== "") {
			$customers = Customer::where('nickname', 'like', '%' . $s . '%')
				->orderBy('updated_at', 'desc')
				->paginate(10);
			return View::make('customers.index')
				->with('customers', $customers)
				->with('s', $s);
		}
		else {
			$customers = Customer::orderBy('updated_at', 'desc')->paginate(10);
			return View::make('customers.index')->with('customers', $customers);
		}
	}

    /**
     * 异步获取客户列表
     * GET /customers
     *
     * @return Response
     */
    public function getCustomerBySearch(Request $request)
    {
    	$query = $request->get('q');
    	$page = $request->get('page');

    	$realname = DB::table('customers')
    		->select('id', 'nickname', 'email', 'realname', 'phone')
    		->where('realname', 'like', '%' . $query . '%');
    	$email = DB::table('customers')
    		->select('id', 'nickname', 'email', 'realname', 'phone')
    		->where('email', 'like', '%' . $query . '%');

    	$customers = DB::table('customers')
    		->select('id', 'nickname', 'email', 'realname', 'phone')
    		->where('nickname', 'like', '%' . $query . '%')
    		->union($realname)
    		->union($email)->get();

    	$result = array('total_count' => count($customers), 'items' => $customers->toArray());

    	return json_encode($result);
	}
	
	/**
	 * 异步获取用户收货地址列表
	 * GET /customers/getcustomeraddresses
	 * 
	 * @return Response
	 */
	public function getCustomerAddresses(Request $request)
	{
		$cid = intval($request->get('cid'));
		$customer = Customer::find($cid);
		$addresses = array();

		if ($customer) {
			$addrs = $customer->addresses()->get();
			if ($addrs && sizeof($addrs) > 0) {
				foreach ($addrs as $addr) {
					$text = $addr->consignee . ', ' . $addr->province . ', ' . $addr->district . ', ' . $addr->address . ', ' . $addr->mobile;
					$tmp = array('id' => $addr->id, 'text' => $text);
					array_push($addresses, $tmp);
				}
			}

			$result = array('errcode' => 0, 'msg' => 'success', 'data' => $addresses);
		}
		else {
			$result = array('errcode' => 1, 'msg' => '客户ID错误', 'data' => $addresses);
		}

		return json_encode($result);
	}
}
