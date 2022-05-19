<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeapAnnounce extends Model
{
    use HasFactory;

    protected $table = 'seap_announces';

    protected $fillable = [
        'advNoticeId',
        'AuthorityName',
        'Pret',
        'contractObject',
        'contractDescription',
        'contractRelatedConditions',
        'documentUrl',
        'documentName',
        'participationConditions',
        'Region',
        'JsonSEAP',
    ];
}
