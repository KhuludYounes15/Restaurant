<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Restaurant;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\MenuRestaurantResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class RestaurantController extends Controller
{ use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 
    public function index()
    {
        try{
            $restaurants=Restaurant::all();
            if(!$restaurants){
                return  $this->notFoundResponse('Not found restaurants');
            }
            $data= RestaurantResource::collection($restaurants);
            return $this->apiResponse($data,true,null,200);

        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);

        }
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
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:restaurants,name',
            'cuisine_type' => 'required|string|unique:restaurants,cuisine_type',
            'location' => 'required|string',
            'phone' => 'required|min:10|unique:restaurants,phone',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Create a new restaurant
            $restaurant = Restaurant::create([
                'uuid' => Str::uuid(),
                'name' => $request->name,
                'cuisine_type' => $request->cuisine_type,
                'location' => $request->location,
                'phone' => $request->phone,
            ]);
            
            return $this->apiResponse($restaurant, true, null, 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        try{
            $restaurant=Restaurant::where('uuid',$uuid)->firstOrFail();
            if(!$restaurant){
                return  $this->notFoundResponse('Not found any restaurant');
            }
            $data= MenuRestaurantResource::make($restaurant);
            return $this->apiResponse($data,true,null,200);

        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);

        }
    } 
    
    public function search(Request $request)
    { 
        try{
        $search = $request->search;
        $restaurant = Restaurant::where(function ($query) use ( $search) {
            $query->where('cuisine_type', 'like', "%$search%")
                ->orwhere('location', 'like', "%$search%");
        })->get();
        if(!$restaurant){
            return  $this->notFoundResponse('Not found any restaurant');
        }
        $data= RestaurantResource::collection($restaurant);
        return $this->apiResponse($data,true,null,200);
        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);

    } 
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
    public function update(Request $request, $uuid)
    {
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        // Retrieve the restaurant to be updated
        $restaurant = Restaurant::where('uuid', $uuid)->first();
        
        if (!$restaurant) {
            return $this->apiResponse(null, false, 'Restaurant not found', 404);
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:restaurants,name,' . $restaurant->id,
            'cuisine_type' => 'required|string|unique:restaurants,cuisine_type,' . $restaurant->id,
            'location' => 'required|string',
            'phone' => 'required|min:10|unique:restaurants,phone,' . $restaurant->id,
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Prepare data for update
            $data = [
                'name' => $request->name,
                'cuisine_type' => $request->cuisine_type,
                'location' => $request->location,
                'phone' => $request->phone,
            ];
    
            // Update the restaurant
            $restaurant->update($data);
            
            // Retrieve and return the updated restaurant
            return $this->apiResponse($restaurant, true, null, 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    { 
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        $restaurant = Restaurant::where('uuid', $uuid)->first();
    
        if (!$restaurant) {
            return $this->apiResponse(null, false, 'Restaurant not found', 404);
        }
    
        $restaurant->delete(); // Use the model instance to delete
    
        return $this->apiResponse(null, true, 'You have successfully deleted the restaurant', 200); 
    }

    public function restore($uuid)
    { 
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        $restaurant = Restaurant::withTrashed()->where('uuid', $uuid)->first();
    
        if (!$restaurant) {
            return $this->apiResponse(null, false, 'Restaurant not found', 404);
        }
    
        $restaurant->restore(); // Restore the restaurant
    
        return $this->apiResponse($restaurant, true, 'Restaurant restored successfully', 200); 
    }
}
