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
use App\Portfolio;
use App\Designer;

class PortfoliosController extends Controller
{
	public function index(Request $request)
	{
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $portfolios = Portfolio::where('title', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('portfolios.index')
                ->with('portfolios', $portfolios)
                ->with('s', $s);
        }
        else {
            $portfolios = Portfolio::orderBy('updated_at', 'desc')->paginate(10);
            return View::make('portfolios.index')->with('portfolios', $portfolios);
        }
	}

	public function create()
	{
        $designers = Designer::all();

		return View::make('portfolios.create')->with('designers', $designers);
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
        $designer_id = $request->input('did');
        $description = trim($request->input('description'));
        $image = "";

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $image_file = $request->file('image');
                $filename = $image_file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);

                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $image_file->getClientOriginalExtension();
                $uploaded_path = $image_file->storeAs(config('imgattrs.portfolio_images.root'), $fullname);

                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                if ($image_width > $image_height && 
                    $image_width/$image_height > config('imgattrs.portfolio_images.width')/config('imgattrs.portfolio_images.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.portfolio_images.height'), function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.portfolio_images.width'), config('imgattrs.portfolio_images.height'));
                    Storage::put(config('imgattrs.portfolio_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.portfolio_images.width'), null, function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.portfolio_images.width'), config('imgattrs.portfolio_images.height'));
                    Storage::put(config('imgattrs.portfolio_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
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
            $portfolio = new Portfolio();
            $portfolio->title = $title;
            $portfolio->image = $image;
            $portfolio->description = $description;

            $designer = Designer::find($designer_id);
            $designer->portfolios()->save($portfolio);

            Notification::success('设计师作品添加成功。');
            return Redirect::route('portfolios.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function edit(Portfolio $portfolio)
    {
        $designers = Designer::all();

        return View::make('portfolios.edit')
            ->with('designers', $designers)
            ->with('portfolio', $portfolio);
    }

    public function update(Request $request, Portfolio $portfolio)
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
        $designer_id = $request->input('did');
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
                    $uploaded_path = $image_file->storeAs(config('imgattrs.portfolio_images.root'), $fullname);

                    $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                    if ($image_width > $image_height &&
                        $image_width / $image_height > config('imgattrs.portfolio_images.width') / config('imgattrs.portfolio_images.height')) {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(null, config('imgattrs.portfolio_images.height'), function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.portfolio_images.width'), config('imgattrs.portfolio_images.height'));
                        Storage::put(config('imgattrs.portfolio_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    } else {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(config('imgattrs.portfolio_images.width'), null, function ($constraint) {
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.portfolio_images.width'), config('imgattrs.portfolio_images.height'));
                        Storage::put(config('imgattrs.portfolio_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
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
            $portfolio->title = $title;
            if ($image !== "") {
                $portfolio->image = $image;
            }
            $portfolio->description = $description;

            $designer = Designer::find($designer_id);
            $designer->portfolios()->save($portfolio);

            Notification::success('设计师作品更新成功。');
            return Redirect::route('portfolios.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    public function destroy(Portfolio $portfolio)
    {
        try {
            $title = $portfolio->title;
            $portfolio->delete();

            Notification::success('设计师作品 "' . $title . '" 删除成功。');
            return Redirect::route('portfolios.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }
}
