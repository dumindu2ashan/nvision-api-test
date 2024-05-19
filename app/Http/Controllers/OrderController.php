<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use App\Jobs\ProcessApiRequest;
use App\Jobs\ProcessThirdPartyResponse;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();

        $data = [
            'customer_name' => $request->customer_name,
            'order_value' => $request->order_value,
            'process_id' => rand(1, 10),
            'status' => 1
        ];
        try {
            //queue orders
            ProcessApiRequest::dispatchSync($data);

            $nextId = Order::orderBy('id', 'desc')->first()->id + 1;
            $data['OrderId'] = FormatOrderId($nextId);
            $data['status'] = FormatOrderStatus(1);

            DB::commit();

//          queue and send order details to 3rd party api
            $data['created_at'] = Carbon::now()->toDateTimeString();
            ProcessThirdPartyResponse::dispatchSync($data);

            return ApiResponseClass::sendResponse(new OrderResource($data), 'Order Created Successfully', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
