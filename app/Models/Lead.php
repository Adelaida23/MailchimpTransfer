<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\LeadFactory;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
	use HasFactory;

	protected $fillable = [
		'email',
		'md5',
		'sha256',
		'last_ip_address',
		'gender',
		'geo_location',
		'domain',
		'domain_country',
		'first_name',
		'last_name',
		'ip',
		'state',
		'zip_code',
		'country',
		'status',
		'reason',
		'subid1',
		'subid2',
		'subid3',
		'emailoversight_check'
	];

	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
	];

	protected $selectFields = [
		'email' => 'Email',
		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'ip' => 'IP Address',
		'subid1' => 'Sub 1',
		'subid2' => 'Sub 2',
		'subid3' => 'Sub 3',
		'domain' => 'Domain',
	];

	public function partner()
	{
		return $this->belongsToMany(Partner::class, 'leads_partners');
	}

	public function lists()
	{
		return $this->hasMany(LeadList::class, 'lead_id', 'id');
	}

	public function hasPartner()
	{
		return count($this->partner);
	}

	public function selectFields()
	{
		return $this->selectFields;
	}

	public static function boot()
	{
		parent::boot();
		static::deleting(function ($lead) {
			$lead->partner()->delete();
		});
	}

	public static function findFromHash($hash)
	{
		return Lead::where("md5", $hash)->first() ?? null;
	}

	public static function allHashesLeads()
	{
		$hashes = DB::table('leads')
			->select('leads.md5', 'leads.email')
			->get();
		return $hashes;
	}

	public static function searchOneHash($hash_text)
	{
		$obj_suppression = Lead::where('md5', '=', $hash_text)->first();
		// if($obj_suppression != "")
		return $obj_suppression;
	}

	//method ade 1 add for transfer
	public static function searchLead($email)
	{
		$lead = Lead::where('email', $email)->first();

		if (!empty($lead->id)) {
			return $lead;
		}
		return null;
	}
}
