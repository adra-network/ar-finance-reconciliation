<?php

namespace Phone\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhoneNumberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'phone_number' => $this->resource->phone_number,
            'user_id' => $this->resource->user_id,
            'name' => $this->resource->name,
            'auto_allocation' => $this->resource->auto_allocation,
            'remember' => $this->resource->remember,
            'comment' => $this->resource->comment,
            'allocation_id' => $this->resource->allocation_id,
            'allocated_to' => $this->whenLoaded('allocated_to'),
            'suggested_allocation' => $this->resource->suggested_allocation ?? null,
        ];
    }
}
