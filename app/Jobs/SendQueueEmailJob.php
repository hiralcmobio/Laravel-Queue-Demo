<?php

namespace App\Jobs;

use App\Mail\SendQueueMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendQueueEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $blogDetail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($blogDetail)
    {
        $this->blogDetail = $blogDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        return Mail::to('hiral.chudasama@mobiosolutions.com')
            ->send(new SendQueueMail($this->blogDetail));
    }
}
