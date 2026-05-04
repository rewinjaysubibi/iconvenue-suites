<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'phone', 'email', 'facebook', 'messenger', 
        'whatsapp', 'address', 'business_hours', 'google_form_url'
    ];
}
