<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;


use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class UserTest extends TestCase
{

    public function testRegisterSuccess(): void
    {

        $this->post("/api/users", [
            "username" => "widadi",
            "password" => "pass",
            "name" => "widadi widadi"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "widadi",
                    "name" => "widadi widadi"
                ]
            ]);
    }
    public function testRegisterFailed(): void
    {
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => ""
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
    public function testRegisterUsernameAlreadyExist(): void
    {
        $this->testRegisterSuccess();
        $this->post("/api/users", [
            "username" => "widadi",
            "password" => "pass",
            "name" => "widadi widadi"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => ["Username already registered"]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test",
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);

        $user = User::where("username", "test")->first();
        assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test",
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["username or password wrong"]
                ]
            ]);
    }
    public function testLoginFailedPasswordWrong()
    {
        $this->seed(UserSeeder::class);

        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "salah",
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["username or password wrong"]

                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/users/current", [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
    }
    public function testGetUnauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/users/current")
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);
        $this->get("/api/users/current", [
            "Authorization" => "invalidtoken"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where("username", "test")->first();
        $this->patch(
            "/api/users/current",
            [
                "password" => "baru"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
        $newUser = User::where("username", "test")->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->patch(
            "/api/users/current",
            [
                "name" => "widadi"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "widadi"
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);

        $this->patch(
            "/api/users/current",
            [
                "name" => "widadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadiwidadi"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field must not be greater than 100 characters."],
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->delete(uri: "/api/users/logout", headers: ["Authorization" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
        $user = User::where("username", "test")->first();
        assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed(UserSeeder::class);
        $this->delete(uri: "/api/users/logout", headers: ["Authorization" => "notauth"])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
            $user = User::where("username", "test")->first();
            assertNotNull($user->token);
    }
}
