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
use Illuminate\Support\Facades\URL;
use Html;
use App\Article;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $articles = Article::where('title', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('articles.index')
                ->with('articles', $articles)
                ->with('s', $s);
        }
        else {
            $articles = Article::orderBy('updated_at', 'desc')->paginate(10);
            return View::make('articles.index')->with('articles', $articles);
        }
    }

    /**
     * 显示创建新文章页面
     * GET /articles/create
     *
     * @return Response
     */
    public function create()
    {
    	return View::make('articles.create');
    }

    /**
     * 显示更新文章页面
     * GET /articles/edit
     *
     * @return Response
     */
    public function edit(Article $article)
    {
        return View::make('articles.edit')->with('article', $article);
    }

    /**
     * 实现新文章页面的表单存储
     * @param  Request $request 表单请求数据
     * @return Reponse
     */
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
        $slug = trim($request->input('slug'));
        if ($slug == "") {
            $slug = 'a' . Str::random(7);
            while (null !== Article::where('slug', $slug)->first()) {
                $slug = 'a' . Str::random(7);
            }
        }
        $content = Html::entities(trim($request->input('content')));
        $featured_image = "";
        $type = $request->input('type');
        $status = $request->input('status');

        if ($request->hasFile('featured_image')) {
            if ($request->file('featured_image')->isValid()) {
                $article_file = $request->file('featured_image');
                $filename = $article_file->getClientOriginalName();
                $filename = pathinfo($filename, PATHINFO_FILENAME);

                $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $article_file->getClientOriginalExtension();
                $uploaded_path = $article_file->storeAs(config('imgattrs.article_images.root'), $fullname);

                $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                if ($image_width > $image_height && 
                    $image_width/$image_height > config('imgattrs.article_images.width')/config('imgattrs.article_images.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.article_images.height'), function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.article_images.width'), config('imgattrs.article_images.height'));
                    Storage::put(config('imgattrs.article_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.article_images.width'), null, function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.article_images.width'), config('imgattrs.article_images.height'));
                    Storage::put(config('imgattrs.article_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }

                if ($uploaded_path) {
                    $featured_image = $fullname;
                }
                else {
                    Notification::error("文件上传错误！");
                    return Redirect::back()->withInput();
                }
            }
        }

        try {
        	$uid = \Auth::user()->id;

            $article = new article();
            $article->user_id = $uid;
            $article->title = $title;
            $article->slug = $slug;
            $article->featured_image = $featured_image;
            $article->content = $content;
            $article->type = $type;
            $article->status = $status;
            $article->save();

            Notification::success('文章添加成功。');
            return Redirect::route('articles.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

    /**
     * 实现文章编辑页面的表单存储
     * @param  Request $request 表单请求数据
     * @return Reponse
     */
    public function update(Request $request, Article $article)
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
        $slug = trim($request->input('slug'));
        if ($slug == "") {
            $slug = 'a' . Str::random(7);
            while (null !== Article::where('slug', $slug)->first()) {
                $slug = 'a' . Str::random(7);
            }
        }
        $content = Html::entities(trim($request->input('content')));
        $featured_image = "";
        $type = $request->input('type');
        $status = $request->input('status');
        $update_thumbnail = $request->input('update-thumbnail');

        if ($update_thumbnail == 1) {
            if ($request->hasFile('featured_image')) {
                if ($request->file('featured_image')->isValid()) {
                    $article_file = $request->file('featured_image');
                    $filename = $article_file->getClientOriginalName();
                    $filename = pathinfo($filename, PATHINFO_FILENAME);

                    $fullname = Str::slug(Str::random(12) . '-' . $filename) . '.' . $article_file->getClientOriginalExtension();
                    $uploaded_path = $article_file->storeAs(config('imgattrs.article_images.root'), $fullname);

                    $image_width = Image::make(storage_path('app/public/' . $uploaded_path))->width();
                    $image_height = Image::make(storage_path('app/public/' . $uploaded_path))->height();

                    if ($image_width > $image_height && 
                        $image_width/$image_height > config('imgattrs.article_images.width')/config('imgattrs.article_images.height')) {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(null, config('imgattrs.article_images.height'), function($constraint){
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.article_images.width'), config('imgattrs.article_images.height'));
                        Storage::put(config('imgattrs.article_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }
                    else {
                        $image = Image::make(storage_path('app/public/' . $uploaded_path))
                            ->resize(config('imgattrs.article_images.width'), null, function($constraint){
                                $constraint->aspectRatio();
                            })
                            ->crop(config('imgattrs.article_images.width'), config('imgattrs.article_images.height'));
                        Storage::put(config('imgattrs.article_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                    }

                    if ($uploaded_path) {
                        $featured_image = $fullname;
                    }
                    else {
                        Notification::error("文件上传错误！");
                        return Redirect::back()->withInput();
                    }
                }
            }
        }

        try {
            $article->title = $title;
            if ($featured_image !== "") {
                $article->featured_image = $featured_image;
            }
            if ($type == 2) {
                $article->slug = $slug;
            }
            $article->content = $content;
            $article->type = $type;
            $article->status = $status;

            $article->save();

            Notification::success('文章更新成功。');
            return Redirect::route('articles.index');
        }
        catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }
    }

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
                    $image_width/$image_height > config('imgattrs.content_images.width')/config('imgattrs.content_images.height')) {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(null, config('imgattrs.content_images.height'), function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.content_images.width'), config('imgattrs.content_images.height'));
                    Storage::put(config('imgattrs.content_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }
                else {
                    $image = Image::make(storage_path('app/public/' . $uploaded_path))
                        ->resize(config('imgattrs.content_images.width'), null, function($constraint){
                            $constraint->aspectRatio();
                        })
                        ->crop(config('imgattrs.content_images.width'), config('imgattrs.content_images.height'));
                    Storage::put(config('imgattrs.content_images.thumbnail') . '/thumb_' . $fullname, $image->stream()->__toString());
                }

                if ($uploaded_path) {
                	$success = true;
                    $file_path = URL::asset('storage/images/contents/' . $fullname);
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

    public function destroy(Article $article)
    {
        try {
            $title = $article->title;
            Article::destroy($article->id);

            Notification::success('文章 "' . $title . '" 删除成功。');
            return Redirect::route('articles.index');
        } catch (\Exception $e) {
            Notification::error('错误: ' . $e->getMessage());
            return Redirect::back()->withInput();
        }  
    }
}
