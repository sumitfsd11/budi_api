<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use Carbon\Carbon;
use App\Http\Controllers\OfferController;
use App\Http\Resources\OfferResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\newMessage;
use App\Http\Resources\newOffer;

class NotificationController extends Controller
{
public function store(Request $request){
    $request->validate([
        'title' => 'required',
        'description' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $notification= new \App\Models\Notification();
    $notification->title=$request->title;
    $notification->description=$request->description;
    if ($image = $request->file('image')) {
        
       $notification->noticeimage= $image->store('notification_images', 'public');
    }

    $notification->save();
    $notification=notificationResource::make($notification);

    return response()->json(['message'=>'notification created!',
    'notification' => $notification,
]);
    
    
}
public function index(Request $request){

    // $notifications = \App\Models\Notification::all();

    // $notifications=NotificationResource::collection($notifications);
 
    $messages = \App\Models\Message::where('seen',0)
    ->where('receiver_id',$request->user()->id)
    ->orderBy('created_at', 'desc')->get();
    $message=newMessage::collection($messages);
$Collection=$message;
    if($request->user()->hasRole('user')){
        $userDetail= \App\Models\UserDetail::find($request->user()->id);

        $fiveHoursAgo = $userDetail->updated_at->subDay();

        $offers= \App\Models\Offer::where('created_at','>=', $fiveHoursAgo)
        ->where('active',1)
        ->orderBy('updated_at', 'desc')->paginate();
        
          $userDetail->updated_at=  Carbon::now();
          $userDetail->save();
  
        $offer=newOffer::collection($offers);
        $Collection = $message->concat($offer);
    }


$newCollection = $Collection->sortByDesc('created_at');
    return response()->json([
        'message' => 'Successfully fetched notifications!',
        'notifications' =>$newCollection,
        
    ], 200);

}

public function count(Request $request){

    $newMessageCount = \App\Models\Message::where('seen',0)
    ->where('receiver_id',$request->user()->id)->count();

    $count=$newMessageCount;

    if($request->user()->hasRole('user')){
        $userDetail= \App\Models\UserDetail::find($request->user()->id);

        $newOfferCount = \App\Models\Offer::whereRaw('created_at > ?', [$userDetail->updated_at])->where('active',1)->count();
        $count= $count + $newOfferCount;
    }
    //  $userDetail->updated_at=  Carbon::now();
    //  $userDetail->save();

   return   response()->json(['message'=>'count of new notification fetched!',
  
   'count'=>$count,
]);    
}

public function show(Request $request, $id)
{
    $notification = \App\Models\Notification::findOrFail($id);

    return response()->json([
        'message' => 'Successfully fetched notification',
        'notification' => NotificationResource::make($notification),
    ], 200);
}


}
