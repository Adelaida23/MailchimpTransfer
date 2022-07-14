<?php

namespace Tests\Feature;

use App\Libraries\Mailchimp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class mailchimpTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_mailchimp_transfer()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/mailchimp/transfer/index');
        $response->assertStatus(200);
        $response->assertViewIs('Mailchimp.Transfer.index');
    }

    public function test_form()
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

    public function test_ping_mailchimp()
    {
        $client = new \MailchimpMarketing\ApiClient();
        $client->setConfig([
            'apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12',
            'server' => 'us12',
        ]);
        $response = $client->ping->get();
        print_r($response);
    }

    public function test_ping()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->ping();
        print_r($response);
    }
    public function test_get_list()
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
    public function test_search_on_mailchimp()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        // $lista_elements = $mailchimp->getListMembersInformation("8100a4643a");        
        $objetoMailchimp = $mailchimp->getOneElement("hzhm1997@gmail.com", "8100a4643a");
        print_r($objetoMailchimp);
    }

    public function test_search_delete_insert_One_email()
    {
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
}
