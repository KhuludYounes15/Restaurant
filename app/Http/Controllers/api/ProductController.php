<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Product;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class ProductController extends Controller
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Create a new restaurant
            $product = Product::create([
                    'uuid' => Str::uuid(),
                    'name' => $request->name,
                    'description' => $request->description,
                    'category_id' => $request->category_id,
                ]);
            
            return $this->apiResponse(new ProductResource($product), true, null, 200);
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
            $prouct=Product::where('uuid',$uuid)->firstOrFail();
            if(!$product){
                return  $this->notFoundResponse('Not found any product');
            }
            $data=ProductResource::make($product);
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
    
        // Retrieve the product to be updated
        $product = Product::where('uuid', $uuid)->first();
        
        if (!$product) {
            return $this->apiResponse(null, false, 'Product not found', 404);
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Prepare data for update
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
            ];
    
            // Update the product
            $product->update($data);
            // Retrieve and return the updated product
            return $this->apiResponse(new ProductResource($product), true, null, 200);
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
    
        $product = Product::where('uuid',$uuid)->first();
    
        if (!$product) {
            return $this->apiResponse(null, false, 'Product not found', 404);
        }
    
        $product->delete(); // Use the model instance to delete
    
        return $this->apiResponse(null, true, 'You have successfully deleted the product', 200); 
    }

    public function restore($uuid)
    { 
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        $product = Product::withTrashed()->where('uuid', $uuid)->first();
    
        if (!$product) {
            return $this->apiResponse(null, false, 'Product not found', 404);
        }
    
        $product->restore(); // Restore the product
    
        return $this->apiResponse($product, true, 'Product restored successfully', 200); 
    }
    }

