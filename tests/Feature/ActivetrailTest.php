<?php

namespace Tests\Feature;

use App\Libraries\ActiveTrail;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActivetrailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_active_trail_insert() //insert
    {
        $lead = Lead::find(6);
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);

        $response = $active_trail->push($lead);
        $at_camps = $response->json();
        print_r($at_camps);
    }

    public function test_getGroupList() //information group
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getGroup(75188);
        $at_camps = $response->json();
        print_r($at_camps);
    }
    public function test_get_elements_group() //get information elements group
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getElementsGroup();
        $at_camps = $response->json();
        print_r($at_camps);
    }

    public function test_get_one_member_group() //get information one member
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getOneMemberGroup('52063716'); //'52069519' o 52063716
        $at_camps = $response->json();
        print_r($at_camps);
    }



    public function test_get_one_element_mailchimp()
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getOneElement('mcelrrroy.kathy@yahoo.com');
        print_r($response);
    }

    public function test_get_contact_id()
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->getElementsGroup();
        $elements = $response->json();

        $correo = "adhel1997@gmail.com"; //hsthenry3244@gmail.com,  //adhel1997@gmail.com
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

    public function test_deleteContact() //delete contanct and later  delete member 
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->deleteContact('52114510');
        $elements = $response->json();
        print_r($elements);
    }
    public function test_delete_element_group() //delete member 
    {
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->deleteMember('52069519'); //'52069519' o 52063716
        $at_camps = $response->json();
        print_r($at_camps);
    }

    //test insert or delete repit
    public function test_insert_with_email_is_exist_AT()
    { //insert or update seconde time asigned id one and first time
        $lead = Lead::find(30);
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);

        $response = $active_trail->push($lead);
        $at_camps = $response->json();
        print_r($at_camps);
    }

    public function test_delete_failed_AT() //delete using id contact
    { //siempre manda true 
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        $response = $active_trail->deleteMember('53558350'); //'52069519' o 52063716
        $at_camps = $response->json();
        print_r($at_camps);

        $response = $active_trail->deleteContact('53558350');
        $elements = $response->json();
        print_r($elements);
    }
}
