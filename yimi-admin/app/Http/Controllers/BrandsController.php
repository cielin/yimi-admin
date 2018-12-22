<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Krucas\Notification\Facades\Notification;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Brand;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $brands = Brand::where('name', 'like', '%' . $s . '%')
                ->orderBy('name', 'asc')
                ->paginate(10);
            return View::make('brands.index')
                ->with('brands', $brands)
                ->with('s', $s);
        }
        else {
            $brands = Brand::orderBy('name', 'asc')->paginate(10);
            return View::make('brands.index')->with('brands', $brands);
        }
    }

    /**
     * 显示创建新品牌页面
     * GET /brands/create
     *
     * @return Response
     */
    public function create()
    {
    	return View::make('brands.create');
    }

    /**
     * 实现新品牌页面的表单存储
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
        $slug = 'b' . Str::random(7);
        while (null !== Brand::where('slug', $slug)->first()) {
            $slug = 'a' . Str::random(7);
        }
        $country = trim($request->input('country'));
        $description = trim($request->input('description'));
        $logo = "";

        if ($request->hasFile('logo')) {
            if ($request->file('logo')->isValid()) {
                $logo_file = $request->file('logo');
                $logo_file_path = $logo_file->store('images/brands');

                $path_parts = pathinfo($logo_file_path);
                $logo = $path_parts['basename'];
                $fullname = 'thumb_' . $logo;
                $image_width = Image::make(storage_path('app/public/' . $logo_file_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $logo_file_path))->height();

                if ($image_width > $image_height) {
                    $image = Image::make(storage_path('app/public/' . $logo_file_path))
                        ->resize(config('imgattrs.brand_logo.width'), null, function($constraint) {
                            $constraint->aspectRatio();
                        });
                    Storage::put(config('imgattrs.brand_logo.root') . '/' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $logo_file_path))
                        ->resize(null, config('imgattrs.brand_logo.height'), function($constraint) {
                            $constraint->aspectRatio();
                        });
                    Storage::put(config('imgattrs.brand_logo.root') . '/' . $fullname, $image->stream()->__toString());
                }
            }
        }

        try {
            $brand = new Brand();
            $brand->name = $name;
            $brand->slug = $slug;
            $brand->logo = $logo;
            $brand->country = $country;
            $brand->description = $description;
            $brand->save();

            Notification::success('品牌添加成功。');
            return Redirect::route('brands.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit(Brand $brand)
    {
        return View::make('brands.edit')->with('brand', $brand);
    }

    public function update(Request $request, Brand $brand)
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
        $country = trim($request->input('country'));
        $description = trim($request->input('description'));
        $logo = "";
        $update_thumbnail = $request->input('update-thumbnail');

        if ($update_thumbnail == 1) {
            if ($request->hasFile('logo')) {
                if ($request->file('logo')->isValid()) {
                    $logo_file = $request->file('logo');
                    $logo_file_path = $logo_file->store('images/brands');

                    $path_parts = pathinfo($logo_file_path);
                    $logo = $path_parts['basename'];
                    $fullname = 'thumb_' . $logo;
                    $image_width = Image::make(storage_path('app/public/' . $logo_file_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $logo_file_path))->height();

                    if ($image_width > $image_height) {
                        $image = Image::make(storage_path('app/public/' . $logo_file_path))
                            ->resize(config('imgattrs.brand_logo.width'), null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        Storage::put(config('imgattrs.brand_logo.root') . '/' . $fullname, $image->stream()->__toString());
                    } else {
                        $image = Image::make(storage_path('app/public/' . $logo_file_path))
                            ->resize(null, config('imgattrs.brand_logo.height'), function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        Storage::put(config('imgattrs.brand_logo.root') . '/' . $fullname, $image->stream()->__toString());
                    }
                }
            }
        }

        try {
            $brand->name = $name;
            if ($logo !== "") {
                $brand->logo = $logo;
            }
            $brand->country = $country;
            $brand->description = $description;
            $brand->save();

            Notification::success('品牌更新成功。');
            return Redirect::route('brands.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $name = $brand->name;
            $brand->delete();

            Notification::success('品牌 "' . $name . '" 删除成功。');
            return Redirect::route('brands.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }
}
