<?php

namespace Phone\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CallerPhoneNumberResource extends JsonResource
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
            'allocatedTo' => $this->whenLoaded('allocatedTo'),
            'suggested_allocation' => $this->resource->suggested_allocation ?? null,
        ];
    }
}
