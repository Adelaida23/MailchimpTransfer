<?php

namespace Tests\Feature;

use App\Libraries\ActiveTrail;
use App\Libraries\Keap;
use App\Models\EspsRecords;
use App\Models\Lead;
use Exception;
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
    /*
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    */

    public function test_keap_ping()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => '8eiAc1ewn6FNxeH9kB0ztoAMyDDG',
            'refresh_token' => 'joct87KhAygfaomqpN6GGoF8DCPtKHUJ',
        ]);
    }

    public function test_do_push()
    {
        $infusionsoft = new Keap([
            // 'esp_account_id' => '', //optional add
            'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
            'client_secret' => 'Tp60FxwTafCvAhpX',
            'access_token'  => 'gnq7WXpfX6Dr2nZLZo4GxPUnoAAY',
            'refresh_token' => 'ikybvY6zcGbpmzoPlOkhmnKoPTYQpjPR',
            'list_id'     => 92
        ]);
        $lead = Lead::find(5);
        $response = $infusionsoft->push($lead);
        //print_r($response);
        print_r($response['id']);

        /*
        $statusCode = $response->getStatusCode();
        //echo $statusCode;die();
        if ($statusCode != 200) {
            print_r(null);
        } else {
            print_r($response);
        }
        */
        //$content = json_decode($response->getBody(), true);
        //return $content;

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

    public function test_transferActiveTrailToKeap()
    {
        $listEmails = ['hsthenry3244@gmail.com'];
        //conect api ACTIVE TRAIL 
        //Search on active trail : if exist 
        //conect api Keap
        //insert on Keap : if response successfull :else dont delete nothing insert keap
        //delete on active trail  
        $active_trail = $this->initActiveTrail();
        $keap = $this->initKeap();

        for ($i = 0; $i < count($listEmails); $i++) {
            //buscar en activetrail
            $object_active_trail = EspsRecords::getActiveTrail('at_id')->searchEmail($listEmails[$i])->first();

            //insert on keap
            $lead = Lead::searchLead($listEmails[$i]); //new method on lead

            if (!is_null($lead)) {
                $response = $keap->push($lead); //review
                if (!empty($response['id'])) {
                    // print_r($response->email_addresses[0]['email']);

                    //insertar en table bd local
                    EspsRecords::create([
                        'email'   => $response->email_addresses[0]['email'], // $response['email'], //??
                        'keap_id' => $response['id']
                    ]);

                    if (!is_null($object_active_trail)) {
                        //eliminar activetrail
                        $active_trail->deleteMember($object_active_trail['at_id']); //'52069519' o 52063716
                        $active_trail->deleteContact($object_active_trail['at_id']);
                        $object_active_trail->delete(); //si eliminar testeado
                    }
                } else {
                    "no se agregó el contacto";
                }

                //recuperar respuesta push si fue exitoso
                //insertar en la bd local y continue with delete en active trail
            }
        }
    }

    public function test_transfer_Keap_to_ActiveTrail()
    {
        $listEmails = ['cdautorio@gmail.com'];
        //conect api ACTIVE TRAIL 
        //Search on active trail : if exist 
        //conect api Keap
        //insert on Keap : if response successfull :else dont delete nothing insert keap
        //delete on active trail  
        $active_trail = $this->initActiveTrail();
        $keap = $this->initKeap();

        for ($i = 0; $i < count($listEmails); $i++) {
            //buscar en activetrail
            $object_keap = EspsRecords::getActiveTrail('keap_id')->searchEmail($listEmails[$i])->first();

            //insert on keap
            $lead = Lead::searchLead($listEmails[$i]); //new method on lead
            $response = $active_trail->insertElement($listEmails[$i]);
            $at_campos = $response->json();
            if (!empty($at_campos['id'])) {
                EspsRecords::create([
                    'email' => $at_campos['email'], // 
                    'at_id' => $at_campos['id']
                ]);

                if (!is_null($object_keap) && !is_null($lead)) {
                    //eliminar activetrail
                    $keap->delete($lead);
                    $object_keap->delete(); //si eliminar testeado
                }
                return true;
            } else {
                return false; //"no se agregó el contacto";
            }

            //recuperar respuesta push si fue exitoso
            //insertar en la bd local y continue with delete en active trail
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
            'access_token'  => 'TihjJXYwgvoaJXAkEm1clDoRqPvk',
            'refresh_token' => 'Iwa5imAvxljhAIkMYnGAFrdYsGIDZjrn',
            'list_id'     => 92
        ]);
        return $infusionsoft;
    }

    public function test_escopes_query()
    {
        //$users = EspsRecords::getActiveTrail('keap_id')->get(); ok
        $users = EspsRecords::getActiveTrail('at_id')->searchEmail("zzzzzzz@gmail.com")->first();
        print_r($users->at_id); //funciona
        //print_r($users['at_id']); //funciona
        //$object_active_trail->at_id
    }


    public function test_consulta()
    {
        $lead = Lead::find(5);
        print_r($lead);
    }

    public function test_ping_correct_token_validate()
    {
        try {
            $infusionsoft = new Keap([
                // 'esp_account_id' => '', //optional add
                'client_id'     => '9G5psoBL1cJ6cHvK8ZKYB6NIF1MQ7zAG',
                'client_secret' => 'Tp60FxwTafCvAhpX',
                'access_token'  => 'iiijgorOluAaDj3Y73LRCmkJ6f7t',
                'refresh_token' => 'gWHOAEJOP9vbuCSLUyD6GRp3IzuiZ7YZ',
                'list_id'     => 92
            ]);
            print_r($infusionsoft);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function test_insert_with_email_is_exist(){

    }

    public function test_delete_failed(){
        
    }
}
