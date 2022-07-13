<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESP extends Model {
    use HasFactory;

    protected $fillable = ['first_name','last_name','email','date','esp_placeholder'];

    protected $table = 'esps';

    public function accounts() {
    	return $this->hasMany(ESPAccount::class, 'esp_id','id');
    }
}
