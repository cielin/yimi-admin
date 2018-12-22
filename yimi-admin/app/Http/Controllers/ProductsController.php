<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Krucas\Notification\Facades\Notification;
use App\ProductCategory;
use App\Brand;
use App\ProductAttribute;
use App\ProductAttrKey;
use App\ProductAttrValue;
use App\ProductSku;
use App\Product;
use App\ProductImage;
use Html, Image, URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
	/**
	 * 显示商品列表
	 * GET /products
	 *
	 * @return Response
	 */
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $products = Product::where('name', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('products.index')
                ->with('products', $products)
                ->with('s', $s);
        }
        else {
            $products = Product::orderBy('updated_at', 'desc')->paginate(10);
            return View::make('products.index')->with('products', $products);
        }
    }

    /**
     * 显示创建新商品页面
     * GET /products/create
     *
     * @return Response
     */
    public function create()
    {
        $productcategories = ProductCategory::where('depth', '<', 1)->get();
        $brands = Brand::all();

        $product_attrs = ProductAttrKey::where('status', 1)->get();
        $product_basic_attrs = array();
        $product_package_attrs = array();
        $product_sale_attrs = array();
        foreach ($product_attrs as $product_attr) {
            if ($product_attr->is_package_attr === 0 && $product_attr->is_sale_attr === 0) {
                array_push($product_basic_attrs, $product_attr);
            }
            
            if ($product_attr->is_package_attr === 1) {
                array_push($product_package_attrs, $product_attr);
            }
            
            if ($product_attr->is_sale_attr === 1) {
                array_push($product_sale_attrs, $product_attr);
            }
        }

    	return View::make('products.create')
            ->with('productcategories', $productcategories)
            ->with('brands', $brands)
            ->with('product_basic_attrs', $product_basic_attrs)
            ->with('product_package_attrs', $product_package_attrs)
            ->with('product_sale_attrs', $product_sale_attrs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'brand' => 'required|integer'
        ], [
            'name.required' => '标题不能为空。',
            'brand.required' => '请选择商品品牌。',
            'brand.integer' => '请选择商品品牌。'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }
            
            return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
        }

        echo var_dump($validator->errors());
        return;

        $name = trim($request->input('name'));
        $slug = 'p' . Str::random(7);
        while (null !== Product::where('slug', $slug)->first()) {
            $slug = 'p' . Str::random(7);
        }
        $category = $request->input('category');
        $brand = $request->input('brand');
        $attributes = $request->input('attributes');
        $specs = $request->input('specs');

        $color_id = $request->input('color-id');
        $color_select = $request->input('color-select');
        $sale_colors = $request->input('sale-colors');
        $color_price = $request->input('color-price');
        $color_quantity = $request->input('color-quantity');
        $color_images = $request->file('color-images');

        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $content = Html::entities($request->input('content'));
        $cover = $request->input('cover');
        $status = $request->input('status');
        $thumbs = $request->file('thumb');
        $is_featured = $request->input('is_featured');
        $is_waterfalled = $request->input('is_waterfalled');
        $poster = "";

        $thumbnails = array();

        if (isset($thumbs) && count($thumbs) > 0) {
            $index = 1;
            foreach ($thumbs as $thumb) {
                if ($thumb->isValid()) {
                    $filename = $thumb->getClientOriginalName();
                    $filename = pathinfo($filename, PATHINFO_FILENAME);

                    $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $thumb->getClientOriginalExtension();
                    $uploaded_path = $thumb->storeAs(config('imgattrs.product_images.root'), $fullname);

                    $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                    if ($image_width > $image_height && 
                        $image_width/$image_height > config('imgattrs.product_images.width')/config('imgattrs.product_images.height')) {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(null, config('imgattrs.product_images.height'), function($constraint){
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                        Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }
                    else {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(config('imgattrs.product_images.width'), null, function($constraint){
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                        Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }

                    if ($uploaded_path) {
                        $thumb = $fullname;
                    }
                    else {
                        Notification::error("文件上传错误！");
                        return Redirect::back()->withInput();
                    }
                }
                else {
                    $thumb = "";
                }

                if ($thumb != "") {
                    array_push($thumbnails, $thumb);
                }

                if ($cover == $index++) {
                    $cover = $thumb;
                }
            }
        }
        else {
            $cover = NULL;
        }

        if ($request->hasFile('poster')) {
            if ($request->file('poster')->isValid()) {
                $poster_file = $request->file('poster');
                $filename = $poster_file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);

                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $poster_file->getClientOriginalExtension();
                $uploaded_path = $poster_file->storeAs(config('imgattrs.product_images.root'), $fullname);

                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                if ($image_width > $image_height &&
                    $image_width / $image_height > config('imgattrs.product_images.width') / config('imgattrs.product_images.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.product_images.height'), function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                    Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                } else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.product_images.width'), null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                    Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }

                if ($uploaded_path) {
                    $poster = $fullname;
                } else {
                    Notification::error("文件上传错误！");
                    return Redirect::back()->withInput();
                }
            }
        }

        try {
            $product = new Product();
            $product->name = $name;
            $product->slug = $slug;
            $product->brand_id = $brand;
            $product->category_id = $category;
            $product->price = $price;
            $product->quantity = $quantity;
            $product->description = $content;
            $product->state = $status;
            $product->featured_image = $cover;
            $product->poster = $poster;
            $product->is_featured = ($is_featured === 'on' ? 1 : 0);
            $product->is_waterfalled = ($is_waterfalled === 'on' ? 1 : 0);

            $product->save();

            foreach ($thumbnails as $thumb) {
                try {
                    $image = new ProductImage();
                    $image->path = $thumb;

                    $product->images()->save($image);
                }
                catch (\Exception $e) {
                    Notification::error('错误: ' . $e->getMessage());
                    return Redirect::back()->withInput();
                }
            }

            // 存储商品基础属性
            $product_basic_attr_ids = array();
            if ($attributes && sizeof($attributes) > 0) {
                foreach ($attributes as $key => $value) {
                    try {
                        list($key_name, $key_id) = explode("_", $key);
                        $product_attr_value = new ProductAttrValue();
                        $product_attr_value->product_attr_key_id = $key_id;
                        $product_attr_value->value = trim($value);
                        $product_attr_value->status = 1;
                        $product_attr_value->save();

                        $product_basic_attr_ids[$key_id . ":" . $product_attr_value->id] = 0;
                    } catch (\Exception $e) {
                        Notification::error('错误: ' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                }
            }

            // 存储商品包装属性
            if ($specs && sizeof($specs) > 0) {
                foreach ($specs as $key => $value) {
                    try {
                        list($key_name, $key_id) = explode("_", $key);
                        $product_attr_value = new ProductAttrValue();
                        $product_attr_value->product_attr_key_id = $key_id;
                        $product_attr_value->value = trim($value);
                        $product_attr_value->status = 1;
                        $product_attr_value->save();

                        $product_basic_attr_ids[$key_id . ":" . $product_attr_value->id] = 0;
                    } catch (\Exception $e) {
                        Notification::error('错误: ' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                }
            }      

            // 存储商品销售属性
            if ($color_select && count($color_select) > 0) {
                for ($i=0; $i < count($color_select); $i++) { 
                    if ($color_select[$i] === "on") {
                        try {
                            $product_attr_value = new ProductAttrValue();
                            $product_attr_value->product_attr_key_id = $color_id;
                            $product_attr_value->value = trim($sale_colors[$i]);
                            $product_attr_value->status = 1;
                            $product_attr_value->save();

                            $product_sku = new ProductSku();
                            $product_sku->attr_codes = $color_id . ":" . $product_attr_value->id;
                            $product_sku->name = trim($sale_colors[$i]);
                            $product_sku->price = trim($color_price[$i]);
                            $product_sku->quantity = trim($color_quantity[$i]);
                            $product_sku->featured_image = "";

                            if ($color_images[$i]->isValid()) {
                                $filename = $color_images[$i]->getClientOriginalName();
                                $filename = pathinfo($filename, PATHINFO_FILENAME);

                                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $color_images[$i]->getClientOriginalExtension();
                                $uploaded_path = $color_images[$i]->storeAs(config('imgattrs.product_images.root'), $fullname);

                                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                                if ($image_width > $image_height && 
                                    $image_width/$image_height > config('imgattrs.product_images.width')/config('imgattrs.product_images.height')) {
                                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                        ->resize(null, config('imgattrs.product_images.height'), function($constraint){
                                            $constraint->aspectRatio();
                                        })
                                        ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                                    Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                                }
                                else {
                                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                        ->resize(config('imgattrs.product_images.width'), null, function($constraint){
                                            $constraint->aspectRatio();
                                        })
                                        ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                                    Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                                }

                                if ($uploaded_path) {
                                    $product_sku->featured_image = $fullname;
                                }
                                else {
                                    Notification::error("文件上传错误！");
                                    return Redirect::back()->withInput();
                                }
                            }

                            $product->skus()->save($product_sku);
                            $product_basic_attr_ids[$color_id . ":" . $product_attr_value->id] = $product_sku->id;
                        }
                        catch (\Exception $e) {
                            Notification::error('错误: ' . $e->getMessage());
                            return Redirect::back()->withInput();
                        }
                    }
                }
            }

            // 存储产品属性表
            if (sizeof($product_basic_attr_ids) > 0) {
                foreach ($product_basic_attr_ids as $key => $value) {
                    $product_attribute = new ProductAttribute();
                    list($product_attribute->product_attr_key_id, $product_attribute->product_attr_value_id) = explode(":", $key);
                    if ($value > 0) {
                        $product_attribute->is_sku = 1;
                        $product_attribute->product_sku_id = $value;
                    }

                    $product->product_attributes()->save($product_attribute);
                }
            }
            
            Notification::success('商品添加成功。');
            return Redirect::route('products.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    /**
     * 显示编辑新商品页面
     * GET /products/edit
     *
     * @return Response
     */
    public function edit(Product $product)
    {
        $productcategories = ProductCategory::where('depth', '<', 1)->get();
        $brands = Brand::all();

        $product_attrs = ProductAttrKey::where('status', 1)->get();
        $product_basic_attrs = array();
        $product_package_attrs = array();
        $product_sale_attrs = array();
        foreach ($product_attrs as $product_attr) {
            if ($product_attr->is_package_attr === 0 && $product_attr->is_sale_attr === 0) {
                array_push($product_basic_attrs, $product_attr);
            }

            if ($product_attr->is_package_attr === 1) {
                array_push($product_package_attrs, $product_attr);
            }

            if ($product_attr->is_sale_attr === 1) {
                array_push($product_sale_attrs, $product_attr);
            }
        }

        return View::make('products.edit')
            ->with('productcategories', $productcategories)
            ->with('brands', $brands)
            ->with('product_basic_attrs', $product_basic_attrs)
            ->with('product_package_attrs', $product_package_attrs)
            ->with('product_sale_attrs', $product_sale_attrs)
            ->with('product', $product);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'brand' => 'required|integer'
        ], [
            'name.required' => '标题不能为空。',
            'brand.required' => '请选择商品品牌。',
            'brand.integer' => '请选择商品品牌。'
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
        $category = $request->input('category');
        $brand = $request->input('brand');
        $attributes = $request->input('attributes');
        $specs = $request->input('specs');

        $color_id = $request->input('color-id');
        $color_select = $request->input('color-select');
        $sale_colors = $request->input('sale-colors');
        $color_price = $request->input('color-price');
        $color_quantity = $request->input('color-quantity');
        $color_images = $request->file('color-images');
        $color_image_remarks = $request->input('color-image-remarks');
        $color_skus = $request->input('color-skus');

        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $content = Html::entities($request->input('content'));
        $cover = $request->input('cover');
        $status = $request->input('status');
        $thumbs = $request->file('thumb');
        $is_featured = $request->input('is_featured');
        $is_waterfalled = $request->input('is_waterfalled');

        // 表单更改标记位
        $update_thumbnail = $request->input('update-thumbnail');
        $update_attributes = $request->input('update-attributes');
        $update_specs = $request->input('update-specs');
        $update_sales = $request->input('update-sales');
        $covername = strtolower($request->input('covername'));
        $update_poster = $request->input('update-poster');
        $poster = "";

        $thumbnails = array();

        if ($update_thumbnail == 1) {
            if (isset($thumbs) && count($thumbs) > 0) {
                $index = 1;
                foreach ($thumbs as $thumb) {
                    if ($thumb->isValid()) {
                        $filename = $thumb->getClientOriginalName();
                        $filename = pathinfo($filename, PATHINFO_FILENAME);

                        $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $thumb->getClientOriginalExtension();
                        $uploaded_path = $thumb->storeAs(config('imgattrs.product_images.root'), $fullname);

                        $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                        $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                        if ($image_width > $image_height &&
                            $image_width / $image_height > config('imgattrs.product_images.width') / config('imgattrs.product_images.height')) {
                            $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                ->resize(null, config('imgattrs.product_images.height'), function ($constraint) {
                                    $constraint->aspectRatio();
                                })
                                ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                            Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                        } else {
                            $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                ->resize(config('imgattrs.product_images.width'), null, function ($constraint) {
                                    $constraint->aspectRatio();
                                })
                                ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                            Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                        }

                        if ($uploaded_path) {
                            $thumb = $fullname;
                        } else {
                            Notification::error("文件上传错误！");
                            return Redirect::back()->withInput();
                        }
                    } else {
                        $thumb = "";
                    }

                    if ($thumb != "") {
                        array_push($thumbnails, $thumb);
                    }

                    if ($cover == $index++) {
                        $cover = $thumb;
                    }
                }
            } else {
                $cover = null;
            }
        }

        if ($update_poster == 1) {
            if ($request->hasFile('poster')) {
                if ($request->file('poster')->isValid()) {
                    $poster_file = $request->file('poster');
                    $filename = $poster_file->getClientOriginalName();
                    $filename = pathinfo($filename, PATHINFO_FILENAME);

                    $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $poster_file->getClientOriginalExtension();
                    $uploaded_path = $poster_file->storeAs(config('imgattrs.product_images.root'), $fullname);

                    $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                    if ($image_width > $image_height &&
                        $image_width / $image_height > config('imgattrs.product_images.width') / config('imgattrs.product_images.height')) {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(null, config('imgattrs.product_images.height'), function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                        Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    } else {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(config('imgattrs.product_images.width'), null, function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                        Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }

                    if ($uploaded_path) {
                        $poster = $fullname;
                    } else {
                        Notification::error("文件上传错误！");
                        return Redirect::back()->withInput();
                    }
                }
            }
        }

        try {
            $product->name = $name;
            $product->brand_id = $brand;
            $product->category_id = $category;
            $product->price = $price;
            $product->quantity = $quantity;
            $product->description = $content;
            $product->state = $status;
            $product->is_featured = ($is_featured === 'on' ? 1 : 0);
            $product->is_waterfalled = ($is_waterfalled === 'on' ? 1 : 0);

            if ($update_poster == 1) {
                $product->poster = $poster;
            }

            $product->save();

            if ($update_thumbnail == 1) {
                $thumbflags = $request->input('thumbflag');
                $thumbnames = $request->input('thumbname');

                if (is_array($thumbnames) && count($thumbnames) > 0) {
                    for ($i = 0; $i < count($thumbnames); $i++) {
                        if ($thumbflags[$i] == 1) {
                            $image = ProductImage::where('product_id', $product->id)
                                ->where('path', $thumbnames[$i])
                                ->first();
                            if (isset($image)) {
                                $image->delete();
                            }
                        }
                    }
                }

                if (count($thumbnails) > 0) {
                    foreach ($thumbnails as $thumb) {
                        try {
                            $image = new ProductImage();
                            $image->path = $thumb;

                            $product->images()->save($image);
                        } catch (\Exception $e) {
                            Notification::error('错误: ' . $e->getMessage());
                            return Redirect::back()->withInput();
                        }
                    }
                }
            }

            if ($covername != "") {
                $images = $product->images()->get();
                if (isset($images) && count($images) > 0) {
                    foreach ($images as $image) {
                        $name = substr($image->path, strpos($image->path, '-') + 1);
                        $covername = str_replace('_', '-', $covername);
                        
                        if (strcasecmp($name, $covername) == 0 || strcasecmp($image->path, $covername) == 0) {
                            $product->featured_image = $image->path;
                            $product->save();

                            break;
                        }
                    }
                }
            }

            $product_basic_attr_ids = array();
            // 存储商品基础属性
            if ($update_attributes == 1) {
                if ($attributes && sizeof($attributes) > 0) {
                    foreach ($attributes as $key => $value) {
                        try {
                            list($key_name, $key_id) = explode("_", $key);
                            $product_attr_value = ProductAttrValue::find($key_id);
                            $product_attr_value->value = trim($value);
                            $product_attr_value->save();
                        } catch (\Exception $e) {
                            Notification::error('错误: ' . $e->getMessage());
                            return Redirect::back()->withInput();
                        }
                    } 
                }
            }

            // 存储商品包装属性
            if ($update_specs == 1) {
                if ($specs && sizeof($specs) > 0) {
                    foreach ($specs as $key => $value) {
                        try {
                            list($key_name, $key_id) = explode("_", $key);
                            $product_attr_value = ProductAttrValue::find($key_id);
                            $product_attr_value->value = trim($value);
                            $product_attr_value->save();
                        } catch (\Exception $e) {
                            Notification::error('错误: ' . $e->getMessage());
                            return Redirect::back()->withInput();
                        }
                    }
                }
            }

            // 存储商品销售属性
            if ($update_sales == 1) {
                $skus = $product->skus()->get();
                foreach ($skus as $sku) {
                    if (null === $color_skus || !in_array($sku->id, $color_skus)) {
                        try {
                            list($key_id, $value_id) = explode(":", $sku->attr_codes);
                            $product_attr_value = ProductAttrValue::find($value_id);
                            $product_attr_value->delete();
                            $product_attribute = $sku->attributes()->first();
                            $product_attribute->delete();
                            $sku->delete();
                        } catch (\Exception $e) {
                            Notification::error('错误: ' . $e->getMessage());
                            return Redirect::back()->withInput();
                        }
                    }
                }
                if ($color_select && count($color_select) > 0) {
                    for ($i = 0; $i < count($color_select); $i++) {
                        if ($color_select[$i] === "on") {
                            if (null !== $color_skus[$i]) {
                                try {
                                    $product_sku = ProductSku::find($color_skus[$i]);
                                    list($key_id, $value_id) = explode(":", $product_sku->attr_codes);

                                    $product_attr_value = ProductAttrValue::find($value_id);
                                    $product_attr_value->value = trim($sale_colors[$i]);
                                    $product_attr_value->save();

                                    $product_sku->name = trim($sale_colors[$i]);
                                    $product_sku->price = trim($color_price[$i]);
                                    $product_sku->quantity = trim($color_quantity[$i]);

                                    if (0 === $color_image_remarks[$i]) {
                                        $product_sku->save();
                                    }
                                    else {
                                        if (isset($color_images) && null !== $color_images[$i] && $color_images[$i]->isValid()) {
                                            $filename = $color_images[$i]->getClientOriginalName();
                                            $filename = pathinfo($filename, PATHINFO_FILENAME);

                                            $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $color_images[$i]->getClientOriginalExtension();
                                            $uploaded_path = $color_images[$i]->storeAs(config('imgattrs.product_images.root'), $fullname);

                                            $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                                            $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                                            if ($image_width > $image_height &&
                                                $image_width / $image_height > config('imgattrs.product_images.width') / config('imgattrs.product_images.height')) {
                                                $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                                    ->resize(null, config('imgattrs.product_images.height'), function ($constraint) {
                                                        $constraint->aspectRatio();
                                                    })
                                                    ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                                                Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                                            } else {
                                                $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                                    ->resize(config('imgattrs.product_images.width'), null, function ($constraint) {
                                                        $constraint->aspectRatio();
                                                    })
                                                    ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                                                Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                                            }

                                            if ($uploaded_path) {
                                                $product_sku->featured_image = $fullname;
                                            } else {
                                                Notification::error("文件上传错误！");
                                                return Redirect::back()->withInput();
                                            }

                                            $product_sku->save();
                                        }
                                        else {
                                            $product_sku->featured_image = "";
                                            $product_sku->save();
                                        }
                                    }
                                } catch (\Exception $e) {
                                    Notification::error('错误: ' . $e->getMessage());
                                    return Redirect::back()->withInput();
                                }
                            }
                            else {
                                try {
                                    $product_attr_value = new ProductAttrValue();
                                    $product_attr_value->product_attr_key_id = $color_id;
                                    $product_attr_value->value = trim($sale_colors[$i]);
                                    $product_attr_value->status = 1;
                                    $product_attr_value->save();

                                    $product_sku = new ProductSku();
                                    $product_sku->attr_codes = $color_id . ":" . $product_attr_value->id;
                                    $product_sku->name = trim($sale_colors[$i]);
                                    $product_sku->price = trim($color_price[$i]);
                                    $product_sku->quantity = trim($color_quantity[$i]);
                                    $product_sku->featured_image = "";

                                    if ($color_images[$i]->isValid()) {
                                        $filename = $color_images[$i]->getClientOriginalName();
                                        $filename = pathinfo($filename, PATHINFO_FILENAME);

                                        $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $color_images[$i]->getClientOriginalExtension();
                                        $uploaded_path = $color_images[$i]->storeAs(config('imgattrs.product_images.root'), $fullname);

                                        $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                                        $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                                        if ($image_width > $image_height &&
                                            $image_width / $image_height > config('imgattrs.product_images.width') / config('imgattrs.product_images.height')) {
                                            $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                                ->resize(null, config('imgattrs.product_images.height'), function ($constraint) {
                                                    $constraint->aspectRatio();
                                                })
                                                ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                                            Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                                        } else {
                                            $image = Image::make(storage_path('app/public/' . $uploaded_path))
                                                ->resize(config('imgattrs.product_images.width'), null, function ($constraint) {
                                                    $constraint->aspectRatio();
                                                })
                                                ->crop(config('imgattrs.product_images.width'), config('imgattrs.product_images.height'));
                                            Storage::put(config('imgattrs.product_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                                        }

                                        if ($uploaded_path) {
                                            $product_sku->featured_image = $fullname;
                                        } else {
                                            Notification::error("文件上传错误！");
                                            return Redirect::back()->withInput();
                                        }
                                    }

                                    $product->skus()->save($product_sku);
                                    $product_basic_attr_ids[$color_id . ":" . $product_attr_value->id] = $product_sku->id;
                                } catch (\Exception $e) {
                                    Notification::error('错误: ' . $e->getMessage());
                                    return Redirect::back()->withInput();
                                }
                            }
                        }
                    }
                }
            }

            // 存储产品属性表
            if (sizeof($product_basic_attr_ids) > 0) {
                foreach ($product_basic_attr_ids as $key => $value) {
                    $product_attribute = new ProductAttribute();
                    list($product_attribute->product_attr_key_id, $product_attribute->product_attr_value_id) = explode(":", $key);
                    if ($value > 0) {
                        $product_attribute->is_sku = 1;
                        $product_attribute->product_sku_id = $value;
                    }

                    $product->product_attributes()->save($product_attribute);
                }
            }

            Notification::success('商品更新成功。');
            return Redirect::route('products.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    /**
     * 删除选定商品
     *
     * @param Product $product
     * @return void
     */
    public function destroy(Product $product)
    {
        try {
            $name = $product->name;
            $product->state = "retired";
            $product->save();
            $product->delete();

            Notification::success('商品 "' . $name . '" 删除成功。');
            return Redirect::route('products.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    /**
     * 异步获取产品列表
     * GET /products
     *
     * @return Response
     */
    public function getProductBySearch(Request $request)
    {
        $query = $request->get('q');
        $page = $request->get('page');

        $products = DB::table('products')
            ->select('id', 'name', 'featured_image', 'price')
            ->where('state', 'active')
            ->where('name', 'like', '%' . $query . '%')
            ->get();

        $result = array('total_count' => count($products), 'items' => $products->toArray());

        return json_encode($result);
    }

    /**
     * 异步上传图片
     * POST /products/upload
     *
     * @return Response
     */
    public function uploadFile(Request $request)
    {
        $success = false;
        $msg = "";
        $file_path = "";

        if ($request->hasFile('upload_file')) {
            $file = $request->file('upload_file');
            if ($file->isValid()) {
                $filename = $file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);

                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $file->getClientOriginalExtension();
                $uploaded_path = $file->storeAs(config('imgattrs.content_images.root'), $fullname);

                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                if ($image_width > $image_height && 
                    $image_width > config('imgattrs.content_images.width')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.content_images.width'), null, function($constraint){
                            $constraint->aspectRatio();
                        });
                    Storage::put(config('imgattrs.content_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                elseif ($image_width < $image_height && $image_height > config('imgattrs.content_images.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.content_images.height'), function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    Storage::put(config('imgattrs.content_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path));
                    Storage::put(config('imgattrs.content_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }

                if ($uploaded_path) {
                    $success = true;
                    $file_path = URL::asset('storage/thumbs/contents/thumb_' . $fullname);
                }
                else {
                    $msg = "文件存储错误！";
                }
            }
            else {
                $msg = "无效的上传内容！";
            }
        }
        else {
            $msg = "表单提交错误！";
        }

        $json = array('success' => $success, 
            'msg' => $msg,
            'file_path' => $file_path);

        return json_encode($json);
    }

    public function getUnprovedProducts()
    {
        $products = Product::where('state', 'draft')->orderBy('created_at', 'desc')->paginate(10);

        return View::make('products.index')->with('products', $products);
    }

    public function getPublishedProducts()
    {
        $products = Product::where('state', 'active')->orderBy('created_at', 'desc')->paginate(10);

        return View::make('products.index')->with('products', $products);
    }
}
