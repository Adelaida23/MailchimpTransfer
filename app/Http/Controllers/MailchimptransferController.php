<?php

namespace App\Http\Controllers;

use App\Libraries\ActiveTrail;
use App\Libraries\Keap;
use App\Libraries\Mailchimp;
use App\Models\EspsRecords;
use App\Models\Lead;
use Illuminate\Http\Request;

class MailchimptransferController extends Controller
{
    public function index()
    {

        return view('Mailchimp.Transfer.index');
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


    public function indexSubscribe()
    {
        return view('Mailchimp.subscribe');
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

        $previus_emails = str_replace("\r", "",  str_replace(" ", "", $request->emails));
        $listEmails   = explode("\n", $previus_emails);

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

        return redirect()->back()->with(['success' => 'Subscribed successfull']);
    }

    public function indexMailToActive()
    {
        return view('Mailchimp.Transfer.mailchimp_activetrail');
    }
    public function storeMailchimpToActivetrail(Request $request)
    {
        //  return true;
        $request->validate(
            [
                'origin'  => 'required',
                //'receives' => 'required',
                'emails' => 'required',
            ],
            [
                'origin.required'   => 'You need select origin account  ',
                //'receives.required'   => 'You need select destinate account  ',
                'emails.required'   => 'You need add email to transfer',
            ]
        );

        $origin     = $request->origin;
        $receives   = $request->receives; //verify names on blade
        $previus_emails = str_replace("\r", "",  str_replace(" ", "", $request->emails));
        $listEmails   = explode("\n", $previus_emails);

        if ($origin == 'mailchimp' && $receives == 'active_trail') {
            $respuesta = $this->transferMailchimpToActivetrail($listEmails);
            if ($respuesta) {
                return redirect()->back()->with(['success' => 'Transfer successfull: Mailchimp to Active Trail']);
            } else {
                return "error";
            }
        }

        if ($origin == 'active_trail' && $receives == 'mailchimp') {

            $response = $this->transferActiveTrailToMailchimp($listEmails);
            if ($response) {
                return redirect()->back()->with(['success' => 'Transfer successfull: Active Trail to Mailchimp']);
            } else {
                return "error";
            }
        }

        if ($origin == 'active_trail' && $receives == 'keap') {
            $response = $this->transferActiveTrailToKeap($listEmails);
            if ($response) {
                return redirect()->back()->with(['success' => 'Transfer successfull: Active Trail to  Keap']);
            } else {
                return "error";
            }
        }

        if ($origin == 'active_trail' && $receives == 'keap') {
            $response = $this->transfer_Keap_to_ActiveTrail($listEmails);
            if ($response) {
                return redirect()->back()->with(['success' => 'Transfer successfull: Keap to Active Trail']);
            } else {
                return "error";
            }
        }
    }


    public function transferMailchimpToActivetrail($listEmails)
    {
        //$listEmails = ['cdautorio@gmail.com'];
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
                return true;
            }
            return false; //"no recupera respuesta";
        }
    }



    public function transferActiveTrailToMailchimp($listEmails)
    {
        //        $listEmails = ['cdautorio@gmail.com'];
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
                return true;
            } else {
                return false; //print_r("no se agrego");
            }
        }
    }

    public function transferActiveTrailToKeap($listEmails)
    {
        //$listEmails = ['hsthenry3244@gmail.com'];
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
                    return true;
                } else {
                    return false; //"no se agregó el contacto";
                }

                //recuperar respuesta push si fue exitoso
                //insertar en la bd local y continue with delete en active trail
            }
        }
    }

    public function transfer_Keap_to_ActiveTrail($listEmails)
    {
        //$listEmails = ['cdautorio@gmail.com'];
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
}
