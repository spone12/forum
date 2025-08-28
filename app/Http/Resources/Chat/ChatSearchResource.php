<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->toArray();
        $count = count($data);
        return [
            'searchResultMessage' => trans_choice('chat.search.results', $count, ['count' => $count]),
            'items' => $data
        ];
    }
}
