<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'users_addresses';

    protected $fillable = [
        'recipient_name',
        'phone',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
