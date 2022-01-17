<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ANAF extends Controller
{
    private $url_firma = 'https://webservicesp.anaf.ro/PlatitorTvaRest/api/v6/ws/tva';
    private $url_bilant = 'https://webservicesp.anaf.ro/bilant';
    
    /*
    Link pentru documentatie extragere informatii despre bilant pe an 
        https://static.anaf.ro/static/10/Anaf/Informatii_R/doc_WS_Bilant_V1.txt
    Link pentru documentatie extragere informatii despre firma folosind cui-ul
        https://static.anaf.ro/static/10/Anaf/Informatii_R/doc_WS_V6.txt
    */
    public function search($cui) {
        $firma = [];

        //extragere informatii despre firma
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url_firma,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                [
                    "cui" => $cui,
                    "data" => date('Y-m-d')
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ]
        ]);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        if($response)
            $firma['date'] = $response->found;

        return json_encode($firma);
    }

    public function search_advanced($cui) {
        $firma = [];

        //extragere informatii despre firma
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url_firma,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                [
                    "cui" => $cui,
                    "data" => date('Y-m-d')
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ]
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $firma['date'] = json_decode($response);

        //extragere informatii despre bilantul firmei
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url_bilant.'?an=2020&cui='.$cui,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $firma['bilant'] = json_decode($response);
        
        return json_encode($firma); 
    }
}
