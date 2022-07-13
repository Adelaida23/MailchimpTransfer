<?php

namespace App\Http\Controllers;

use App\Libraries\Mailchimp;
use Illuminate\Http\Request;

class MailchimptransferController extends Controller
{
    public function index()
    {

        return view('Mailchimp.Transfer.index');
    }

    public function store(Request $request)
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

        //buscar

        //eliminar
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
        // return redirect()->route('transfer');
    }

    public function registerDestinate()
    {
    }
}
