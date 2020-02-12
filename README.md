#### **Laravel Queue**
Queues allow you to defer the processing of a time-consuming task, such as sending an email. Delaying these time-consuming tasks drastically speeds up web requests to your application.

Laravel queues provide a unified API across a variety of different queue backends, such as Beanstalk, Amazon SQS, Redis, or even a relational database.

The queue configuration file is stored in `config/queue.php` In this file, you will find connection configurations for each of the queue drivers that are included with the framework, which consists of a database, Beanstalkd, Amazon SQS, Redis, and synchronous driver that will execute jobs immediately A null queue driver is also included which simply discards queued jobs.

So, let's start with Queue!

First we will configure .env file

    QUEUE_CONNECTION=database
    
    MAIL_DRIVER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=your_email_address
    MAIL_PASSWORD=your_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=null
    MAIL_FROM_NAME="LaraQueue"
    
    DB_DATABASE=your_database_name
    DB_USERNAME=root
    DB_PASSWORD=

Now we will make a controller 

    php artisan make:controller LaraQueueController
    
We will create a migration for Jobs table by below command

    php artisan queue:table
    
It will create jobs and failed jobs table in database
and also we will create blog table by below command

    php artisan make:migration create_blogs_table
    
It will create a migration and then we need to migrate the database.

    php artisan migrate
    
Now we will create Model Blog

    php artisan make:model Blog
    
We want to add blog using Queue. So, we will make one job file like

    php artisan make:job blogDetail
    
and now we will call the job class to controller like below.

    $blogDetail = (new blogDetail())->delay(Carbon::now()->addSeconds(3));
        dispatch($blogDetail);
        
Now, we will make one blade file for add blog in `view/blog/addBlog.php`.

    @extends('layouts.app')
    
    @section('content')
        @if(Session::has('message'))
            <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('message') }}</p>
        @endif
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Add Blog</div>
    
                        <form action="postBlog" method="post">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="btitle">Title</label>
                                <div class="col-md-6">
                                    <input class="form-control" type="text" name="title">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="bdescription">Description</label>
                                <div class="col-md-6">
                                    <textarea cols="30" class="form-control" rows="4" name="description"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <input type="submit" name="submit">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    
Now, we will get the posted data in controller and send request data to job file of blog
    
    public function postBlog(Request $request)
    {
        $requestData = $request->all();
        $requestData['user_id'] = Auth::id();
        
        $blogDetail = (new blogDetail($requestData))->delay(Carbon::now()->addSeconds(3));
        dispatch($blogDetail);
    }
Add route to `web.php` file like `Route::post('/postBlog', 'LaraQueueController@postBlog')->name('postBlog');`
And our job file will be like below

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

Now, we will add blog to database 

    php artisan serve
    
    php artisan queue:work
    
In browser add this url: `http://127.0.0.1:8000/`

Do register and login. it will redirect you to home page that will show the add blog page. in that add title and description of blog.
then submit it. It will save blog data through Queue.

Now, we will send newly added blog to mail. We will make mailable file for send mail

    php artisan make:mail SendQueueMail
    
So, it will create this file inside `App\Mail\SendQueueMail.php`. And also we will make one job file for send email.

    php artisan make:job SendQueueEmailJob
    
Now we will send blog data to mail job file with below code add to controller function `postBlog()`.

        $blogDetail = (new blogDetail($requestData))->delay(Carbon::now()->addSeconds(3));
        dispatch($blogDetail);

        //send email to queue
        $blogDetail = $blogDetail->blogData;
        $emailJob = (new SendQueueEmailJob($blogDetail))->delay(Carbon::now()->addSeconds(3));
        dispatch($emailJob);    
        
And we will inject this blog detail to `SendQueueEmailJob.php` file.

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
    
We will make view file which we will send to mail with blog information and for that, we will set data to mailable file like below:

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
    
The View file will like below

    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <title>Laravel</title>
    
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
    
            .full-height {
                height: 100vh;
            }
    
            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
                font-size: 15px;
                font-weight: bold;
            }
    
            .position-ref {
                position: relative;
            }
    
            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
    
            .content {
                text-align: center;
            }
    
            .title {
                font-size: 84px;
            }
    
            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
    
            .m-b-md {
                margin-bottom: 30px;
            }
    
            table, th, td {
                padding: 10px;
                border: 1px solid black;
                border-collapse: collapse;
            }
        </style>
    </head>
    <body>
    <div class="content">
        <div class="flex-center position-ref">
            Newly Added Blog
        </div>
    
        <table>
            <th>Title</th>
            <th>Description</th>
    
            <tr>
                <td>{{$blogDetail['title']}}</td>
                <td>{{$blogDetail['description']}}</td>
            </tr>
        </table>
    </div>
    </body>
    </html>
    
Now run `php artisan quque:work` again and add new blog through browser. So it will add blog and send email through Queue.

    
    




