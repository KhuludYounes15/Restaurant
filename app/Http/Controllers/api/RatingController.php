<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Traits\GeneralTrait;
use App\Models\Order;
use App\Models\Rating;
class RatingController extends Controller
{use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $user=auth('sanctum')->user();
        if (!$user) {
            return $this->unAuthorizeResponse();
        }
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required| integer|exists:restaurants,id',
            'comment' => 'required|string',
            'star' => 'required|integer|min:0|max:5',
               ]);
           if ($validator->fails()) {
               return $this->requiredField($validator->errors()->first());
               }
        $order = Order::where('user_id', $user->id)
            ->where('restaurant_id', $request->restaurant_id)
            ->where('status', 'received')
            ->exists();
        if (!$order) {
            return $this->apiResponse(null,false,'You need to place an order and receive it before rating this restaurant', 403);
        }
            try{
             
             $rating = Rating::create([
            'uuid'=> Str::uuid(),
            'user_id' => $user->id,
             'restaurant_id' => $request->restaurant_id,
             'star' =>$request->star,
             'comment' => $request->comment,
            ]);
            return  $this->apiResponse($rating,true,null,200);
            }
            catch(\Exception $e){
                return $this->apiResponse(null,false,$e->getMessage(),500);
            }
        
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
