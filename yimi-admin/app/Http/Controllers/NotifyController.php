<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Krucas\Notification\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use App\Order;
use App\OrderItem;
use App\Product;
use App\Notify;
use App\OrderStatusHistories;

class NotifyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $notifys = Notify::where('content', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('notifys.index')
                ->with('notifys', $notifys)
                ->with('s', $s);
        }
        else {
			$notifys = Notify::orderBy('updated_at', 'desc')->paginate(10);
			return View::make('notifys.index')->with('notifys', $notifys);
		}
    }

    public function create()
    {
    	return View::make('notifys.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'customer' => 'required',
        ], [
            'content.required' => '请输入消息。',
            'customer.required' => '请选择客户。',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }
        $customer = $request->input('customer');
        $content = $request->input('content');
        try {
            $order = new Notify();
            $order->customer_id = $customer;
            $order->status = 0;
            $order->content = strip_tags($content);
            $order->save();
            Notification::success('消息添加成功。');
            return Redirect::route('notifys.index');
        } catch(\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit(Notify $notify)
    {
        $notify = Notify::where('id', $notify->id)
            ->orderBy('created_at', 'desc')
            ->first();
        return View::make('notifys.edit')->with('notify', $notify);
    }
	
    public function destroy(Notify $Notify)
    {
        try {
            $id = $notify->id;
            $notify->delete();

            Notification::success('信息删除成功。');
            return Redirect::route('notifys.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function update(Request $request, Notify $notify)
    {
        $old_customer = $request->input('old-customer');
		$old_content = $request->input('old-content');
		$customer = $request->input('customer');
        $content = $request->input('content');
		if ($customer && $customer!=$old_customer) {
			$notify->status = 0;
			$notify->customer_id = $customer;
		}
		if ($content && $content!=$old_content) {
			$notify->status = 0;
			$notify->content = strip_tags($content);
		}
        try {
            $notify->save();
        } catch(\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }

        Notification::success('信息修改成功。');
        return Redirect::route('notifys.index');
    }




}
