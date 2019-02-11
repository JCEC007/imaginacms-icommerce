<?php

namespace Modules\Icommerce\Http\Controllers\Api;

// Requests & Response
use Modules\Icommerce\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Transformers
use Modules\Icommerce\Transformers\OrderTransformer;

// Entities
use Modules\Icommerce\Entities\Order;

// Repositories
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\OrderHistoryRepository;

// Events
use Modules\Icommerce\Events\OrderWasCreated;
use Modules\Icommerce\Events\OrderWasUpdated;

class OrderApiController extends BaseApiController
{

  private $order;
  private $orderStatusHistory;


  public function __construct(OrderRepository $order,OrderHistoryRepository $orderStatusHistory)
  {
    $this->order = $order;
    $this->orderStatusHistory = $orderStatusHistory;
  }

  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index(Request $request)
  {
    try {
      //Request to Repository
      $orders = $this->order->getItemsBy($this->getParamsRequest($request));

      //Response
      $response = ['data' => OrderTransformer::collection($orders)];
      //If request pagination add meta-page
      $request->page ? $response['meta'] = ['page' => $this->pageTransformer($orders)] : false;

    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }

  /** SHOW
   * @param Request $request
   *  URL GET:
   *  &fields = type string
   *  &include = type string
   */
  public function show($criteria, Request $request)
  {
    try {
      //Request to Repository
      $order = $this->order->getItem($criteria,$this->getParamsRequest($request));

      $response = [
        'data' => $order ? new OrderTransformer($order) : '',
      ];

    } catch (\Exception $e) {
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }

  /**
   * Show the form for creating a new resource.
   * @return Response
   */
  public function create(Request $request)
  {

    try {
      $order = $this->order->create($request->all());

      // Status History
      $this->orderStatusHistory->create([
        'order_id' => $order->id,
        'status' => 0,
        'notify' => 0,
        'comment' => 'first status'
      ]);

      event(new OrderWasCreated($order));

      $response = ['data' => ''];

    } catch (\Exception $e) {

      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }

  /**
   * Update the specified resource in storage.
   * @param  Request $request
   * @return Response
   */
  public function update($criteria, Request $request)
  {
    try {

      $order = $this->order->updateBy($criteria, $request->all(),$this->getParamsRequest($request));

      event(new OrderWasUpdated($order));
      
      $response = ['data' => ''];

    } catch (\Exception $e) {
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }


  /**
   * Remove the specified resource from storage.
   * @return Response
   */
  public function delete($criteria, Request $request)
  {
    try {

      $this->order->deleteBy($criteria,$this->getParamsRequest($request));

      $response = ['data' => ''];

    } catch (\Exception $e) {
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }
}