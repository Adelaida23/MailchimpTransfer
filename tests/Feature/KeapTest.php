<?php

namespace Tests\Feature;

use App\Libraries\Keap;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KeapTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_keap_ping()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => 'Gtgw3dODTuwXu3bHodYiYt9pZVSY',
            'refresh_token' => 'VhSiiFsn6TzaQCRAZBHQE28Hfb4ulEXA',
        ]);
    }

    public function test_do_push()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => '5i9eIinFGNEpi2XJQTOIJSBg4Shb',
            'refresh_token' => 'QFqtnnJxD1Jru6hDQA47kN0U2Z8ADf92',
            'list_id'     => 92
        ]);
        $lead = Lead::find(21);
        $response = $infusionsoft->push($lead);
        print_r($response);

    }

}
