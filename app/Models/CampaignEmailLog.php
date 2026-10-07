<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CampaignEmailLog extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;



    protected $fillable = [
        'campaign_id',
        'email',
        'sent_at',
        'status',
        'created_by',
        'updated_by',
    ];

    public function emailCampaign()
    {
        return $this->belongsTo(EmailCampain::class, 'campaign_id', 'id');
    }


}
