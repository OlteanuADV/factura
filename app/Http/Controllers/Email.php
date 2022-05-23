<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use PhpImap\Mailbox as Mailbox;
use App\Models\Olteanu;
use App\Models\UserEmail;   

class Email extends Controller
{
    public function index() 
    {
        $mailbox = Olteanu::readEmails(UserEmail::first());
        //reverse the array
        $mailbox = array_reverse($mailbox);
        foreach($mailbox as $email)
        {
            $email->title = imap_mime_header_decode($email->subject)[0]->text;
        }
        return $mailbox;
    }
}
