<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESPAccount extends Model {
    use HasFactory;

    protected $table = 'esps_accounts';

//    protected $fillable = ['esp_id','name',
//        'server','key','token','client_id','client_secret',
//        'access_token','refresh_token','url','user','user_token'];

    protected $guarded = [];


	public function lists() {
		return $this->hasMany(Lists::class, 'esp_account_id', 'id');
	}

	public function esp() {
		return $this->belongsTo(ESP::class, 'esp_id', 'id');
	}

    public function getEsp(){
        $esp = ESP::find($this->esp_id);
        return $esp;
    }
}
