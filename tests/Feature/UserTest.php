<?php

namespace Tests\Feature;


use Tests\TestCase;

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
                "errors"=> [
                    "username" => ["Username already registered"]
                ]
            ]);
    }
    
}
