<?php

class Mailchimp
{

    private static $instance = null;



    private function __construct()
    {
        DB::table('esps_accounts')->where('email', $email)->first();
        $this->config_status =  Config::where("key", 'eostatus')->first();
        $this->config_key =  Config::where("key", 'eokey')->first();
    }

    public function init()
    {
        $this->mailchimp = new \MailchimpMarketing\ApiClient();
        $this->mailchimp->setConfig([
            'apiKey' => $this->apiKey,
            'server' => $this->server
        ]);
    }
    
}
