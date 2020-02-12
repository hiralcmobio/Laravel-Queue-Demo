<?php

namespace App\Jobs;

use App\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class blogDetail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $blogData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $requestData)
    {
        $this->blogData = $requestData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Save blog
        $data = new Blog();
        $data->title = $this->blogData['title'];
        $data->description = $this->blogData['description'];
        $data->user_id = $this->blogData['user_id'];
        $data->save();

        return $this->blogData;
    }
}
