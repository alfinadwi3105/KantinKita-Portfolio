<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode',
        'menu_id',
        'qty',
        'total',
        'snap_token',
        'status',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}