<?php

namespace Phone\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllocationResource extends JsonResource
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
            'name' => $this->resource->name,
            'charge_to' => $this->resource->charge_to,
            'account_number' => $this->resource->account_number,
        ];
    }
}
