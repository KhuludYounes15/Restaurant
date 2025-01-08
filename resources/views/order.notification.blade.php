@component('mail::message')
# Order Notification

Dear Customer{{$order->user->name}},

Your order details are as follows:

Order Number: {{ $order['order_id'] }}
Restaurant :{{$order->restaurant->name}}
Order Details {{ $order->orderMenus}}

Thank you for your purchase.

Regards,
{{ config('app.name') }}
@endcomponent