<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Menu;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\MenuResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class MenuController extends Controller
{ use GeneralTrait;
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
          
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Create a new menu
            $menu = Menu::create([
                'uuid' => Str::uuid(),
                'restaurant_id' => $request->restaurant_id,
                'product_id' => $request->product_id,
                'price' => $request->price,
                ]);
            
            return $this->apiResponse(new MenuResource($menu), true, null, 200);
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
    public function update(Request $request, $uuid)
    {
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        // Retrieve the menu to be updated
        $menu = Menu::where('uuid', $uuid)->first();
        
        if (!$menu) {
            return $this->apiResponse(null, false, 'Menu not found', 404);
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'sometimes|required|exists:restaurants,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'price' => 'sometimes|required|numeric',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Prepare data for update
            $data = [
                'restaurant_id' => $request->restaurant_id,
                'product_id' => $request->product_id,
                'price' => $request->price,
            ];
    
            // Update the product
            $menu->update($data);
            // Retrieve and return the updated product
            return $this->apiResponse(new MenuResource($menu), true, null, 200);
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
    public function destroy($id)
    {
        //
    }
}
