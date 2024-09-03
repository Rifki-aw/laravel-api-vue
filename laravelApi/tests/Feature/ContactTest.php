<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
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

    public function testCreateContactUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Bahrul',
            'email' => 'rifki',
            'phone' => '6283128788',
        ], [
            'Authorization' => 'salah',
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@mail.com',
                    'phone' => '123456789',
                ]
            ]);
    }

    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'User not found'
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test2',
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'User not found'
                    ]
                ]
            ]);
    }
}
