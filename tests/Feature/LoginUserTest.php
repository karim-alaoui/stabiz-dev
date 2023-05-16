<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\AppBaseTestCase;

/**
 * Class LoginTest
 * @package Tests\Feature
 */
class LoginUserTest extends AppBaseTestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/login';

    /**
     * @param array $data
     * @return TestResponse
     */
    private function callAPI(array $data = []): TestResponse
    {
        return $this->post($this->url, $data);
    }

    public function test_login_fdr()
    {
        // check if login fails if you use the entr login link instead of fdr
        $password = 'password';
        $user = User::factory()->state([
            'type' => User::FOUNDER,
        ])->create();

        $data = [
            'email' => $user->email,
            'password' => $password
        ];
        $endpoint = $this->url . '/entr';
        $res = $this->post($endpoint, $data);
        $res->assertUnauthorized();

        $endpoint2 = $this->url . '/fdr';
        $res2 = $this->post($endpoint2, $data);
        $res2->assertSuccessful();

        // check if fails on wrong password provided
        $res3 = $this->post($endpoint2, array_merge($data, [
            'password' => 'wrong password'
        ]));
        $res3->assertUnauthorized();
    }

    public function test_login_entr()
    {
        $password = 'password';
        $entr = User::factory()->state([
            'type' => User::ENTR
        ])->create();

        // entr should not be able to login using fdr link
        $endpoint = $this->url . '/fdr';
        $data = [
            'email' => $entr->email,
            'password' => $password
        ];
        $res = $this->post($endpoint, $data);
        $res->assertUnauthorized();

        $endpoint2 = $this->url . '/entr';
        $res2 = $this->post($endpoint2, $data);
        $res2->assertSuccessful();

        // check if fails on wrong password
        $res3 = $this->post($endpoint2, array_merge($data, [
            'password' => 'hello world'
        ]));
        $res3->assertUnauthorized();
    }
}
