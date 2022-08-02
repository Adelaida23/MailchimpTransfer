<?php

namespace App\Http\Controllers;

use App\Libraries\ActiveTrail;
use App\Libraries\Keap;
use App\Libraries\Mailchimp;
use App\Models\ESP;
use App\Models\ESPAccount;
use App\Models\EspsRecords;
use App\Models\Lead;
use Exception;
use Illuminate\Http\Request;

class MailchimptransferController extends Controller
{
    public function index()
    {

        return view('mailchimp.transfer.index');
    }

    public function indexSubscribe()
    {
        return view('mailchimp.subscribe');
    }

    public function indexMailToActive()
    {
        return view('mailchimp.transfer.mailchimp_activetrail');
    }


    public function storeMailchimpToMailchimp(Request $request)
    {
        $request->validate(
            [
                'origin'  => 'required',
                'receives'  => 'required',
                'emails' => 'required'
            ],
            [
                'origin.required'   => 'You need select origin account  ',
                'receives.required' => 'You need select receives account',
                'emails.required'   => 'You need add emails to transfer',
            ]
        );

        $origin     = $request->origin;
        $receives   = $request->receives;
        $previus_emails = str_replace("\r", "",  str_replace(" ", "", $request->emails));
        $listEmails   = explode("\n", $previus_emails);

        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        //hash md5 or list member's email or contact_id
        for ($i = 0; $i < count($listEmails); $i++) {
            $mailchimp->archivateListMember('8100a4643a', $listEmails[$i]);
        }
        //crear
        $mailchimp = new Mailchimp(['apiKey' => '5ab1dfc294b23187ec937bf029340efb-us12', 'server' => 'us12']);
        for ($i = 0; $i < count($listEmails); $i++) {
            $mailchimp->addListOneMember('9097b7bd17', [
                "email_address" => $listEmails[$i],
                "status" => "subscribed",
            ]);
        }

        return redirect()->back()->with(['success' => 'Transfer success']);
    }


    public function storeSubscribe(Request $request)
    {
        $request->validate(
            [
                'origin'  => 'required',
                'emails' => 'required',
            ],
            [
                'origin.required'   => 'You need select origin account  ',
                'emails.required'   => 'You need add email to subscribe',
            ]
        );

        $origin     = $request->origin;
        $res = false;

        $previus_emails = str_replace("\r", "",  str_replace(" ", "", $request->emails));
        $listEmails   = explode("\n", $previus_emails);

        //$mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $mailchimp = $this->initMailchimpOrigin();
        if (!is_null($mailchimp)) {
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
                    $res = true;
                } //else $res = false;
            }
            $res = true;
        }
        if ($res) {
            return redirect()->back()->with(['success' => 'Subscribed successfull']);
        }
        return redirect()->back()->with(['error' => 'Has error on response. Dont create']);
    }


    public function storeTransfer(Request $request)
    {
        $request->validate(
            [
                'origin'  => 'required',
                'receives' => 'required',
                'emails' => 'required',
            ],
            [
                'origin.required'   => 'You need select origin account  ',
                'receives.required'   => 'You need select destinate account  ',
                'emails.required'   => 'You need add email to transfer',
            ]
        );

        $bandera_insert = false;

        $origin     = $request->origin;
        $receives   = $request->receives;

        $previus_emails = str_replace("\r", "",  str_replace(" ", "", $request->emails));
        $listEmails   = explode("\n", $previus_emails);

        $list_ids = ['8100a4643a', '75188', '92'];

        $bandera = false;
        $espAccount_origin = ESPAccount::find($origin);
        $esp_origin = ESP::find($espAccount_origin->esp_id);

        $espAccount_receives = ESPAccount::find($receives);
        $esp_receives = ESP::find($espAccount_receives->esp_id);

        $origin_class = $esp_origin->class;

        $receive_class = $esp_receives->class;

        $api_origen = EspsRecords::getApiOrigin($origin);
        if ($api_origen == 'mc_id') {
            $list_id_origin = $list_ids[0];
        } else if ($api_origen == 'at_id') {
            $list_id_origin = $list_ids[1];
        } else if ($api_origen == 'keap_id') {
            $list_id_origin = $list_ids[2];
        }

        $api_receive = EspsRecords::getApiReceive($receives);
        if ($api_receive == 'mc_id') {
            $list_id_receive = $list_ids[0];
        } else if ($api_receive == 'at_id') {
            $list_id_receive = $list_ids[1];
        } else if ($api_receive == 'keap_id') {
            $list_id_receive = $list_ids[2];
        }


        //return $espAccount_origin;

        $api_origin = new $origin_class([

            'apiKey' => $espAccount_origin->key,
            'server' => $espAccount_origin->server,
            'list_id' => $list_id_origin,
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
            'list_id' => $list_id_receive,
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
                            $bandera_insert = true;
                        }
                    } else if ($api_receive == 'at_id') {
                        $at_campos = $response->json();
                        if (!empty($at_campos['id'])) {
                            EspsRecords::create([
                                'email' => $at_campos['email'], // 
                                'at_id' => $at_campos['id']
                            ]);
                            $bandera_insert = true;
                        }
                    } else if ($api_receive == 'keap_id') {
                        if (!empty($response['id'])) {
                            EspsRecords::create([
                                'email'   => $response->email_addresses[0]['email'], // $response['email'], //??
                                'keap_id' => $response['id']
                            ]);
                            $bandera_insert = true;
                        }
                    }
                    if ($bandera_insert) {
                        $api_origin->delete($object_records_origin); //do delete all
                        //$api_origin->archivateListMember($list_id, $object_records_origin->email); //do delete all
                        $object_records_origin->delete();
                        return redirect()->back()->with(['success' => 'Subscribed successfull']);
                    }
                    //  }  //" else no recupera respuesta";
                }
            }
        }
        return redirect()->back()->with(['error' => 'Has error on response. Dont create']);
    }


    public function initMailchimpOrigin()
    {
        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $response = $mailchimp->ping();
        if (!empty($response->health_status)) {
            return $mailchimp;
        } else {
            return null;
        }
    }
}
