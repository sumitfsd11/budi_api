<?php

namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //$device_id = \App\Models\Device::where('user_id', $this->receiver->id)->first();
$timeto =(!$this->created_at->gte(Carbon::now()->subDay()))? $this->created_at->diffForHumans() : $this->created_at->format('h:i A');
        return [
            'id' => $this->id,
            'sender' => BareUserResource::make($this->sender),
            'receiver' => BareUserResource::make($this->receiver),
           // 'device_id'=> $device_id->device_id,
            'message' => $this->message,
            'created_at' =>$this->created_at->diffForHumans(),
        ];
    }
}
