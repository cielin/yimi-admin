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
use App\Designer;

class DesignersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $designers = Designer::where('name', 'like', '%' . $s . '%')
                ->orderBy('name', 'asc')
                ->paginate(10);
            return View::make('designers.index')
                ->with('designers', $designers)
                ->with('s', $s);
        }
        else {
            $designers = Designer::orderBy('name', 'asc')->paginate(10);
            return View::make('designers.index')->with('designers', $designers);
        }
    }

    /**
     * 显示创建新设计师页面
     * GET /designers/create
     *
     * @return Response
     */
    public function create()
    {
    	return View::make('designers.create');
    }

    /**
     * 实现新设计师页面的表单存储
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
        $slug = 'd' . Str::random(7);
        while (null !== Designer::where('slug', $slug)->first()) {
            $slug = 'd' . Str::random(7);
        }
        $description = trim($request->input('description'));
        $avatar = "";

        if ($request->hasFile('avatar')) {
            if ($request->file('avatar')->isValid()) {
                $avatar_file = $request->file('avatar');
                $filename = $avatar_file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);

                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $avatar_file->getClientOriginalExtension();
                $uploaded_path = $avatar_file->storeAs(config('imgattrs.designer_avatar.root'), $fullname);

                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                if ($image_width > $image_height && 
                    $image_width/$image_height > config('imgattrs.designer_avatar.width')/config('imgattrs.designer_avatar.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.designer_avatar.height'), function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.designer_avatar.width'), config('imgattrs.designer_avatar.height'));
                    Storage::put(config('imgattrs.designer_avatar.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.designer_avatar.width'), null, function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.designer_avatar.width'), config('imgattrs.designer_avatar.height'));
                    Storage::put(config('imgattrs.designer_avatar.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }

                if ($uploaded_path) {
                    $avatar = $fullname;
                }
                else {
                    Notification::error("文件上传错误！");
                    return Redirect::back()->withInput();
                }
            }
        }

        try {
            $designer = new Designer();
            $designer->name = $name;
            $designer->slug = $slug;
            $designer->avatar = $avatar;
            $designer->description = $description;
            $designer->save();

            Notification::success('设计师添加成功。');
            return Redirect::route('designers.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit(Designer $designer)
    {
        return View::make('designers.edit')->with('designer', $designer);
    }

    /**
     * 实现设计师编辑页面的表单存储
     * @param  Request $request 表单请求数据
     * @return Reponse
     */
    public function update(Request $request, Designer $designer)
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
        $description = trim($request->input('description'));
        $avatar = "";
        $update_thumbnail = $request->input('update-thumbnail');

        if ($update_thumbnail == 1) {
            if ($request->hasFile('avatar')) {
                if ($request->file('avatar')->isValid()) {
                    $avatar_file = $request->file('avatar');
                    $filename = $avatar_file->getClientOriginalName();
                    $filename = pathinfo($filename, PATHINFO_FILENAME);

                    $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $avatar_file->getClientOriginalExtension();
                    $uploaded_path = $avatar_file->storeAs(config('imgattrs.designer_avatar.root'), $fullname);

                    $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                    if ($image_width > $image_height &&
                        $image_width / $image_height > config('imgattrs.designer_avatar.width') / config('imgattrs.designer_avatar.height')) {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(null, config('imgattrs.designer_avatar.height'), function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.designer_avatar.width'), config('imgattrs.designer_avatar.height'));
                        Storage::put(config('imgattrs.designer_avatar.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    } else {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(config('imgattrs.designer_avatar.width'), null, function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.designer_avatar.width'), config('imgattrs.designer_avatar.height'));
                        Storage::put(config('imgattrs.designer_avatar.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }

                    if ($uploaded_path) {
                        $avatar = $fullname;
                    } else {
                        Notification::error("文件上传错误！");
                        return Redirect::back()->withInput();
                    }
                }
            }
        }

        try {
            $designer->name = $name;
            if ($avatar !== "") {
                $designer->avatar = $avatar;
            }
            $designer->description = $description;
            $designer->save();

            Notification::success('设计师更新成功。');
            return Redirect::route('designers.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function destroy(Designer $designer)
    {
        try {
            $name = $designer->name;
            $designer->delete();

            Notification::success('设计师 "' . $name . '" 删除成功。');
            return Redirect::route('designers.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }
}
