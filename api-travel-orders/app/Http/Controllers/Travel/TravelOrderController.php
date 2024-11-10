<?php

namespace App\Http\Controllers\Travel;

use App\Exceptions\TravelException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Travel\CreateTravelOrderRequest;
use App\Http\Requests\Travel\UpdateTravelOrderStatusRequest;
use App\Services\Travel\IOrderStatusService;
use App\Services\Travel\ITravelOrderService;
use App\ValueObject\Travel\TravelOrderCreateVO;
use App\ValueObject\Travel\OrderStatusVO;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\DB;

class TravelOrderController extends Controller
{
    private ITravelOrderService $travelOrderServicel;
    private IOrderStatusService $orderStatusService;
    public function __construct(ITravelOrderService $travelOrderServicel, IOrderStatusService $orderStatusService)
    {
        $this->travelOrderServicel = $travelOrderServicel;
        $this->orderStatusService = $orderStatusService;
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->travelOrderServicel->getAll([
                "status" => $request->get("status")
            ]);

            return ApiResponse::response([
                "orders" => $orders->toArray()
            ]);
        } catch (TravelException $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            return ApiResponse::error();
        }
    }
    public function show($id)
    {
        try {
            $order = $this->travelOrderServicel->findById(intval($id));
            return ApiResponse::response([
                "order" => $order
            ]);
        } catch (TravelException $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            return ApiResponse::error();
        }
    }
    public function create(CreateTravelOrderRequest $request)
    {
        try {
            $travelOrderCreateVO = new TravelOrderCreateVO(
                $request->travelerName,
                $request->destination,
                new DateTimeImmutable($request->departureDate),
                new DateTimeImmutable($request->returnDate),
                OrderStatusVO::from($request->status)
            );
            DB::beginTransaction();
            $order = $this->travelOrderServicel->create($travelOrderCreateVO);
            DB::commit();
            return ApiResponse::response([], "", 201)
            ->header('Location', route('travel.orders', ['id' => $order->id]));
        } catch (TravelException $th) {
            DB::rollback();
            return ApiResponse::error($th->getMessage(), $th->getData(), $th->getCode());
        } catch (Throwable $th) {
            DB::rollback();
            return ApiResponse::error();
        }
    }

    public function updateStatus($id, UpdateTravelOrderStatusRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = $this->travelOrderServicel->updateStatus(intval($id), OrderStatusVO::from($request->status));
            DB::commit();
            return ApiResponse::response([
                "order" => $order
            ]);
        } catch (TravelException $th) {
            DB::rollback();
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            DB::rollback();
            return ApiResponse::error();
        }
    }

    function showTravelStatusOrder()
    {
        try {
            $orderStatus = $this->orderStatusService->getAll();
            return ApiResponse::response([
                "orderStatus" => $orderStatus
            ]);
        } catch (TravelException $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            return ApiResponse::error();
        }
    }
}
