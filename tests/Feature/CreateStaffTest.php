<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AppBaseTestCase;

class CreateStaffTest extends AppBaseTestCase
{
    use WithFaker;

    private string $endpoint = '/api/v1/staff';

    private array $validData;

    public function __construct()
    {
        parent::__construct();

        $this->validData = [
            'first_name' => 'first name',
            'last_name' => 'last',
            'email' => 'email@email.com',
            'phone' => '9055555555',
            'password' => 'staff_password837123',
            'confirm_password' => 'staff_password837123',
            'role' => Staff::MATCH_MAKER_ROLE
        ];
    }

    public function test_forbidden()
    {
        $this->authStaff();
        $this->testForbidden($this->endpoint);
    }

    public function test_validation_error()
    {
        $this->authSuperAdmin();
        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'email',
            'password',
            'confirm_password',
            'phone',
            'role'
        ]);
    }

    public function test_validate_password()
    {
        $this->authSuperAdmin();
        $data = $this->validData;
        Arr::set($data, 'confirm_password', 'random password');

        $res = $this->post($this->endpoint, $data);
        $res->assertJsonValidationErrors(['confirm_password']);
    }


    public function test_pass_min_8_digit()
    {
        $this->authSuperAdmin();
        $data = $this->validData;
        Arr::set($data, 'password', '7777777'); // must be 8 digit password

        $res = $this->post($this->endpoint);
        $res->assertJsonValidationErrors([
            'password', // because not 8 digit pass provided
            'confirm_password' // because now it won't match the password provided
        ]);
    }

    public function test_if_created_successful()
    {
        $this->authSuperAdmin();
        $count = Staff::count();
        $data = $this->validData;
        $res = $this->post($this->endpoint, $data);
        $res->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.first_name', Arr::get($data, 'first_name')) // enough to check if the new created resource is being returned
                ->where('data.roles.0.name', Arr::get($data, 'role')) // check if role is being returned in an array
                ->etc());

        // check if actually inserted into the database
        $this->assertDatabaseCount((new Staff())->getTable(), $count + 1);

        Arr::set($data, 'role', 'any role');
        $res = $this->post($this->endpoint, $data);
        $res->assertJsonValidationErrors(['email', 'role'])
            ->assertJsonMissingValidationErrors([
                'first_name',
                'last_name',
                'password',
                'confirm_password',
                'phone',
            ]); // email is taken and invalid role validation error
    }
}
