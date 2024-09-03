<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ContactCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // mengambil setiap item dalam koleksi menjadi resource yang diformat dalam ContactResource
        return [
            'data' => ContactResource::collection($this->collection)
        ];
    }
}
