<?php

namespace Phone\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhoneTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'comment' => $this->resource->comment,
        ];
    }
}
