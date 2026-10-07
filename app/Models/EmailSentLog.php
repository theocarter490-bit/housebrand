<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'source',
        'email',
        'sent_at',
        'status',
        'created_by',
        'updated_by',
        'subject',
        'text',
    ];

    const CAMPAIGN = 'campaign';
    const EVENT = 'event';
    const NOTICE = 'notice';
    const DESIGNER_CONTACT = 'designer_contact';
    const CONTACT_US = 'contact_us';


    public function emailCampaign()
    {
        return $this->belongsTo(EmailCampain::class, 'source_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'source_id', 'id');
    }

    public function notice()
    {
        return $this->belongsTo(NoticeBoard::class, 'source_id', 'id');
    }

    public function designerContact()
    {
        return $this->belongsTo(DesignerContact::class, 'source_id', 'id');
    }
}
