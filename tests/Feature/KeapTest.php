<?php

namespace Tests\Feature;

use App\Libraries\ActiveTrail;
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
            'access_token'  => 'OG8MBlYlUKgP7eycARk347KESFi9',
            'refresh_token' => 'Ak2zW7v7vdeC40VG69ucedwKqXBSIxXs',
            'list_id'     => 92
        ]);
        $lead = Lead::find(1);
        $response = $infusionsoft->push($lead);
        print_r($response);
    }
    /* its ok
    public function test_insert_find_contact()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => 'oIoOTimlUHQMOdmpuupxIABJ6No2',
            'refresh_token' => 'TYME56TgMLlP3F8fV5kTjoNyj7AFjedM',
            'list_id'     => 92
        ]);
        $lead = Lead::find(22);
        $response = $infusionsoft->push($lead);
        //print_r($response);

        //$lead = Lead::find(20);
        $response = $infusionsoft->delete($lead);
        print_r(" print_R, el contacto email es: ---- : " . $response);
        dd("El response dd: ", $response);
        //print_r($response);

    }
    */

    public function test_delete_contact()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => 'ywAwAEtf9VPfq7jKU8MIY6V5UFBZ',
            'refresh_token' => 'CvTRO1GOBIsr4ZzMDm2RSwsGASsrSRbx',
            // 'list_id'     => 92
        ]);
        $lead = Lead::find(22);

        $response = $infusionsoft->delete($lead);
        print_r(" print_R, el contacto email es: ---- : " . $response);
        dd("El response dd: ", $response);
        //print_r($response);

    }

    public function test_trasfer_activeTrail_keap()
    {
        //conect api ACTIVE TRAIL 
        //Search on active trail : if exist 
        //conect api Keap
        //insert on Keap : if response successfull :else dont delete nothing insert keap
        //delete on active trail  



    }

    public function transferActiveTrailToMailchimp($listEmails)
    {
        $active_trail = $this->initActiveTrail();

        $keap = $this->initKeap();

        for ($i = 0; $i < count($listEmails); $i++) {
            //buscar en activetrail
            $object_active_trail = $active_trail->getOneElement($listEmails[$i]);

            //insert on keap
            $lead = Lead::find(1);
            $keap->push($lead); //review


            if (!is_null($object_active_trail)) {
                //eliminar activetrail
                $active_trail->deleteMember($object_active_trail['id']); //'52069519' o 52063716
                $active_trail->deleteContact($object_active_trail['id']);
            }
        }
    }

    public function initActiveTrail()
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        return $active_trail;
    }

    public function initKeap()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => 'ywAwAEtf9VPfq7jKU8MIY6V5UFBZ',
            'refresh_token' => 'CvTRO1GOBIsr4ZzMDm2RSwsGASsrSRbx',
            // 'list_id'     => 92
        ]);
        return $infusionsoft;
    }
}
