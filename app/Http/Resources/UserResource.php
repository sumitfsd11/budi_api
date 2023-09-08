<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
          //  'device_id'=>$this->device(),
            'projects'=>[
                 'total'=>$this->agentProjects->count(),
                 'completed'=>$this->completedProjects->count(),
                 'inprogress'=>$this->agentProjects->count()- $this->completedProjects->count(),
                ],
            'ratings' => [
                'average_ratings' => number_format($this->average_rating, 1, '.', ''),
                'total_ratings' => $this->agentReviews->count(),
                'agent_reviews' => ReviewResource::collection($this->agentReviews),
            ],
            'profile' => new ProfileResource($this->profile),
            'misc' => new MiscResource($this->misc),
            'coordinates' => [
                'latitude' => $this->coordinate->latitude,
                'longitude' => $this->coordinate->longitude,
            ],
            'user_detail' => new UserDetailResource($this->userDetail),
            'interests' => new CategoryCollection($this->categories),
            'agent_status' => new AgentStatusResource($this->agentStatus),
            'balance' => [
                'amount' => number_format($this->balance->amount ?? 0, 2, '.', ''),
            ],
        ];
    }
}
