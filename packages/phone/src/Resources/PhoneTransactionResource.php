<?php

namespace Phone\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhoneTransactionResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'comment' => $this->resource->comment,
            'allocation_id' => $this->resource->allocation_id,
            'allocatedTo' => $this->whenLoaded('allocatedTo'),
        ];
    }
}
