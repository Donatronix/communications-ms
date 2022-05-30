@component('mail::message')
##Welcome {{env('APP_NAME')}}

Dear {{$display_name}},

You received an alert from warehouse.

There is a negative balance for your product.
Status: {{$status}}<p/>
Operation ID: {{$operationId}}<p/>
Warehouse ID: {{$warehouse_id}}<p/>
Warehouse name: {{$warehouse_name}}<p/>
Warehouse owner: {{$warehouse_owner}}<p/>
Warehouse owner ID: {{$warehouse_owner_id}}<p/>
Warehouse balance: {{$warehouse_balance}}<p/>
Product ID: {{$productId}}<p/>
Option product ID: {{$optionProductId}}<p/>
User ID: {{$userId}}<p/>

@endcomponent
