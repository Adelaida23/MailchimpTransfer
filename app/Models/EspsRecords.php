<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspsRecords extends Model
{
    use HasFactory;

    protected $guarded = [];



    public function scopeGetActiveTrail($query, $key)
    {
        return $query->where($key, '<>', null);
    }

    public function scopeSearchEmail($query, $email_search)
    {
        return $query->where('email', $email_search);
    }


    public static function getApiOrigin($origin_id)
    {
        $account =  ESPAccount::find($origin_id);
        if (!empty($account->esp_id)) {
            if ($account->esp_id == 1)
                return 'mc_id';
            if ($account->esp_id == 2)
                return 'at_id';
            if ($account->esp_id == 3)
                return 'keap_id';
            else return 0;
        }

        return 0;
    }

    public static function getApiReceive($receive_id)
    {
        $account =  ESPAccount::find($receive_id);
        if (!empty($account->esp_id)) {
            if ($account->esp_id == 1)
                return 'mc_id';
            if ($account->esp_id == 2)
                return 'at_id';
            if ($account->esp_id == 3)
                return 'keap_id';
            else return 0;
        }

        return 0;
    }
}
