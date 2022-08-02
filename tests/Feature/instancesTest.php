<?php

namespace Tests\Feature;

use App\Libraries\ActiveTrail;
use App\Libraries\Mailchimp;
use App\Models\ESP;
use App\Models\ESPAccount;
use App\Models\EspsRecords;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class instancesTest extends TestCase
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


    public function test_transferDoGeneric()
    {
        $list_id = ['8100a4643a', '75188', '92'];
        $listEmails = ['aaronosei@yahoo.com'];
        $origin     = 18; //MC: 18, AT:19, KEAP: 20

        $receive   = 19; //AT
        //$list_id = '8100a4643a';
        $bandera = false;
        $espAccount_origin = ESPAccount::find($origin);
        $esp_origin = ESP::find($espAccount_origin->esp_id);

        $espAccount_receives = ESPAccount::find($receive);
        $esp_receives = ESP::find($espAccount_receives->esp_id);

        $origin_class = $esp_origin->class;

        $receive_class = $esp_receives->class;

        $api_origin = new $origin_class([
            'apiKey' => $espAccount_origin->key,
            'server' => $espAccount_origin->server,
            'list_id' => $list_id[0],
            'client_id' => $espAccount_origin->client_id,
            'client_secret' => $espAccount_origin->client_secret,
            'token' => $espAccount_origin->token,
            'access_token' => $espAccount_origin->access_token,
            'refresh_token' => $espAccount_origin->refresh_token,
            'esp_account_id' => $espAccount_origin->id,
            'url' => $espAccount_origin->url,
            'user' => $espAccount_origin->user,
            'user_token' => $espAccount_origin->user_token
        ]);



        $api_receives = new $receive_class([
            'apiKey' => $espAccount_receives->key,
            'server' => $espAccount_receives->server,
            'list_id' => $list_id[1],
            'client_id' => $espAccount_receives->client_id,
            'client_secret' => $espAccount_receives->client_secret,
            'token' => $espAccount_receives->token,
            'access_token' => $espAccount_receives->access_token,
            'refresh_token' => $espAccount_receives->refresh_token,
            'esp_account_id' => $espAccount_receives->id,
            'url' => $espAccount_receives->url,
            'user' => $espAccount_receives->user,
            'user_token' => $espAccount_receives->user_token
        ]);

        for ($i = 0; $i < count($listEmails); $i++) {
            //verificar si existe en mailchimp with table
            $api_origen = EspsRecords::getApiOrigin($origin);
            $api_receive = EspsRecords::getApiReceive($receive);
            $object_records_origin = EspsRecords::getActiveTrail($api_origen)->searchEmail($listEmails[$i])->first();

            if (!is_null($object_records_origin)) {
                //$response = $api_receives->insertElement($listEmails[$i]); //do push all 3 apis
                $lead = Lead::searchLead($listEmails[$i]);
                if (!empty($lead)) {
                    $response = $api_receives->push($lead); //do push all 3 apis
                    if ($api_receive == 'mc_id') {
                        if ($response != false &&  !empty($response->unique_email_id)) { //solo si manda true: ok //solo inserta una vez un email, la segunda vez manda false
                            EspsRecords::create([
                                'email' => $response->email_address, // 
                                'mc_id' => $response->unique_email_id, //1111 change //se agrega solo para darle un valor a mailchimp. Para borrar se ocupa email
                                'list_id' => $response->list_id
                            ]);
                        }
                    } else if ($api_receive == 'at_id') {
                        $at_campos = $response->json();
                        if (!empty($at_campos['id'])) {
                            EspsRecords::create([
                                'email' => $at_campos['email'], // 
                                'at_id' => $at_campos['id']
                            ]);
                        } else $bandera = false; //"no recupera respuesta";
                    } else if ($api_receive == 'keap_id') {
                        if (!empty($response['id'])) {
                            EspsRecords::create([
                                'email'   => $response->email_addresses[0]['email'], // $response['email'], //??
                                'keap_id' => $response['id']
                            ]);
                        }
                    }

                    $api_origin->delete($object_records_origin); //do delete all
                    //$api_origin->archivateListMember($list_id, $object_records_origin->email); //do delete all
                    $object_records_origin->delete();
                    $bandera = true;
                    //  }  //" else no recupera respuesta";
                }
            }
        }
        return $bandera;
    }

    //verified MC TO AT

    public function test_verified_exist_email()
    {
        $list_id = '8100a4643a';
        $email   = 'aaronosei@yahoo.com';
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $objetoMailchimp = $mailchimp->getOneElement($list_id, $email);
        if (is_object($objetoMailchimp)) {
            print_r("si existe");
            print_r($objetoMailchimp);
        } else {
            print_r("email no encontradoooo");
        }
    }

    public function test_verified_exist_email_email_at()
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getElementsGroup();
        $elements = $response->json();

        $correo = "aaronosei@yahoo.com"; //hsthenry3244@gmail.com,  //adhel1997@gmail.com
        $indice = 0;
        $object = null;
        $limite = count($elements);

        while ($indice < $limite && $elements[$indice]['email'] != $correo) {
            print_r('entro');
            $indice++;
        }

        if ($indice != $limite) {
            //imprimir encontrado
            $object = $elements[$indice];
            print_r($elements[$indice]);
        } else {
            print_r('no encontrado');
        }
    }

    //verified AT TO KEAP

    public function test_verified_exist_email_email_at2()
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getElementsGroup();
        $elements = $response->json();

        $correo = "wperez1214@yahoo.com"; //hsthenry3244@gmail.com,  //adhel1997@gmail.com
        $indice = 0;
        $object = null;
        $limite = count($elements);

        while ($indice < $limite && $elements[$indice]['email'] != $correo) {
            print_r('entro');
            $indice++;
        }

        if ($indice != $limite) {
            //imprimir encontrado
            $object = $elements[$indice];
            print_r($object);
        } else {
            print_r('no encontrado');
        }
    }
}
