<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];
    
    public $timestamps = true;

    public static function mySeapSubscriptions() {
        // get my cpv subscriptions
        $array = [];
        $subscriptions = Settings::where('name','SEAP_NAME')->get();

        foreach($subscriptions as $subs) 
        {
            $name = strtoupper($subs->value);
            $cpv = Settings::where('name', 'SEAP_CPV_'.$name)->first()->value;
            $text = Settings::where('name', 'SEAP_TEXT_'.$name)->first()->value;
            array_push($array, [
                'CPV' => $cpv,
                'TEXT' => $text
            ]);
        }
        
        return $array;
    }
    
    public static function fetchAvailableSEAP($subscriptions) {
        $found = [];
        foreach($subscriptions as $subscription) {
            $subscription = (object) $subscription;
            $pageIndex = 0;
            $pageSize = 1000;
            $publicationDateStart = date("Y-m-d H:m:s", strtotime('-7 days'));
            $url = 'https://www.e-licitatie.ro/api-pub/AdvNoticeCommon/GetAdvNoticeList/';
            $params = ['cpvCodeId' => $subscription->CPV, 'cpvCodeText' => $subscription->TEXT, 'pageIndex' => $pageIndex, 'pageSize' => $pageSize, 'publicationDateStart' => $publicationDateStart, ];
            $myvars = 'cpvCodeId=' . $subscription->CPV . '&cpvCodeText="' . $subscription->TEXT . '"&pageIndex=' . $pageIndex . '&pageSize=' . $pageSize . '&publicationDateStart=' . $publicationDateStart;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            if(!is_null($response->items))
                array_push($found, $response->items);
        }

        return $found;
    }
}
