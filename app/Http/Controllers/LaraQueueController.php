<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendQueueEmailJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use App\Jobs\blogDetail;

class LaraQueueController extends Controller
{

    public function postBlog(Request $request)
    {
        //get data to store in blog.
        $requestData = $request->all();
        $requestData['user_id'] = Auth::id();

        //send data to Queue
        $blogDetail = (new blogDetail($requestData))->delay(Carbon::now()->addSeconds(3));
        dispatch($blogDetail);

        //send email to queue
        $blogDetail = $blogDetail->blogData;
        $emailJob = (new SendQueueEmailJob($blogDetail))->delay(Carbon::now()->addSeconds(3));
        dispatch($emailJob);

        //flash mail send message
        Session::flash('message', 'Newly added blog send to your email id!');
        Session::flash('alert-class', 'alert-success');
        return redirect('/home');
    }

}
