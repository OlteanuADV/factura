<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserEmail;

class Olteanu extends Model
{
    use HasFactory;

    public static function returnMessage($type, $message, $others = null) {
        switch($type) {
            case 0:
                return ['success' => 1, 'message' => $message, $others];
            break;
            case 1:
                return ['errors' => 1, 'message' => $message, $others];
            break;
        }
    }

    public static function readEmails(UserEmail $email) 
    {
        $mailbox = imap_open("{".$email->host.":".$email->port."/imap/ssl}INBOX", $email->email, $email->password);
        $mc = imap_check($mailbox);
        $result = imap_fetch_overview($mailbox,"1:{$mc->Nmsgs}",0);
        return $result;
    }
}
