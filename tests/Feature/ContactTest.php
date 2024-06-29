<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Auth;
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . $contact->id, [
            "Authorization" => "test"
        ])->assertStatus(200)->assertJson([
            "data" => [
                "first_name" => "test",
                "last_name" => "test",
                "email" => "test@pzn.com",
                "phone" => "111111",
            ]
        ]);
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . ($contact->id + 1), [
            "Authorization" => "test"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ]);
    }
    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . $contact->id, [
            "Authorization" => "test2"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        echo (User::query()->limit(1)->first());

        $this->put("/api/contacts/" . $contact->id, [
            "first_name" => "test2",
            "last_name" => "test2",
            "email" => "test2@pzn.com",
            "phone" => "111112",
        ], [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test2",
                    "last_name" => "test2",
                    "email" => "test2@pzn.com",
                    "phone" => "111112",
                ]
            ]);
    }
    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();
        echo (User::query()->limit(1)->first());

        $this->put("/api/contacts/" . $contact->id, [
            "first_name" => "",
            "last_name" => "test2",
            "email" => "test2@pzn.com",
            "phone" => "111112",
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ],
                ]
            ]);
    }
}
