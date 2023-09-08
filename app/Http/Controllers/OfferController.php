<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfferResource;
use Illuminate\Http\Request;
use App\Models\Offer;
use DB;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image;
class OfferController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'thumbnail' => 'required|image',
            'image' => 'array',
            'image.*' => 'image',
            'category_id' => 'required|integer|exists:categories,id',
            'active'=>'required|bool'
        ]);

        $offer = new Offer();
        $offer->title = $request->title;
        $offer->body = $request->body;
        $offer->price = $request->price;
        $offer->created_by = auth()->id();
        $offer->thumbnail = $request->thumbnail->store('thumbnails', 'public');
        $offer->active = $request->active;
        $offer->category_id = $request->category_id;
        $offer->save();

        if ($request->hasFile('image')) {
            foreach ($request->image as $image) {
                $offer->offerImages()->create([
                    'image' => $image->store('offer_images', 'public'),
                ]);
            }
        }

        return response()->json([
            'message' => 'Offer created successfully',
            'offer' => OfferResource::make($offer),
        ]);
    }

    public function index(Request $request)
    {
        if ($request->user()->hasRole('agent')) {
            $offers = \App\Models\Offer::where('created_by', $request->user()->id)->paginate();
        } else {
            if ($request->agent_id) {
                $request->validate([
                    'agent_id' => 'required|exists:users,id',
                ]);
                $agent = \App\Models\User::find($request->agent_id);
                if (! ($agent->hasRole('agent') ||  $agent->hasRole('admin'))) {
                    return response()->json([
                        'message' => 'Invalid agent',
                    ], 400);
                }
                $offers = \App\Models\Offer::where('created_by', $request->agent_id)->paginate();
            } else {
                $offers = \App\Models\Offer::paginate(10);
            }
        }

        OfferResource::collection($offers);

        return response()->json([
            'message' => 'Successfully fetched offers',
            'offers' => $offers,
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $offer = \App\Models\Offer::findOrFail($id);

        return response()->json([
            'message' => 'Successfully fetched offer',
            'offer' => OfferResource::make($offer),
        ], 200);
    }


    public function findByName(Request $request){
        $request->validate([
            'agent_name' => 'required|string',]);
$offers = DB::table('users')
            ->join('offers', 'users.id', '=', 'offers.created_by')
            ->where('users.name', 'like', $request->agent_name . '%')
            ->where('users.name','!=','admin')
            ->select('offers.*')
            ->get();

      OfferResource::collection($offers);
        return response()->json(['messsage'=>"offer fetched successfully!",'offers'=>$offers]);
    }
    public function update(Request $req, $id){

        $req->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'thumbnail' => 'required',
            'image' => 'array',
            'image.*' => 'nullable',
            'category_id' => 'required|integer|exists:categories,id',
            'active'=>'required|bool'
        ]);


        $offer= \App\Models\Offer::find($id);
        $offer->category_id=$req->category_id;
        $offer->title=$req->title;
        $offer->body=$req->body;
        $offer->price=$req->price;
       if($req->hasFile('thumbnail')){
        storage::delete( 'public/'.$offer->thumbnail);
        $offer->thumbnail = $req->thumbnail->store('thumbnails', 'public');
        
       } elseif ($req->has('thumbnail')) {
        // Handle URL input
        $url = $req->input('thumbnail');
    
        // Download the image from the URL using Intervention Image
        $image = Image::make($url);
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $imagePath = 'thumbnails/' . uniqid() . '.' .  $extension;
        $image->save(public_path('storage/' . $imagePath));
    
        // Delete the old thumbnail, if exists
        Storage::delete('public/' . $offer->thumbnail);
    
        // Save the URL of the new thumbnail in the $offer model
        $offer->thumbnail = $imagePath;
    }


        
        $offer->active = $req->active;
       
        $offer->save();

        if ($req->has('image')) {
            $offerImage= $offer->offerImages->pluck('image');
            foreach ($offerImage as $imagePath) {
               
                Storage::delete('public/' . $imagePath);
            }
            
            //delete the associated records from the database
            $offer->offerImages()->delete();
          
            foreach ($req->image as $image) {

                if (file_exists($image)) {
                    $offer->offerImages()->create([
                        'image' => $image->store('offer_images', 'public'),
                    ]);
                   
                } elseif (filter_var($image, FILTER_VALIDATE_URL)) {
                 
                    $url = $image;
    
                    // Download the image from the URL using Intervention Image
                    $loadimage = Image::make($url);
                    $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                    $imagePath = 'offer_images/' . uniqid() . '.' .  $extension;
                    $loadimage->save(public_path('storage/' . $imagePath));
                
                 //saving image

                 $offer->offerImages()->create([
                    'image' =>  $imagePath,
                ]);
                } 
                

             
            }
            $offer= \App\Models\Offer::find($id);
        }
      
        return response()->json([
            'message' => 'Offer updated successfully!',
            'offer' => OfferResource::make($offer),
        ]);
    }
}
