<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test@mail.com',
            'phone' => '123456789',
        ], [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'first_name' => 'test2',
                    'last_name' => 'test2',
                    'email' => 'test@mail.com',
                    'phone' => '123456789',
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'test2',
            'email' => 'test@mail.com',
            'phone' => '123456789',
        ], [
            'Authorization' => 'test',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'first_name' => [
                        'The first name field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . ($contact->id + 1), [], [
            'Authorization' => 'test',
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testSearchByFirstName() {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        // Log::info(json_encode($response, JSON_PRETTY_PRINT));
        
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName() {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->json();

        // Log::info(json_encode($response, JSON_PRETTY_PRINT));
        
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByEmail() {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->Json();
        
        // Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone() {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=123', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->Json();
        
        // Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound() {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->Json();
        
        // Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage() {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=1', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->Json();
        
        // Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(1, $response['meta']['current_page']);
    }
}
