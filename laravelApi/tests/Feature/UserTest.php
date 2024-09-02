<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertNotEquals;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'rifki',
            'password' => 'rahasia',
            'name' => 'Muhammad Rifki Bahrul Ulum',
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "rifki",
                    "name" => "Muhammad Rifki Bahrul Ulum"
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'rifki',
            'password' => 'rahasia',
            'name' => 'Muhammad Rifki Bahrul Ulum',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ]
            ]);


        $user = User::where('username', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'salah',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test'
                ]
            ]);
    }

    // test mengirimkan token kosong / tanpa token
    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    // test mengirimkan token salah
    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'salah',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        
        // check user sebelum update 
        $oldUser = User::where('username', 'test')->first();

        $this->patch(
            '/api/users/current',
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        
        // pastikan password lama dan baru tidak sama 
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess() {
        $this->seed([UserSeeder::class]);
        
        // check user sebelum update 
        $oldUser = User::where('username', 'test')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'Rifki'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'Rifki',
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        
        // pastikan name lama dan baru tidak sama 
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailedNameExceeds() {
        $this->seed([UserSeeder::class]);
        
        // check user sebelum update 
        $oldUser = User::where('username', 'test')->first();

        $this->patch(
            '/api/users/current',
            [
                'name' => 'RifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifkiRifki'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }
}
