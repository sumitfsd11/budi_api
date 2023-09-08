<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class newMessage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
     $sender=BareUserResource::make($this->sender);

        
        return [
            'id' => $sender->id,
            'name'=>$sender->name,
            'title'=>'New message from '.$sender->name,
            'description' => $this->message,
            'image' => $sender->profile->profile_picture,
            'type'=>"message",
            'created_at' =>$this->created_at->diffForHumans(),
        ];
    }

  
}
