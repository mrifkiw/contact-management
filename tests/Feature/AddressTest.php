<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressesSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{

    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/" . $contact->id . "/addresses", [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "test",
            "postal_code" => "213432",
        ], ["Authorization" => "test"])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "213432",
                ]
            ]);
    }
    public function testCreateEmptyRequiredFieldFailed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/" . $contact->id . "/addresses", [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "",
            "postal_code" => "213432",
        ], ["Authorization" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }
    public function testCreateContactNotFound(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/" . ($contact->id + 1) . "/addresses", [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "test",
            "postal_code" => "213432",
        ], ["Authorization" => "test"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressesSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $url = "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id;

        $this->get($url, [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => '11111'
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressesSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $urlWrongAddressId = "/api/contacts/" . $address->contact_id . "/addresses/" . ($address->id + 1);

        $urlWrongContactId = "/api/contacts/" . ($address->contact_id + 1) . "/addresses/" . $address->id;

        $this->get($urlWrongAddressId, [
            "Authorization" => "test"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "address not found"
                    ]
                ]
            ]);

        $this->get($urlWrongContactId, [
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressesSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $url = "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id;

        $this->put(uri: $url, data: [
            'street' => 'update',
            'city' => 'update',
            'province' => 'update',
            'country' => 'update',
            'postal_code' => '011111'
        ], headers: [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'update',
                    'city' => 'update',
                    'province' => 'update',
                    'country' => 'update',
                    'postal_code' => '011111'
                ]
            ]);
    }

    public function testUpdateValidationFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressesSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $url = "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id;

        $this->put(uri: $url, data: [
            'street' => 'update',
            'city' => 'update',
            'province' => 'update',
            'country' => '',
            'postal_code' => '011111'
        ], headers: [
            "Authorization" => "test"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }
}
