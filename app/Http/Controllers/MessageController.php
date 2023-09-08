<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
        ]);

        $messages = \App\Models\Message::between($request->user()->id, $request->receiver_id)
            ->orderByDesc('created_at')
            ->with('sender', 'receiver')
            ->get();

        \App\Models\Message::between($request->user()->id, $request->receiver_id)->where('receiver_id',$request->user()->id)->update(['seen'=>1]);

        return response()->json([
            'message' => 'Successfully fetched messages',
            'messages' => MessageResource::collection($messages),
        ], 200);
    }

    public function AgentReply(Request $request){
        
        if ($request->user()->hasRole('agent')) {
            return response()->json([
                'message' => 'Invalid authenticated user role',
            ], 401);
        }
        $messages = \App\Models\Message::where('seen',0)
        ->where('receiver_id',$request->user()->id)
        ->get();

        return response()->json([
            'message' => 'Successfully fetched new messages',
            'messages' => MessageResource::collection($messages),
        ], 200);
    }
    public function index_users(Request $request)
    {
        $messages = \App\Models\Message::where('receiver_id', $request->user()->id)
            ->orWhere('sender_id', $request->user()->id)
            ->orderByDesc('created_at')->get()
            ->unique(function ($item) use ($request) {
                return $item->sender_id == $request->user()->id ? $item->receiver_id : $item->sender_id;
            });
        MessageResource::collection($messages);

        return response()->json([
            'message' => 'Successfully fetched messages',
            'messages' => MessageResource::collection($messages),
        ], 200);
    }



    public function pushNotification($token,$message){

        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = $token;
            
        $serverKey = 'AAAAglp4260:APA91bGaLz0oQ2Nj-92lVY5Wm6dwA-Nc3erjyeeDrlSsCvZGVuKHnSkZo_blc4CkdOIq106QbEMDGB_MXTNiDe08xy7ouDQNvMMOIvdoWDzwXXqr8tMlbFtlqg13DyzYEJVjfSMXn5cU'; // ADD SERVER KEY HERE PROVIDED BY FCM
    
        $currentDateTime = Carbon::now();

$humanTime = $currentDateTime->diffForHumans();

        $data = [
            "to" => $FcmToken,
            "notification" => [
                "title" => "Budi App",
                "body" => $message,  
             
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key='. $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'message' => 'required|string',
        ]);
        
        $message = \App\Models\Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]); 

     $device_id = \App\Models\Device::where('user_id', $request->receiver_id)->first();

     if( $device_id){
        $token = $device_id->device_id;

        
        $checkPush=\App\Models\UserDetail::where('user_id',$request->receiver_id)->first();
        $pushEnable=$checkPush->push_notifications;
   
        if($pushEnable){
           $this->pushNotification($token,$request->message);
        }
   
     }
     

        return response()->json([
             'message' => 'Successfully sent message',
             'message' => MessageResource::make($message),
         ], 200);
    }

}
