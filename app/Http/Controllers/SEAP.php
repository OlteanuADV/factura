<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;

class SEAP extends Controller
{
    public function search() {
        // in functie de ce esti abonat
        $subscriptions = Settings::mySeapSubscriptions();

        $ads = Settings::fetchAvailableSEAP($subscriptions);
        
        foreach($ads as $add) {
            $add = $add[0];
            $urlItem = 'https://www.e-licitatie.ro/api-pub/PUBLICAdvNotice/getForView/' . $add->advNoticeId;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlItem);
            curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $urlItem);
            $response = curl_exec($ch);
            $data = json_decode($response);
            // return $data;
            curl_close($ch);
            $urlDoc = 'https://www.e-licitatie.ro/'.$data->documentUrl;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlDoc);
            curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $urlItem);          
            $response = curl_exec($ch);
            curl_close($ch);             
            return $response;
        }

        $data = Cache::remember('searchInSeap_' . $info, 120, function () use ($info)
        {
            if ($info = 'deratizare')
            {
                $cpvCodeId = 19263;
                $cpvCodeText = "90923000-3 Servicii de deratizare (Rev.2)";
            }
            if ($info = 'dezinsectie')
            {
                $cpvCodeId = 19261;
                $cpvCodeText = "90921000-9 Servicii de dezinfectie si de dezinsectie (Rev.2)";
            }
            
            $pageIndex = 0;
            $pageSize = 1000;
            $publicationDateStart = date("Y-m-d H:m:s", strtotime('-7 days'));
            $url = 'https://www.e-licitatie.ro/api-pub/AdvNoticeCommon/GetAdvNoticeList/';
            $params = ['cpvCodeId' => $cpvCodeId, 'cpvCodeText' => $cpvCodeText, 'pageIndex' => $pageIndex, 'pageSize' => $pageSize, 'publicationDateStart' => $publicationDateStart, ];
            $myvars = 'cpvCodeId=' . $cpvCodeId . '&cpvCodeText="' . $cpvCodeText . '"&pageIndex=' . $pageIndex . '&pageSize=' . $pageSize . '&publicationDateStart=' . $publicationDateStart;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            $response = curl_exec($ch);
            curl_close($ch);

            $anunturi = json_decode($response)->items;

            foreach ($anunturi as $add)
            {
                if (!SEAP::where('AdID', $add->advNoticeId)
                    ->first())
                {
                    //cautare, descarcare, adaugare in db
                    $urlItem = 'https://www.e-licitatie.ro/api-pub/PUBLICAdvNotice/getForView/' . $add->advNoticeId;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $urlItem);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_REFERER, $urlItem);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $data = json_decode($response);

                    $db = new SEAP;
                    $db->AdID = $add->advNoticeId;
                    $db->AuthorityName = $data->contractAuthorityName;
                    $db->Pret = $data->value;
                    $db->contractObject = $data->contractObject;
                    $db->contractDescription = $data->contractDescription;
                    $db->contractRelatedConditions = $data->contractRelatedConditions;
                    $db->documentURL = $data->documentUrl;
                    $db->documentName = $data->documentName;
                    $db->participationConditions = $data->participationConditions;
                    $db->additionalInformation = $data->additionalInformation;
                    $db->Deadline = date('Y-m-d H:i:s', strtotime($data->tenderReceiptDeadline));
                    $db->AuthorityEmail = $data
                        ->noticeEntityAddress->email;
                    $db->Region = $data
                        ->noticeEntityAddress
                        ->county->localeKey;
                    $db->JsonSEAP = json_encode($data);

                    $db->save();

                    /*if($data->documentUrl !== null) {
                        $urlDoc = 'https://www.e-licitatie.ro/'.$data->documentUrl;
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $urlDoc);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_REFERER, $urlDoc);
                        $fp = fopen('./seapdocs/'.$data->documentName, 'w+');
                        curl_setopt($ch, CURLOPT_FILE, $fp);
                        $response = curl_exec($ch);
                        curl_close($ch);
                    }*/
                }
                else
                {
                    return 'already_found';
                }
            }
        });
    }
}
