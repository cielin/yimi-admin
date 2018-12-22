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
use App\OrderStatusHistories;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $orders = Order::where('order_code', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('orders.index')
                ->with('orders', $orders)
                ->with('s', $s);
        }
        else {
            $orders = Order::orderBy('updated_at', 'desc')->paginate(10);
            return View::make('orders.index')->with('orders', $orders);
        }
    }

    public function create()
    {
    	return View::make('orders.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required',
            'customer' => 'required',
            'customer-address' => 'required'
        ], [
            'products.required' => '请添加商品。',
            'customer.required' => '请选择客户。',
            'customer-address.required' => '请选择客户收货地址。'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }

            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $products = $request->input('products');
        $customer = $request->input('customer');
        $customer_address = $request->input('customer-address');
        $remarks = $request->input('remarks');
        $status = $request->input('status');
        $status_remarks = $request->input('status-remarks');
        $type = $request->input('order-type');
        $related = $request->input('order-related');
        $shipped_at = $request->input('shipped-at');

        if ($type === Order::UNITED && $related === null) {
            $error = "请选择关联订单，或将该订单类型设置为独立订单。";
            Notification::error($error);

            return Redirect::back()
                ->withErrors($error)
                ->withInput();
        }

        try {
            $order = new Order();
            $order->customer_id = $customer;
            $order->status = $status;
            $order->shipping_address = $customer_address;
            $order->shipped_at = $shipped_at;
            $order->type = $type;
            $order->order_code = 'W' . substr(strval(time()), 3, 7) . substr(strval($customer), strlen(strval($customer)) - 2, 2);
            $order->remarks = $remarks;

            $order->save();

            $total_price = 0;
            for ($i = 0; $i < sizeof($products['id']); ++$i) {
                $item = new OrderItem();
                $item->product_id = $products['id'][$i];
                $item->name = $products['name'][$i];
                $item->quantity = $products['quantity'][$i];
                $item->price = $products['price'][$i];
                $item->featured_image = $products['featured_image'][$i];

                $total_price += (intval($products['quantity'][$i]) * intval($products['price'][$i]));

                $order->items()->save($item);
            }

            $order->total_price = $total_price;
            $order->save();

            $orderStatus = new OrderStatusHistories();
            $orderStatus->state = $order->status;
            $orderStatus->remark = $status_remarks;
            $order->order_status_histories()->save($orderStatus);

            Notification::success('订单添加成功。');
            return Redirect::route('orders.index');
        } catch(\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit(Order $order)
    {
        $acient_status = OrderStatusHistories::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return View::make('orders.edit')->with('order', $order)
            ->with('acient_status', $acient_status);
    }

    public function update(Request $request, Order $order)
    {
        $update_items = $request->input('update-items');
        $update_status = $request->input('update-status');
        $update_shipping = $request->input('update-shipping');
        $update_remarks = $request->input('update-remarks');
        $status_remarks = $request->input('status-remarks');

        if ($update_status == 1) {
            try {
                $order->status = $request->input('status');

                $orderStatus = new OrderStatusHistories();
                $orderStatus->state = $order->status;
                $orderStatus->remark = $status_remarks;
                $order->order_status_histories()->save($orderStatus);
            } catch(\Exception $e) {
                Notification::error('错误: ' . $e->getMessage());
                return Redirect::back()->withInput();
            }
        }

        if ($order->status == Order::PENDING_PAYMENT) {
            if ($update_items == 1) {
                try {
                    $items = $order->items()->get();
                    $products = $request->input('products');
    
                    foreach ($items as $item) {
                        if (!in_array($item->product_id, $products['id'])) {
                            $item->delete();
                        }
                    }
    
                    $total_price = 0;
                    for ($i = 0; $i < sizeof($products['id']); ++$i) {
                        $item = OrderItem::where('order_id', $order->id)
                            ->where('product_id', $products['id'][$i])
                            ->first();
                        if (isset($item) && $item !== null) {
                            $item->quantity = $products['quantity'][$i];
                            $item->save();
                        }
                        else {
                            $item = new OrderItem();
                            $item->product_id = $products['id'][$i];
                            $item->name = $products['name'][$i];
                            $item->quantity = $products['quantity'][$i];
                            $item->price = $products['price'][$i];
                            $item->featured_image = $products['featured_image'][$i];
    
                            $order->items()->save($item);
                        }
                        
                        $total_price += (intval($products['quantity'][$i]) * intval($products['price'][$i]));
                    }
    
                    $order->total_price = $total_price;
                } catch(\Exception $e) {
                    Notification::error('错误: ' . $e->getMessage());
                    return Redirect::back()->withInput();
                }
            }
            if ($update_shipping == 1) {
                $order->customer_id = $request->input('customer');
                $order->shipping_address = $request->input('customer-address');
                $order->shipped_at = $request->input('shipped-at');
            }
            if ($update_remarks == 1) {
                $order->remarks = trim($request->input('remarks'));
            }
        } else {
            if ($update_shipping == 1) {
                $order->shipped_at = $request->input('shipped-at');
            }
            if ($update_remarks == 1) {
                $order->remarks = trim($request->input('remarks'));
            }
        }

        try {
            $order->save();
        } catch(\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }

        Notification::success('订单修改成功。');
        return Redirect::route('orders.index');
    }

    public function addProduct(Request $request)
    {
        $product_ids = $request->input('products');
        $result = array();

        if ($product_ids && sizeof($product_ids) > 0) {
            foreach ($product_ids as $product_id) {
                $product = Product::where('id', $product_id)->first();
                $item = array(
                    'id' => $product_id,
                    'featured_image' => $product->featured_image,
                    'name' => $product->name,
                    'count' => 1,
                    'price' => $product->price,
                    'total' => $product->price
                );
                array_push($result, $item);
            }

            $json = array(
                'errcode' => 0,
                'msg' => 'success',
                'products' => $result
            );
        }
        else {
            $json = array(
                'errcode' => 1,
                'msg' => '未提交有效的商品',
                'products' => $result
            );
        }        

        return json_encode($json);
    }

    public function getSiblingOrders(Request $request)
    {
        $cid = $request->input('cid');
        $out = array();

        $orders = Order::where('customer_id', $cid)
            // ->where('status', 'pending_payment')
            // ->where('type', 'united')
            // ->where('parent_id', '<>', 0)
            ->get();

        if ($orders && sizeof($orders) > 0) {
            foreach ($orders as $order) {
                $quantity = 0;
                foreach ($order->items as $item) {
                    $quantity += $item->quantity;
                }
                $text = $order->order_code . ', ' . $quantity . '件商品, 共¥' . $order->total_price . ', ' . $order->created_at;
                $tmp = array('id' => $order->id, 'text' => $text);
                array_push($out, $tmp);
            }

            $result = array('errcode' => 0, 'msg' => 'success', 'data' => $out);
        }
        else {
            $result = array('errcode' => 1, 'msg' => '未找到关联订单', 'data' => $out);
        }

        return json_encode($result);
    }
}
