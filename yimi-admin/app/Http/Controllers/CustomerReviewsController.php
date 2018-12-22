<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\CustomerComment;

class CustomerReviewsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('s') && ($s = $request->input('s')) !== "") {
            $reviews = CustomerComment::where('content', 'like', '%' . $s . '%')
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            return View::make('reviews.index')
                ->with('reviews', $reviews)
                ->with('s', $s);
        }
        else {
            $reviews = CustomerComment::orderBy('updated_at', 'desc')->paginate(10);
            return View::make('reviews.index')->with('reviews', $reviews);
        }
    }

    public function doReview(Request $request)
    {
        $id = $request->input('id');
        $action = $request->input('action');
        $errcode = 0;
        $message = "Success";

        if (is_numeric($id) && $id > 0) {
            $review = CustomerComment::find($id);
            if (isset($review) && $review !== null) {
                if ($action === 'unban' && $review->status === 0) {
                    try {
                        $review->status = 1;
                        $review->save();
                    }
                    catch (\Exception $e) {
                        $errcode = 1;
                        $message = $e->getMessage();
                    }
                }
                elseif ($action === 'ban' && $review->status === 1) {
                    try {
                        $review->status = 0;
                        $review->save();
                    }
                    catch (\Exception $e) {
                        $errcode = 1;
                        $message = $e->getMessage();
                    }
                }
                else {
                    $errcode = 2;
                    $message = 'Action dismatch';
                }
            }
            else {
                $errcode = 3;
                $message = 'Cannot find review object corresponding id';
            }
        }
        else {
            $errcode = 4;
            $message = 'Wrong ID';
        }

        return \response()->json(array('errcode' => $errcode, 'message' => $message));
    }
}
