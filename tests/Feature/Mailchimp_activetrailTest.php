<?php

namespace Tests\Feature;

use App\Libraries\ActiveTrail;
use App\Libraries\Mailchimp;
use App\Models\EspsRecords;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Mailchimp_activetrailTest extends TestCase
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
    public function test_transfer_mailchimp_activeTrail_index()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('transfer/index');
        $response->assertStatus(200);
        //$response->assertSeeText('ade');
    }
    public function test_route_store_transfer_mailchimp_activetrail()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('transfer/mailchimp/to/activetrail', [
            'hola' => 'hola'
        ]);
        $response->assertStatus(200);
    }

    public function test_transfer_mailchimp_activetrail_store()
    {
        $listEmails = ['developper@gmail.com'];
        $list_id = '8100a4643a';

        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);

        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);

        for ($i = 0; $i < count($listEmails); $i++) {
            //eliminar en mailchimp si encuentra
            //buscar en mailchimp
            $objetoMailchimp = $mailchimp->getOneElement($list_id, $listEmails[$i]);
            if (!is_null($objetoMailchimp)) {
                $mailchimp->archivateListMember($list_id, $listEmails[$i]);
            }

            //active trail: insert
            /*insert with lead
            $lead = Lead::where('email', '=', $listEmails[$i])->first();
            if (!is_null($lead)) {
                $response = $active_trail->push($lead);
                $at_camps = $response->json();
            }
            */
            //insert with email
            $active_trail->insertElement($listEmails[$i]);
        }
    }

    public function test_transfer_activetrail_to_mailchimp_store_antiguo()
    {
        //$listEmails = ['hsthenry3244@gmail.com', 'Hudziak100@yahoo.com'];
        /*$listEmails = ['Hudziak100@yahoo.com', 'hsthenry3244@gmail.com',
                       'developper@gmail.com','czuly1989@gmail.com','cdautorio@gmail.com',
                       'arturodelosangeles@hotmail.com'];
                       */
        $listEmails = [
            'Hudziak100@yahoo.com', 'hsthenry3244@gmail.com',
            'developper@gmail.com'
        ];

        $list_id_origin = '8100a4643a';
        //$group_id = '75188';

        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);

        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);

        for ($i = 0; $i < count($listEmails); $i++) {
            //buscar en activetrail
            $object_active_trail = $active_trail->getOneElement($listEmails[$i]);
            // print_r($object_active_trail); // 52110187

            if (!is_null($object_active_trail)) {
                //eliminar activetrail
                // print_r($object_active_trail['id']);
                $active_trail->deleteMember($object_active_trail['id']); //'52069519' o 52063716
            }

            //insert mailchimp
            $response = $mailchimp->addListOneMember($list_id_origin, [
                "email_address" => $listEmails[$i],
                "status" => "subscribed",
            ]);
            //print_r($response);

        }
    }

    public function test_transferActiveTrailToMailchimp_recent_ultimate_ok() //test : 28-07-22
    {
        $listEmails = ['cdautorio@gmail.com'];
        $mail_list_id = '8100a4643a'; //do dinamic since db

        $active_trail = $this->initActiveTrail();
        $mailchimp = $this->initMailchimpOrigin();

        for ($i = 0; $i < count($listEmails); $i++) {
            //buscar en activetrail
            //$object_active_trail = $active_trail->getOneElement($listEmails[$i]);
            $object_active_trail = EspsRecords::getActiveTrail('at_id')->searchEmail($listEmails[$i])->first();
            //insert on api destinate only if not exist. If existe return false
            $response = $mailchimp->addListOneMember($mail_list_id, [
                "email_address" => $listEmails[$i],
                "status" => "subscribed",
            ]);
            if ($response != false &&  !empty($response->unique_email_id)) { //solo si manda true: ok //solo inserta una vez un email, la segunda vez manda false
                //print_r($response->unique_email_id);

                EspsRecords::create([
                    'email' => $response->email_address, // 
                    'mc_id' => $response->unique_email_id //1111 change //se agrega solo para darle un valor a mailchimp. Para borrar se ocupa email
                ]);

                if (!is_null($object_active_trail)) {
                    //eliminar activetrail
                    $active_trail->deleteMember($object_active_trail['at_id']); //'52069519' o 52063716
                    $active_trail->deleteContact($object_active_trail['at_id']);
                    $object_active_trail->delete(); //si eliminar testeado
                }
            } else {
                print_r("no se agrego");
            }
        }
    }

    public function initMailchimpOrigin()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        return $mailchimp;
    }
    public function initActiveTrail()
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        return $active_trail;
    }


    public function test_transferMailchimpToActivetrail_new_ok() //test: 28-07-2022
    {
        $listEmails = ['hsthenry3244@gmail.com'];
        $list_id = '8100a4643a';

        //$mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $mailchimp = $this->initMailchimpOrigin();
        $active_trail = $this->initActiveTrail();

        for ($i = 0; $i < count($listEmails); $i++) {
            //eliminar en mailchimp si encuentra
            //buscar en mailchimp entrando a la api para hacer la llamada
            //$objetoMailchimp = $mailchimp->getOneElement($list_id, $listEmails[$i]);

            //verificar si existe en mailchimp with table
            $object_active_trail = EspsRecords::getActiveTrail('mc_id')->searchEmail($listEmails[$i])->first();
            $response = $active_trail->insertElement($listEmails[$i]);
            $at_campos = $response->json();
            if (!empty($at_campos['id'])) {
                EspsRecords::create([
                    'email' => $at_campos['email'], // 
                    'at_id' => $at_campos['id']
                ]);
                if (!is_null($object_active_trail)) {
                    //return $object_active_trail->email;
                    //$mailchimp->archivateListMember($list_id, $listEmails[$i]);
                    $mailchimp->archivateListMember($list_id, $object_active_trail->email);
                    $object_active_trail->delete();
                }
                return "success";
            }
            return "no recupera respuesta";
        }
    }
}
