<?php

namespace App\Models;

use App\Models\Traits\CommonQueryTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmailSetting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use CommonQueryTraits;

    protected $fillable = [
        'user_id',
        'email_engine_type',
        'from_name',
        'from_email',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'is_active',
    ];

    /**
     * Security Tip: Encrypt the password in the database
     * so it isn't stored as plain text.
     */
    protected $casts = [
        'mail_password' => 'encrypted',
        'is_active' => 'integer',
    ];
}
