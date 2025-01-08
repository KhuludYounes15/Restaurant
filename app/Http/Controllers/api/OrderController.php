<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\Order;
use  App\Models\OrderMenu;
use  App\Models\Menu;
use Illuminate\Support\Str;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\OrderMenuResource;
use App\Http\Resources\OrderResource;
use Carbon\Carbon;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Mail;
class OrderController extends Controller
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
public function addOrder(Request $request)
{
    $user = auth('sanctum')->user();
    if (!$user) {
        return $this->unAuthorizeResponse();
    }
    $message = [
        'order_menus.*.menu_id.exists' => 'Menu not found in this restaurant',
    ];
    try {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'order_menus' => 'required|array',
            'order_menus.*.menu_id' => 'required|integer|exists:menus,id,restaurant_id,'. $request->restaurant_id,
            'order_menus.*.count' => 'required|integer|min:1',
        ],$message);

        if ($validator->fails()) {
            return $this->requiredField($validator->errors()->first());
        }

        $order = Order::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'restaurant_id' => $request->restaurant_id,
        ]);

        foreach ($request->order_menus as $order_menu) {
            $menu = Menu::where('id', $order_menu['menu_id'])
                ->where('restaurant_id', $request->restaurant_id)
                ->firstOrFail();
            if ($menu) {
                OrderMenu::create([
                    'uuid' => Str::uuid(),
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'count' => $order_menu['count'],
                ]);
            }
        }
        $data = new OrderResource($order);
        $user->notify(new NewOrderNotification($order));
        return $this->apiResponse($data, true,null, 200);
    } catch (\Exception $e) {
        return $this->apiResponse(null, false, $e->getMessage(), 500);
    }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ShowallOrderbyUser()
    {
    try {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unAuthorizeResponse();
        }

        $orders = Order::where('user_id', $user->id)->where('status', 'received')->get();
        if ($orders->isEmpty()) {
            return $this->notFoundResponse('No orders found for the user');
        }
        $data = OrderResource::collection($orders);
        return $this->apiResponse($data, true, null, 200);
    } catch (\Exception $e) {
        return $this->apiResponse(null, false, $e->getMessage(), 500);
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
    public function update(Request $request,$uuid)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unAuthorizeResponse();
        }
        try {
            $message = [
                'order_menus.*.menu_id.exists' => 'Menu not found in this restaurant',
            ];
            $validator = Validator::make($request->all(), [
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'order_menus' => 'required|array',
                'order_menus.*.menu_id' => 'required|integer|exists:menus,id,restaurant_id,' . $request->restaurant_id,
                'order_menus.*.count' => 'required|integer|min:1',
            ],$message);
            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }
    
            $order = Order::where('uuid', $uuid)->firstOrFail();
            if (!$order) {
                return $this->apiResponse(null, false, 'Order not found', 404);
            }
    
            $order->update([
                'restaurant_id' => $request->restaurant_id,
            ]);
             $order->orderMenus()->delete();
                foreach($request->order_menus as $order_menu)
                    {$menu=Menu::where('id',$order_menu['menu_id'])
                        ->where('restaurant_id',$request->restaurant_id)
                        ->firstorFail();
                      if (!$menu) {
                         return $this->apiResponse(null, false, 'Menu is not available in this restaurant', 404);
                        }
                        OrderMenu::create([
                                'uuid' => Str::uuid(),
                                'menu_id' => $menu->id,
                                'order_id' =>$order->id,
                                'count' => $order_menu['count'],
                            ]);
                        }
            
            $data = new OrderResource($order);
            return $this->apiResponse($data, true,null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }
    public function deliverOrder($uuid)
    {
        try{
        $user=auth('sanctum')->user();
        if (!($user && $user->role=='admin'))
        {
            return $this->unAuthorizeResponse();
        }
        $order=Order::where('uuid',$uuid)
       ->firstorFail();
        if(!$order)
        {
            return $this->notFoundResponse('Order not found for the user.');
        }
        $order->update(['status'=>'received']);
        $data = new OrderResource($order);
        return $this->apiResponse($data, true,null, 200);
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }

    }
        


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelOrder($uuid)
    {  
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return $this->unAuthorizeResponse();
            }
            
            $currentDateTime = Carbon::now();
            $cutoffTime = $currentDateTime->copy()->subHour();
          
            $order = Order::where('uuid',$uuid)
               ->where('user_id', $user->id)
               ->where('status', 'not_received')
               ->where('created_at', '<=', $cutoffTime) 
                ->first();
            if(!$order)
            {
                return $this->apiResponse(null, false, 'Order cannot be canceled anymore.', 400);
            }
            $order->orderMenus()->delete();
            $order->delete();
            
            return $this->apiResponse(null, true, 'Order has been cancelled', 200);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }
}