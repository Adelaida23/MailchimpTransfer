<?php

namespace App\Http\Controllers;

use App\Libraries\ActiveTrail;
use App\Libraries\Mailchimp;
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

        if ($origin == 'mailchimp') {

            $this->mailchimpToActivetrail($listEmails);

            return redirect()->back()->with(['success' => 'Transfer successfull: Mailchimp to Active Trail']);
        }

        if ($origin == 'active_trail') {

            $this->activeTrailToMailchimp($listEmails);

            return redirect()->back()->with(['success' => 'Transfer successfull: Active Trail to Mailchimp']);
        }

        
    }

    public function mailchimpToActivetrail($listEmails)
    {
        $list_id = '8100a4643a';

        $mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $mailchimp = $this->initMailchimpOrigin();
        /*
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        */
        $active_trail = $this->initActiveTrail();

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
            //$object_active_trail = $active_trail->getOneElement($listEmails[$i]);
            //if (is_null($object_active_trail)) {
            $active_trail->insertElement($listEmails[$i]);
            // }
        }
    }

    public function activeTrailToMailchimp($listEmails)
    {
        $list_id = '8100a4643a';
        //$group_id = '75188';

        /*
        $active_trail = new ActiveTrail([
            'token' => '0X203B6AD2BBD3DF03434AE455A95F261A8FA40E0B192209DD2DBD9F3BCAD742A70217E44BB13E00A46A01C7747E03D82C',
            'list_id' => '75188'
        ]);
        */
        $active_trail = $this->initActiveTrail();

        //$mailchimp = new Mailchimp(['apiKey' => 'e6ce965275b2c237e341f3876d34f802-us12', 'server' => 'us12']);
        $mailchimp = $this->initMailchimpOrigin();
        for ($i = 0; $i < count($listEmails); $i++) {
            //buscar en activetrail
            $object_active_trail = $active_trail->getOneElement($listEmails[$i]);
            // print_r($object_active_trail); // 52110187

            if (!is_null($object_active_trail)) {
                //eliminar activetrail
                // print_r($object_active_trail['id']);
                $active_trail->deleteMember($object_active_trail['id']); //'52069519' o 52063716
                $active_trail->deleteContact($object_active_trail['id']);
            }

            //insert mailchimp if not exist
            // $objetoMailchimp = $mailchimp->getOneElement($list_id, $listEmails[$i]);
            // if (is_null($objetoMailchimp)) {
            $mailchimp->addListOneMember($list_id, [
                "email_address" => $listEmails[$i],
                "status" => "subscribed",
            ]);
            // }
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
}
