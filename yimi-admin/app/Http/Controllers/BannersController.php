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
use App\Banner;

class BannersController extends Controller
{
	public function index(Request $request)
	{
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $banners = Banner::where('title', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('banners.index')
                ->with('banners', $banners)
                ->with('s', $s);
        }
        else {
            $banners = Banner::orderBy('updated_at', 'desc')->paginate(10);
            return View::make('banners.index')->with('banners', $banners);
        }
	}

	public function create()
	{
		return View::make('banners.create');
	}

	public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ], [
            'title.required' => '标题不能为空。'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }
            
            return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $title = trim($request->input('title'));
        $type = $request->input('type');
        $link = trim($request->input('link'));
        $description = trim($request->input('description'));
        $image = "";

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $image_file = $request->file('image');
                $filename = $image_file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);

                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $image_file->getClientOriginalExtension();
                $uploaded_path = $image_file->storeAs(config('imgattrs.banner_images.root'), $fullname);

                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                if ($image_width > $image_height && 
                    $image_width/$image_height > config('imgattrs.banner_images.width')/config('imgattrs.banner_images.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.banner_images.height'), function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.banner_images.width'), config('imgattrs.banner_images.height'));
                    Storage::put(config('imgattrs.banner_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.banner_images.width'), null, function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.banner_images.width'), config('imgattrs.banner_images.height'));
                    Storage::put(config('imgattrs.banner_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }

                if ($uploaded_path) {
                    $image = $fullname;
                }
                else {
                    Notification::error("文件上传错误！");
                    return Redirect::back()->withInput();
                }
            }
        }

        try {
            $banner = new Banner();
            $banner->title = $title;
            $banner->type = $type;
            $banner->link = $link;
            $banner->image = $image;
            $banner->description = $description;
            $banner->save();

            Notification::success('Banner添加成功。');
            return Redirect::route('banners.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit(Banner $banner)
    {
        return View::make('banners.edit')->with('banner', $banner);
    }

    public function update(Request $request, Banner $banner)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ], [
            'title.required' => '标题不能为空。'
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::error($error);
            }

            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $title = trim($request->input('title'));
        $type = $request->input('type');
        $link = trim($request->input('link'));
        $description = trim($request->input('description'));
        $image = "";
        $update_thumbnail = $request->input('update-thumbnail');

        if ($update_thumbnail == 1) {
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    $image_file = $request->file('image');
                    $filename = $image_file->getClientOriginalName();
                    $filename = pathinfo($filename, PATHINFO_FILENAME);

                    $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $image_file->getClientOriginalExtension();
                    $uploaded_path = $image_file->storeAs(config('imgattrs.banner_images.root'), $fullname);

                    $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                    if ($image_width > $image_height &&
                        $image_width / $image_height > config('imgattrs.banner_images.width') / config('imgattrs.banner_images.height')) {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(null, config('imgattrs.banner_images.height'), function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.banner_images.width'), config('imgattrs.banner_images.height'));
                        Storage::put(config('imgattrs.banner_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    } else {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(config('imgattrs.banner_images.width'), null, function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.banner_images.width'), config('imgattrs.banner_images.height'));
                        Storage::put(config('imgattrs.banner_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }

                    if ($uploaded_path) {
                        $image = $fullname;
                    } else {
                        Notification::error("文件上传错误！");
                        return Redirect::back()->withInput();
                    }
                }
            }
        }

        try {
            $banner->title = $title;
            $banner->type = $type;
            $banner->link = $link;
            if ($image !== "") {
                $banner->image = $image;
            }
            $banner->description = $description;
            $banner->save();

            Notification::success('Banner更新成功。');
            return Redirect::route('banners.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function destroy(Banner $banner)
    {
        try {
            $title = $banner->title;
            $banner->delete();

            Notification::success('Banner "' . $title . '" 删除成功。');
            return Redirect::route('banners.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }
}
