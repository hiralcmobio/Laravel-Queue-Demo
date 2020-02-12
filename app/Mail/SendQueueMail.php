<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendQueueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $blogDetail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $blogDetail)
    {
        $this->blogDetail = $blogDetail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('blogs.viewBlog')
            ->with([
                'blogDetail' => $this->blogDetail
            ]);
    }
}
