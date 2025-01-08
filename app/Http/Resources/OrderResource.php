<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    { 
        return [
            'uuid'=>$this->uuid,
            'username'=>$this->user->name,
            'restaurant'=>$this->restaurant->name,
            'menu'=>OrderMenuResource::collection($this->orderMenus),
            'total_price'=>$this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
           'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
