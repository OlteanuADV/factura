<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\SeapCpv;
use App\Models\SeapWord;
use App\Models\SeapAnnounce;
use Cache;
use Carbon\Carbon;
use Auth;

class SEAP extends Controller
{
    public function getCpvs() {
        $cpvs = Cache::rememberForever('seap_cpvs', function() {
            return SeapCpv::whereNull('company_id')->get();
        });

        return $cpvs;
    }

    public function getMyCpvs() {
        $my_cpvs = Cache::remember('seap_cpvs_'.Auth::user()->last_company, now()->addMinutes(5), function() {
            return SeapCpv::where('company_id', Auth::user()->last_company)->get();
        });
        return $my_cpvs;
    }

    public function addCpv(Request $request) {
        $cpv = SeapCpv::where('id', $request->code)->first();

        if(!is_null($cpv)) {
            $cpv_in_db = SeapCpv::where('company_id', Auth::user()->last_company)->where('code', $cpv->code)->first();

            if(is_null($cpv_in_db)) {
                $new_cpv = new SeapCpv;
                $new_cpv->code = $cpv->code;
                $new_cpv->text = $cpv->text;
                $new_cpv->company_id = Auth::user()->last_company;
                $new_cpv->save();
                Cache::forget('seap_cpvs_'.Auth::user()->last_company);
                return ['success' => 1, 'message' => 'Ati adaugat CPV-ul cu succes!'];
            }
            return ['errors' => 1, 'message' => 'Aveti deja acest CPV in lista dumneavoastra.!'];
        }

        return ['errors' => 1, 'message' => 'Acest CPV nu a fost gasit! Va rugam reincercati!'];
    }

    public function addWord(Request $request) {
        $word = $request->word;
        $word_in_db = SeapWord::where('company_id', Auth::user()->last_company)->where('text', $word)->first();

        if(is_null($word_in_db)) {
            $new_word = new SeapWord;
            $new_word->text = $word;
            $new_word->company_id = Auth::user()->last_company;
            $new_word->save();
            Cache::forget('seap_words_'.Auth::user()->last_company);
            return ['success' => 1, 'message' => 'Ati adaugat cu succes cuvantul!'];
        }

        return ['errors' => 1, 'message' => 'Aveti deja acest cuvant in lista dumneavoastra!'];
    }

    public function getMyWords() {
        $my_words = Cache::remember('seap_words_'.Auth::user()->last_company, now()->addMinutes(5), function() {
            return SeapWord::where('company_id', Auth::user()->last_company)->get();
        });
        return $my_words;
    }

    public function getMyAnnounces() {
        // Cache::forget('my_announces_'.Auth::user()->last_company);
        // Cache::flush();
        $this->fetchSeapAnnounces();
        $my_announces = Cache::remember('my_announces_'.Auth::user()->last_company, now()->addMinutes(5), function() {
            $words = SeapWord::where('company_id', Auth::user()->last_company)->get();
            $cpvs = SeapCpv::where('company_id', Auth::user()->last_company)->get();
            $announces = [];

            foreach($words as $word)
            {
                $adds = SeapAnnounce::where('cpvCodeText', 'like', '%'.$word->text.'%')
                ->orWhere('contractObject', 'like', '%'.$word->text.'%')
                ->orWhere('contractRelatedConditions', 'like', '%'.$word->text.'%')
                ->orWhere('participationConditions', 'like', '%'.$word->text.'%')
                ->orWhere('additionalInformation', 'like', '%'.$word->text.'%')
                ->get();
                foreach($adds as $add)
                {
                    array_push($announces, $add);
                }
            }

            foreach($cpvs as $cpv)
            {
                $adds = SeapAnnounce::where('cpvCodeText', $cpv->code)->get();
                foreach($adds as $add)
                {
                    array_push($announces, $add);
                }
            }
            
            $result = array();
            $i = 0;
            foreach ($announces as $key => $value){
                if(!in_array($value, $result))
                {
                    $result[$i]=$value;
                    $i++;
                }
            }
            $announces = $result;

            foreach ($announces as $key => $announce)
            {
                $count[$key] = $announce->Deadline;
            }
            // return $announces;
            array_multisort($count, SORT_DESC, $announces);
            return $announces;
        });
        return $my_announces;
    }

    private function fetchSeapAnnounces() {
        $cache_to_remember_announces_in_database = Cache::remember('seap_fetch_announces_'.Auth::user()->last_company, now()->addMinutes(60), function() {
            $anunturi = [];
            $words = SeapWord::where('company_id', Auth::user()->last_company)->get();
            $pageIndex = 0;
            $pageSize = 1000;
            $publicationDateStart = date("Y-m-d H:m:s", strtotime('-14 days'));
            $url = 'https://www.e-licitatie.ro/api-pub/AdvNoticeCommon/GetAdvNoticeList/';
            
            foreach($words as $word) {
                $params = ['contractObject' => $word->text, 'pageIndex' => $pageIndex, 'pageSize' => $pageSize, 'publicationDateStart' => $publicationDateStart, ];
                $myvars = 'contractObject=' . $word->text . '"&pageIndex=' . $pageIndex . '&pageSize=' . $pageSize . '&publicationDateStart=' . $publicationDateStart;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_REFERER, $url);
                $response = curl_exec($ch);
                curl_close($ch);

                foreach(json_decode($response)->items as $add) {                    
                    //cautare, descarcare, adaugare in db
                    $urlItem = 'https://www.e-licitatie.ro/api-pub/PUBLICAdvNotice/getForView/' . $add->advNoticeId;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $urlItem);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_REFERER, $urlItem);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $data = json_decode($response);

                    $db = new SeapAnnounce;
                    if(!SeapAnnounce::where('advNoticeId', $add->advNoticeId)->first()) {
                        $db->advNoticeId = $add->advNoticeId;
                        $db->AuthorityName = $data->contractAuthorityName;
                        $db->cpvCodeText = $data->cpvCode->text;
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
                    }
                }
            }

            $cpvs = SeapCpv::where('company_id', Auth::user()->last_company)->get();
            foreach($cpvs as $cpv) {
                $params = ['cpvCodeId' => $cpv->code, 'cpvCodeText' => $cpv->text, 'pageIndex' => $pageIndex, 'pageSize' => $pageSize, 'publicationDateStart' => $publicationDateStart, ];
                $myvars = 'cpvCodeId=' . $cpv->code . '&cpvCodeText="' . $cpv->text . '"&pageIndex=' . $pageIndex . '&pageSize=' . $pageSize . '&publicationDateStart=' . $publicationDateStart;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_REFERER, $url);
                $response = curl_exec($ch);
                curl_close($ch);

                foreach(json_decode($response)->items as $add) {                    
                    //cautare, descarcare, adaugare in db
                    $urlItem = 'https://www.e-licitatie.ro/api-pub/PUBLICAdvNotice/getForView/' . $add->advNoticeId;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $urlItem);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_REFERER, $urlItem);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $data = json_decode($response);

                    $db = new SeapAnnounce;
                    if(!SeapAnnounce::where('advNoticeId', $add->advNoticeId)->first()) {
                        $db->advNoticeId = $add->advNoticeId;
                        $db->AuthorityName = $data->contractAuthorityName;
                        $db->cpvCodeText = $data->cpvCode->text;
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
                    }
                }
            }
            return 1;
        });
    }
}
