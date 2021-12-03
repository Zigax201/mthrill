<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'desc',
        'price',
        'tinggi',
        'berat',
        'warna',
        'jenis',
        'catalog',
        'timestamp'
    ];

    protected $hidden = [
        'timestamp'
    ];
}
