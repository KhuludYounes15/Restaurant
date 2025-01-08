<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Category;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class CategoryController extends Controller
{ use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $categories=Category::all();
            if(!$categories){
                return  $this->notFoundResponse('Not found categories');
            }
            $data= CategoryResource::collection($categories);
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
            'name' => 'required|string|unique:categories,name',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Create a new restaurant
            $category = Category::create([
                'uuid' => Str::uuid(),
                'name' => $request->name,
            ]);
            
            return $this->apiResponse($category, true, null, 200);
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
            $category=Category::where('uuid',$uuid)->firstOrFail();
            if(!$category){
                return  $this->notFoundResponse('Not found any restaurant');
            }
            $data= CategoryResource::make($category);
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
        $category = Category::where('uuid', $uuid)->first();
        
        if (!$category) {
            return $this->apiResponse(null, false, 'Category not found', 404);
        }
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name,' . $category->id,
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }
    
        try {
            // Prepare data for update
            $data = [
                'name' => $request->name,
            ];
    
            // Update the category
            $category->update($data);
            
            // Retrieve and return the updated category
            return $this->apiResponse($category, true, null, 200);
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
    
        $category = Category::where('uuid', $uuid)->first();
    
        if (!$category) {
            return $this->apiResponse(null, false, 'Category not found', 404);
        }
    
        $category->delete(); // Use the model instance to delete
    
        return $this->apiResponse(null, true, 'You have successfully deleted the category', 200); 
    
    }

    public function restore($uuid)
    { 
        $user = auth('sanctum')->user();
        
        // Check if the user is not authenticated or not an admin
        if (!$user || $user->role !== 'admin') {
            return $this->unAuthorizeResponse();
        }
    
        $category = Category::withTrashed()->where('uuid', $uuid)->first();
    
        if (!$category) {
            return $this->apiResponse(null, false, 'Category not found', 404);
        }
    
        $category->restore(); // Restore the restaurant
    
        return $this->apiResponse($category, true, 'Category restored successfully', 200); 
    }

    public function search(Request $request)
    { 
        try{
        $search = $request->search;
        $category =Category::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
        })->get();
        if(!$category){
            return  $this->notFoundResponse('Not found any category');
        }
        $data= CategoryResource::collection($category);
        return $this->apiResponse($data,true,null,200);
        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);

    } 
    }
}
