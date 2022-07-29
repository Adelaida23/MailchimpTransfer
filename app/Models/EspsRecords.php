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

    /*
public function scopeGetDataForEachApi($query, $key)
{
    return $query->where($key, '<>', null);
}
*/
    public function scopeSearchEmail($query, $email_search)
    {
        return $query->where('email', $email_search);
    }

    /*
public static function searchActiveTrail($email_search)
{
    $key = 'email';
    // $key2 = 'at_id';
    $data = EspsRecords::where($key, $email_search)->first();
    if (!empty($data->id)) {
        return $data;
    }
    return null;
}

*/
}
