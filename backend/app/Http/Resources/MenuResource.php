<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'price'    => (int) $this->price,
            'stock'    => (int) $this->stock,
            'note'     => $this->note,
            'category' => new CategoryResource($this->category),
        ];
    }
}