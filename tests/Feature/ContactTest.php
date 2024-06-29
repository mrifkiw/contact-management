<?php

namespace Tests\Feature;


use Database\Seeders\UserSeeder;
use Tests\TestCase;

class ContactTest extends TestCase
{

    public function testCreateSuccess()
    {

        $this->seed(UserSeeder::class);

        $this->post("/api/contacts", [
            "first_name" => "wi",
            "last_name" => "wid",
            "email" => "wid@gmail.com",
            "phone" => "081234567654",
        ], ["Authorization" => "test"])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "wi",
                    "last_name" => "wid",
                    "email" => "wid@gmail.com",
                    "phone" => "081234567654",
                ]
            ]);
    }

    public function testCreateRequiredDataFailed()
    {

        $this->seed(UserSeeder::class);

        $this->post("/api/contacts", [
            // "first_name" => "wi",
            "last_name" => "wid",
            "email" => "wid@gmail.com",
            "phone" => "081234567654",
        ], ["Authorization" => "test"])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }
    public function testCreateUnauthorizedFailed()
    {

        $this->seed(UserSeeder::class);

        $this->post("/api/contacts", [
            "first_name" => "wi",
            "last_name" => "wid",
            "email" => "wid@gmail.com",
            "phone" => "081234567654",
        ], ["Authorization" => "wrong"])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
}
