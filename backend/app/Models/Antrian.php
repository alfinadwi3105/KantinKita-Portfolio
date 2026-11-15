<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $table = 'antrian';
    protected $fillable = [
        'kode', 'menu_id', 'menu', 'qty', 'total', 'status',
        'dipesan_pada',
    ];
}