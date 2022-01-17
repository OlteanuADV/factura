<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
