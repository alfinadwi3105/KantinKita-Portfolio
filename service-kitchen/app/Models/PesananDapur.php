<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananDapur extends Model
{
    protected $table = 'pesanan_dapur';

    protected $fillable = [
        'message_id', 'kode', 'menu_id', 'menu', 'qty', 'status',
        'diterima_pada',
    ];
}
