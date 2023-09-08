<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class newOffer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $creator=new BareUserResource($this->createdBy);
      

        return [
            'id' => $creator->id,
            'name'=>$creator->name,
            'title' => 'New Offer from '.$creator->name,
            'description' => $this->title,
            'image' => $this->thumbnail,
            'type'=>"offer",
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
