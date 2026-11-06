<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PesananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kode' => $this->kode,
            'menu' => $this->menu->name,
            'qty' => $this->qty,
            'total' => $this->total,
            'status' => $this->status,
            'snap_token' => $this->snap_token,
        ];
    }
}