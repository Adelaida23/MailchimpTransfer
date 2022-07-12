<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailchimptransferController extends Controller
{
    public function index(){

        return view('Mailchimp.Transfer.index');

    }

    public function store(){

    }

    public function getParams(Request $request)
    {
        $request->validate(
            [
                'origin'  => 'required',
                'receives'  => 'required',
                'emails' => 'required',
                'subject'  => 'required'
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
        return $listEmails;

        return redirect()->back()->with(['success' => 'Transfer success']);
    }
}
