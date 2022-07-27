<?php

namespace App\Libraries;

use Infusionsoft\Infusionsoft;
use App\Models\ESPAccount;
use Infusionsoft\Token;

class Keap
{

	public $client = null;
	protected $token = null;
	protected $esp_account_id;
	protected $list_id;

	/**
	 * @param Array | clientId, clientSecret, access_token, refresh_token, esp_account_id
	 * Sets object for Infusion and Token
	 * @return void
	 */
	public function __construct($config = [])
	{
		$this->esp_account_id = $config['esp_account_id'] ?? 3;
		$this->client = new Infusionsoft([
			'clientId' => $config['client_id'],
			'clientSecret' => $config['client_secret'],
			'redirectUri'  => 'http://example.com'
		]);

		$this->token = new Token([
			'access_token' => $config['access_token'],
			'refresh_token' => $config['refresh_token'],
			'expires_in' => 86400
		]);
		$this->list_id = $config['list_id'] ?? 103;
		$this->client->setToken($this->token);
		$this->getRefreshToken();
	}

	//public function push($lead, $suppressed)
	public function push($lead)
	{
		$options = [
			'email_addresses' => [
				['email' => $lead->email, 'field' => 'EMAIL1']
			],
			'given_name' => $lead->first_name,
			'family_name' => $lead->last_name,
			'opt_in_reason' => 'Contact gave explicit permission'
		];
		if ($this->checkDuplicate($lead->email)) {
			$contact = $this->client->contacts()->where('email', $lead->email)->first();
			unset($contact->tag_ids);
			unset($contact->ScoreValue);
			unset($contact->last_updated_utc_millis);
			$contact->fill([
				'given_name' => $lead->first_name,
				'family_name' => $lead->last_name,
				'opt_in_reason' => 'Contact gave explicit permission'
			]);
			$contact->save();
			$contact->addTags([$this->list_id]);
		} else {
			$contact = $this->client->contacts()->create($options, false);
			$contact->addTags([$this->list_id]);
		}
		$this->updateEmailOPT($lead->email);
		return $contact;
	}

	/*
	public function deleteContact($lead){
		$contact  = $this->client->contacts()->where('email', $lead->email)->first();
		$response = $contact->deleteContact();
		return $response;
	}
	*/

	public function getMergeFields($list_id)
	{
		return false;
	}

	public function getMergeField($object, $advertiser)
	{
		return false;
	}

	public function updateEmailOPT($email)
	{
		$client = \Illuminate\Support\Facades\Http::withToken($this->client->getToken()->accessToken);
		$client->put("https://api.infusionsoft.com/crm/rest/v1/emailAddresses/$email", [
			'opted_in' => TRUE,
			'reason' => 'Company gave explicit permission'
		]);
	}
	public function create($email)
	{
		$client = \Illuminate\Support\Facades\Http::withToken($this->client->getToken()->accessToken);
		$client->post("https://api.infusionsoft.com/crm/rest/v1/email/$email", [
			'emailWithContent' => '<h1>Hello World</h1>',

		]);
	}

	public function delete($lead)
	{
		$client   = \Illuminate\Support\Facades\Http::withToken($this->client->getToken()->accessToken);
		if ($this->checkDuplicate($lead->email)) {
			$contact = $this->client->contacts()->where('email', $lead->email)->first();
			$contact_decod = json_decode($contact);
			
			$response = $client->delete("https://api.infusionsoft.com/crm/rest/v1/contacts/$contact_decod->id");
			return $response;
		} else {
			return "Contact dont exist";
		}
	}

	public function getCount($list_id)
	{
		return (int)0;
	}

	public function getAllContacts()
	{
		return $this->client->contacts()->all();
	}

	protected function checkDuplicate($email)
	{
		$data = $this->client->contacts()->where('email', $email)->get();
		return (count($data) > 0);
	}

	protected function getRefreshToken()
	{
		$esp_account = ESPAccount::find($this->esp_account_id);
		$newToken = $this->client->refreshAccessToken();
		$esp_account->fill([
			'access_token' => $newToken->accessToken,
			'refresh_token' => $newToken->refreshToken
		]);
		$esp_account->save();
	}
}
