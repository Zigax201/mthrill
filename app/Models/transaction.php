<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_user',
        'total_price',
        'payment_status',
        'snap_token',
        'timestamp'
    ];

    // protected $hidden = [
    //    'snap_token'
    // ];
}
