<?php

namespace Tests\Unit\Services\Travel;

use App\Exceptions\TravelException;
use App\Models\Travel\OrderStatus;
use App\Models\Travel\TravelOrder;
use App\Repositories\Travel\ITravelOrderRepository;
use App\Services\Travel\TravelOrderService;
use Mockery;
use PHPUnit\Framework\TestCase;
use App\ValueObject\Travel\OrderStatusVO;

class TravelOrderServiceUpdateTest extends TestCase
{
    private $travelOrderRepositoryMock;
    private $travelOrderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelOrderRepositoryMock = Mockery::mock(ITravelOrderRepository::class);
        $this->travelOrderService = new TravelOrderService($this->travelOrderRepositoryMock);
    }

    public function testUpdateStatusThrowsExceptionWhenStatusIsSame()
    {
        $orderStatus = Mockery::mock(OrderStatus::class);
        $orderStatus->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn(OrderStatusVO::Requested->name);

        $order = Mockery::mock(TravelOrder::class);
        $order->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn(OrderStatusVO::Requested->value);
        $order->shouldReceive('getAttribute')
            ->with('travelStatus')
            ->andReturn($orderStatus);

        $this->travelOrderRepositoryMock
            ->shouldReceive('findOrderById')
            ->once()
            ->with(1)
            ->andReturn($order);

        $this->expectException(TravelException::class);
        $this->expectExceptionMessage('The status has already been requested');

        $this->travelOrderService->updateStatus(1, OrderStatusVO::Requested);
    }

    public function testUpdateStatusReturnsUpdatedOrder()
    {
        $orderStatus = Mockery::mock(OrderStatus::class);
        $orderStatus->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn(OrderStatusVO::Requested->name);

        $order = Mockery::mock(TravelOrder::class);
        $order->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn(OrderStatusVO::Requested->value);
        $order->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $order->shouldReceive('getAttribute')
            ->with('travelStatus')
            ->andReturn($orderStatus);

        $this->travelOrderRepositoryMock
            ->shouldReceive('findOrderById')
            ->once()
            ->with(1)
            ->andReturn($order);

        $orderStatusResult = Mockery::mock(OrderStatus::class);
        $orderStatusResult->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn(OrderStatusVO::Canceled->name);

        $orderResult = Mockery::mock(TravelOrder::class);
        $orderResult->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn(OrderStatusVO::Canceled->value);
        $orderResult->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $orderResult->shouldReceive('getAttribute')
            ->with('travelStatus')
            ->andReturn($orderStatusResult);

        $this->travelOrderRepositoryMock
            ->shouldReceive('updateOrder')
            ->once()
            ->with(1, ['status' => OrderStatusVO::Canceled->value])
            ->andReturn($orderResult);

        $orderUpdated = $this->travelOrderService->updateStatus(1, OrderStatusVO::Canceled);

        $this->assertInstanceOf(TravelOrder::class, $orderUpdated);
        $this->assertEquals($orderResult->status, $orderUpdated->status);
    }

    /**
     * Data provider para testar o método updateStatus com diferentes combinações de status
     *
     * @return array
     */
    public function statusTransitionDataProvider()
    {
        return [
            "already_same" => [OrderStatusVO::Requested, OrderStatusVO::Requested, 'The status has already been requested', 400],
            "change_to_requested" => [OrderStatusVO::Approved, OrderStatusVO::Requested, 'Unable to change status from approved to requested', 400],
            "change_from_canceled_to_approved" => [OrderStatusVO::Canceled, OrderStatusVO::Approved, 'Unable to change status from canceled to approved', 400],
            "change_from_requested_to_approved" => [OrderStatusVO::Requested, OrderStatusVO::Approved, null],
        ];
    }

    /**
     * @dataProvider statusTransitionDataProvider
     */

    public function testUpdateStatusThrowsExceptionWhenStatusIsUnchanged(OrderStatusVO $initialStatus, OrderStatusVO $newStatus, string $expectedExceptionMessage = null, $codeHttp = null)
    {
        $orderStatus = Mockery::mock(OrderStatus::class);
        $orderStatus->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn($initialStatus->name);

        $order = Mockery::mock(TravelOrder::class);
        $order->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn($initialStatus->value);
        $order->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $order->shouldReceive('getAttribute')
            ->with('travelStatus')
            ->andReturn($orderStatus);

        $this->travelOrderRepositoryMock
            ->shouldReceive('findOrderById')
            ->once()
            ->with(1)
            ->andReturn($order);

        $orderStatusResult = Mockery::mock(OrderStatus::class);
        $orderStatusResult->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn($newStatus->name);

        $orderResult = Mockery::mock(TravelOrder::class);
        $orderResult->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn($newStatus->value);
        $orderResult->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $orderResult->shouldReceive('getAttribute')
            ->with('travelStatus')
            ->andReturn($orderStatusResult);

        $this->travelOrderRepositoryMock
            ->shouldReceive('updateOrder')
            ->times($expectedExceptionMessage ? 0 : 1)
            ->with(1, ['status' => $newStatus->value])
            ->andReturn($orderResult);

        if ($expectedExceptionMessage) {
            $this->expectException(TravelException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
            $this->expectExceptionCode($codeHttp);
        }

        $result = $this->travelOrderService->updateStatus(1, $newStatus);
        $this->assertEquals($orderResult, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
