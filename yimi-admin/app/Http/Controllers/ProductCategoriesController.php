<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Krucas\Notification\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\ProductCategory;

class ProductCategoriesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $productcategories = ProductCategory::where('name', 'like', '%' . $s . '%')
                ->orderBy('parent_id', 'asc')
                ->paginate(10);
            return View::make('productcategories.index')
                ->with('productcategories', $productcategories)
                ->with('s', $s);
        }
        else {
            $productcategories = ProductCategory::orderBy('parent_id', 'asc')->paginate(10);
            return View::make('productcategories.index')->with('productcategories', $productcategories);
        }
    }

    /**
     * 显示创建新商品类别页面
     * GET /productcategories/create
     *
     * @return Response
     */
    public function create()
    {
    	$productcategories = ProductCategory::where('depth', '<', 1)->get();

    	return View::make('productcategories.create')->with('productcategories', $productcategories);
    }

    /**
     * 实现新商品类别页面的表单存储
     * @param  Request $request 表单请求数据
     * @return Reponse
     */
    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'name' => 'required'
        ], [
            'name.required' => '标题不能为空。'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }
            
            return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $name = trim($request->input('name'));
        $slug = 'c' . Str::random(7);
        while (null !== ProductCategory::where('slug', $slug)->first()) {
            $slug = 'a' . Str::random(7);
        }
        $parent_id = $request->input('parent');

        try {
            $productCategory = new ProductCategory();
            $productCategory->name = $name;
            $productCategory->slug = $slug;
            $productCategory->parent_id = $parent_id;

            if ($parent_id === 0) {
            	$productCategory->depth = 0;
            }
            else {
            	$parent = ProductCategory::find($parent_id);
            	if ($parent) {
            		$productCategory->depth = $parent->depth + 1;
            	}
            	else {
            		$productCategory->depth = 0;
            	}
            }

            $productCategory->save();

            Notification::success('商品类别添加成功。');
            return Redirect::route('productcategories.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit($id)
    {
        $productCategory = ProductCategory::find($id);
        $productcategories = ProductCategory::where('depth', '<', 1)->get();

        return View::make('productcategories.edit')
            ->with('productcategories', $productcategories)
            ->with('productCategory', $productCategory);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ], [
            'name.required' => '标题不能为空。'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }

            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $name = trim($request->input('name'));
        $parent_id = $request->input('parent');

        try {
            $productCategory = ProductCategory::find($id);
            $productCategory->name = $name;
            $productCategory->parent_id = $parent_id;

            if ($parent_id === 0) {
                $productCategory->depth = 0;
            } else {
                $parent = ProductCategory::find($parent_id);
                if ($parent) {
                    $productCategory->depth = $parent->depth + 1;
                } else {
                    $productCategory->depth = 0;
                }
            }

            $productCategory->save();

            Notification::success('商品类别更新成功。');
            return Redirect::route('productcategories.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function destroy($id)
    {
        $productCategory = ProductCategory::find($id);
        
        try {
            $name = $productCategory->name;

            if (null!== $productCategory->children && sizeof($productCategory->children) > 0) {
                foreach ($productCategory->children as $pcc) {
                    foreach ($pcc->products as $pcc_p) {
                        $pcc_p->category_id = 0;
                        $pcc_p->save();
                    }
                    $pcc->delete();
                }
            }

            foreach ($productCategory->products as $pc_p) {
                $pc_p->category_id = 0;
                $pc_p->save();
            }
            $productCategory->delete();

            Notification::success('商品类别 "' . $name . '" 及其子类别删除成功。');
            return Redirect::route('productcategories.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }
}
