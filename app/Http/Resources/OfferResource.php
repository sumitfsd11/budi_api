<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'id' => $this->id,
            'status'=> $this->active? 'active':'inactive',
            'category' => new CategoryResource($this->category),
            'title' => $this->title,
            'body' => $this->body,
            'price' => number_format($this->price, 2, '.', ''),
            'created_at' => $this->created_at->diffForHumans(),
            'created_by' => new BareUserResource($this->createdBy),
            'ratings' => [
                'average_rating' => number_format($this->createdBy->average_rating, 1, '.', '')
            ],
            'thumbnail' => $this->thumbnail,
            'offer_images' => $this->offerImages->pluck('image'),
        ];
    }
}
