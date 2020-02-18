<?php

namespace Tests\Feature;

use http\Client\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class laraQueueTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @desc Add blog test case
     *
     * @return Response
     */
    public function testAddBlogView()
    {
        //call below function for use url without middleware
        $this->withoutMiddleware();

        //generate fake data
        $attributes = [
            'title' => $this->faker->title,
            'description' => $this->faker->paragraph,
            'user_id' => $this->faker->randomNumber(),
        ];

        //send data to route
        $response = $this->post('/postBlog',$attributes);

        //check assert sattus
        $response->assertStatus(302);
    }

}
