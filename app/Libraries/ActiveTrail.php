<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ActiveTrail {
	protected $client;
	protected $group;

	public function __construct($config = []){
		$this->client = Http::withToken($config['token'],'');
		$this->group = $config['list_id'];
	}

	public function campaigns($options = []) {
		return $this->client->get('https://webapi.mymarketing.co.il/api/campaigns',$options);
	}
	/**
	* @param $options | All data related to new campaign
	* $options => [
		scheduling => [
			is_sent => boolen,
			schedule_date_utc => date,
			is_limit_to_confirm => boolean
		],
		details => [
			name => String,
			subject => String,
			preheader => String
		],
		design => [
			content => String | HTML,
			language_type => http://webapi.mymarketing.co.il/api/docs/User/ResourceModel?modelName=SystemLanguageType,
			is_add_print_email => boolean,
			is_auto_css_inliner => boolean
		],

	]
	* @return Array | With data stored in Active Trail API
	**/
	public function addCampaign($options = []) {
		return $this->client->get('https://webapi.mymarketing.co.il/api/campaigns');
	}

	public function contacts($options = []) {
		return $this->client->get('https://webapi.mymarketing.co.il/api/contacts',$options);
	}

	public function getMemberData($contact_id) {
		return $this->client->get("https://webapi.mymarketing.co.il/api/contacts/$contact_id");
	}

	public function push($lead, $field = FALSE) {
        //echo "at push $lead";die("555");
        $data = [
            'email'=>$lead->email,
            'first_name' => $lead->first_name??"",
            'last_name' => $lead->last_name??"",
            'status' => 1
        ];
        /*if (is_array($field)) {
            foreach ($field as $f) {
                $data[$f] = 'TRUE';
            }
        }*/
        return $this->client->post('https://webapi.mymarketing.co.il/api/groups/'.$this->group.'/members',$data);
	}

	protected function getGroup($list_id) {
		return $this->client->get("https://webapi.mymarketing.co.il/api/groups/$list_id", []);
	}

	public function updateContact($contact_id, $data = []) {
		$data['status'] = $data['status'] ?? 1;
		return $this->client->put("https://webapi.mymarketing.co.il/api/contacts/$contact_id",$data);
	}

	public function getMergeFields() {
	    return $this->client->get('https://webapi.mymarketing.co.il/api/account/contactFields');
    }

    public function getMergeField($arrayFields = [], $advertiser=null) {
        if (!empty($arrayFields)){
            foreach ($arrayFields as $field) {
                if ($field['field_display_name'] === "s+$advertiser") {
                    return strtolower($field['field_source_column']);
                }
            }
        } else {
            return false;
        }
    }

	public function getCount($list_id) {
		$group = $this->getGroup($list_id);
		$group = $group->json();
		return $group['counter'];
	}
}
