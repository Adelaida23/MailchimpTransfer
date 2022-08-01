<?php

namespace Tests\Feature;

use App\Libraries\Mailchimp;
use App\Models\EspsRecords;
use ErrorException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\returnSelf;

class mailchimpTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_mailchimp_transfer_go_view()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/mailchimp/transfer/index');
        $response->assertStatus(200);
        $response->assertViewIs('Mailchimp.Transfer.index');
    }

    public function test_form_ruta_post_mailchimp_transfer()
    {
        $this->withoutExceptionHandling();
        $response = $this->post(
            '/mailchimp/transfer',
            [
                'origin'   => 'adelaida.molinar1997@gmail.com',
                'receives' => 'adhel1997@gmail.com',
                'emails'   => 'babyflory23@gmail.com'
            ]
        );

        $response->assertStatus(200);
        //$response->assertSeeText('success');
    }

    public function test_ping_mailchimp_with_apiclient()
    {
        $client = new \MailchimpMarketing\ApiClient();
        $client->setConfig([
            'apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12',
            'server' => 'us12',
        ]);
        $response = $client->ping->get();
        print_r($response);
    }

    public function test_ping_with_library()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->ping();
        print_r($response);
    }
    //change name do descriptivo
    public function test_get_list_emails_on_list()
    { //otener datos list include list_id
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->getLists();
        print_r($response);
    }

    public function test_addMemberList()
    {
        $client = new \MailchimpMarketing\ApiClient();
        $client->setConfig([
            'apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12',
            'server' => 'us12',
        ]);

        $response = $client->lists->addListMember('8100a4643a', [
            "email_address" => "babyflory23@gmail.com",
            "status" => "subscribed",
        ]);
        print_r($response);
    }
    public function test_getMembersList()
    {
        $client = new \MailchimpMarketing\ApiClient();
        $client->setConfig([
            'apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12',
            'server' => 'us12',
        ]);
        $response = $client->list->getListMembersInfo("8100a4643a");
        dd($response);
    }
    public function test_getListName()
    {
        /*
        $data = array(
            'fields' => 'lists', // total_items, _links
            //'email' => 'misha@rudrastyh.com',
            'count' => 5, // the number of lists to return, default - all
            'before_date_created' => '2016-01-01 10:30:50', // only lists created before this date
            'after_date_created' => '2014-02-05' // only lists created after this date
        );
        */

        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->getListMembersInformation("8100a4643a");
        print_r($response);
        /*
        foreach ($response as $obj) {
            print_r($obj->email_address . " " . $obj->status . "\n");
        }
        */
    }


    public function testGetOneMember()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $reponse = $mailchimp->getOneMemberInfo("8100a4643a", " da6510be0808282bbca47dc7cc0fb631");
        print_r($reponse);
    }

    public function test_insertOneEmail()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->addListOneMember('8100a4643a', [
            "email_address" => "czuly1989@gmail.com",
            "status" => "subscribed",
        ]);
        print_r($response);
    }
    public function test_archivateOneEmail()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        //hash md5 or list member's email or contact_id
        $response = $mailchimp->archivateListMember('8100a4643a', "babyflo@gmail.com");
        print_r($response);
    }
    public function test_delete_permanent()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->deleteListMemberPermanent('8100a4643a', "paolacastillo@gmail.com");
        print_r($response);
    }

    public function test_find_one_element_list_mailchimp()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $lista_elements = $mailchimp->getListMembersInformation("8100a4643a");
        $correo = "hzhm1997@gmail.com"; //hsthenry3244@gmail.com,  //adhel1997@gmail.com
        $indice = 0;
        $object = null;
        $limite = count($lista_elements);

        while ($indice < $limite && $lista_elements[$indice]->email_address != $correo) {
            print_r('entro');
            $indice++;
        }

        if ($indice != $limite) {
            //imprimir encontrado
            $object = $lista_elements[$indice];
            print_r($lista_elements[$indice]);
        } else {
            print_r('no encontrado');
        }
    }
    public function search_on_mailchimp($list_id, $email)
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        // $lista_elements = $mailchimp->getListMembersInformation("8100a4643a");        
        $objetoMailchimp = $mailchimp->getOneElement("8100a4643a", "pat.chtensen@hcjpd.hctx.net");
        if (is_object($objetoMailchimp))
            print_r($objetoMailchimp);
        else print_r("email no encontrado");
    }



    //view Subscribe
    public function test_getViewSubscribe()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/mailchimp/subscribe/index');
        $response->assertStatus(200);
        $response->assertViewIs('Mailchimp.subscribe');
    }
    public function test_route_subscribe()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('mailchimp-subscribe');
        $response->assertOk();
    }
    public function test_get_key_server_account_esp()
    {
        //DB::table('esps_accounts')->where('email', $email)->first();
        // $this->config_status =  Config::where("key", 'eostatus')->first();
        //$this->config_key =  Config::where("key", 'eokey')->first();

    }

    public function test_ping_destinate()
    {
        $mailchimp = new Mailchimp(['apiKey' => '5ab1dfc294b23187ec937bf029340efb-us12', 'server' => 'us12']);
        $response = $mailchimp->ping();
        print_r($response);
    }
    public function test_get_list_destinate()
    { //otener datos list include list_id
        $mailchimp = new Mailchimp(['apiKey' => '5ab1dfc294b23187ec937bf029340efb-us12', 'server' => 'us12']);
        $response = $mailchimp->getLists();
        print_r($response);
    }

    public function test_addMemberList_destinate()
    {
        $client = new \MailchimpMarketing\ApiClient();
        $client->setConfig([
            'apiKey' => '5ab1dfc294b23187ec937bf029340efb-us12',
            'server' => 'us12',
        ]);

        $response = $client->lists->addListMember('9097b7bd17', [
            "email_address" => "babyflory23@gmail.com",
            "status" => "subscribed",
        ]);
        print_r($response);
    }

    //pasado on controller
    public function test_storeSubscribe_ultimo_ok() //test 28-07-22
    {

        $listEmails   = ['hsthenry3244@gmail.com'];
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        for ($i = 0; $i < count($listEmails); $i++) {
            $response = $mailchimp->addListOneMember('8100a4643a', [
                "email_address" => $listEmails[$i],
                "status" => "subscribed",
            ]);

            if ($response != false &&  !empty($response->unique_email_id)) { //solo si manda true: ok //solo inserta una vez un email, la segunda vez manda false
                //print_r($response->unique_email_id);
                EspsRecords::create([
                    'email' => $response->email_address, // 
                    'mc_id' => $response->unique_email_id //se agrega solo para darle un valor a mailchimp. Para borrar se ocupa email
                ]);
            }
        }
    }

    //success test ultimates

    //test delete with validate response 
    public function test_archivateOneEmail_validate_response()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        //hash md5 or list member's email or contact_id
        $response = $mailchimp->archivateListMember('8100a4643a', "babyflo@gmail.com");
        print_r($response);
    }

    public function test_ping_validate()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->ping();
        if (!empty($response->health_status)) {
            print_r($response->health_status);
        } else {
            print_r("dont ping");
        }
    }
    public function test_verified_exist_email()
    {
        $list_id = '8100a4643a';
        $email   = 'paolacastillo@gmail.com';
        $this->search_on_mailchimp($list_id, $email);
    }
    //test delete and insert repit
    public function test_insert_with_email_is_exist_mailchimp()
    { //when is exist the response is empty
        $listEmails = 'babyflory23@gmail.com';
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->addListOneMember('8100a4643a', [
            "email_address" => $listEmails,
            "status"        => "subscribed",
        ]);
        if (is_object($response)) {
            print_r("success");
        } else print_r("failed");
    }

    //search_on_mailchimp($list_id, $email)


    public function test_delete_failed_mailchimp()
    { //siempre confirma delete
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        //hash md5 or list member's email or contact_id
        //$response = $mailchimp->archivateListMember('8100a4643a', "santos@gmail.com");
        //print_r($response);
        $response = $mailchimp->deleteListMemberPermanent('8100a4643a', "paolacastillo@gmail.com");
        print_r($response);
        //dd("response");
    }
    /*
    public function test_delete_permanent2()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->deleteListMemberPermanent('8100a4643a', "paolacastillo@gmail.com");
        print_r($response);
    }
    */
}
