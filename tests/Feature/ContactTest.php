<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete(
            uri: "/api/contacts/" . $contact->id,
            headers: [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true,
            ]);
    }
    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete(
            uri: "/api/contacts/" . ($contact->id + 1),
            headers: [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "contact not found"
                    ]
                ],
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?name=first",["Authorization" => "test"])
            ->assertStatus(200)
            ->json();


        assertEquals(10, count($response["data"]));
        assertEquals(20, $response["meta"]["total"]);
    }
    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?name=last",["Authorization" => "test"])
            ->assertStatus(200)
            ->json();


        assertEquals(10, count($response["data"]));
        assertEquals(20, $response["meta"]["total"]);
    }
    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?email=test",["Authorization" => "test"])
            ->assertStatus(200)
            ->json();


        assertEquals(10, count($response["data"]));
        assertEquals(20, $response["meta"]["total"]);
    }
    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?phone=11111",["Authorization" => "test"])
            ->assertStatus(200)
            ->json();


        assertEquals(10, count($response["data"]));
        assertEquals(20, $response["meta"]["total"]);
    }
    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?name=notfound",["Authorization" => "test"])
            ->assertStatus(200)
            ->json();


        assertEquals(0, count($response["data"]));
        assertEquals(0, $response["meta"]["total"]);
    }
    
    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get("/api/contacts?size=5&page=2",["Authorization" => "test"])
            ->assertStatus(200)
            ->json();

            Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(5, count($response["data"]));
        assertEquals(20, $response["meta"]["total"]);
        assertEquals(2, $response["meta"]["current_page"]);
    }
}
