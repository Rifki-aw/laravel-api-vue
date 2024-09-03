<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateContactSuccess()
    {
        // check ser sudah login atau belum
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/contacts', [
            'first_name' => 'Rifki',
            'last_name' => 'Bahrul',
            'email' => 'rifki@example.com',
            'phone' => '6283128788',
        ], [
            'Authorization' => 'test',
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'first_name' => 'Rifki',
                    'last_name' => 'Bahrul',
                    'email' => 'rifki@example.com',
                    'phone' => '6283128788',
                ]
            ]);
    }

    public function testCreateContactFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Bahrul',
            'email' => 'rifki',
            'phone' => '6283128788',
        ], [
            'Authorization' => 'test',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ],
                    "email" => [
                        "The email field must be a valid email address."
                    ]
                ]
            ]);
    }
}
