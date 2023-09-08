<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Resources\DiscountResource;
class discountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discounts = \App\Models\Discount::all();

        DiscountResource::collection($discounts);

        return response()->json([
            'message' => 'Successfully fetched discounts!',
            'discounts' => $discounts,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */



    public function store(Request $request)
    {
       

        //  if (!$request->user()->hasRole('agent')) {
           
        //      return response()->json([
        //          'message' => 'Invalid role',
        //      ], 401);
        //  };

        $request->validate([
            'headline' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
   
        $discount= new \App\Models\Discount();
        $discount->headline=$request->headline;
        $discount->description=$request->description;
        if ($image = $request->file('image')) {
            
           $discount->image= $image->store('discount_images', 'public');
        }
    
        $discount->save();
        $discount=DiscountResource::make($discount);

        return response()->json(['message'=>'Discount created!',
        'discount' => $discount,
    ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
