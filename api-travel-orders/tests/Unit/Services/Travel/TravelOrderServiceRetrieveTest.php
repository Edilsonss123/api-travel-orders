<?php

namespace Tests\Unit\Services\Travel;

use App\Exceptions\TravelException;
use App\Models\Travel\TravelOrder;
use App\Repositories\Travel\ITravelOrderRepository;
use App\Services\Travel\TravelOrderService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class TravelOrderServiceRetrieveTest extends TestCase
{
    private $travelOrderRepositoryMock;
    private $travelOrderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelOrderRepositoryMock = Mockery::mock(ITravelOrderRepository::class);
        $this->travelOrderService = new TravelOrderService($this->travelOrderRepositoryMock);
    }
    public function testGetAllReturnsOrders()
    {
        $orders = new Collection([Mockery::mock(TravelOrder::class)->makePartial(), Mockery::mock(TravelOrder::class)->makePartial()]);
        $this->travelOrderRepositoryMock
            ->shouldReceive('getOrders')
            ->once()
            ->with([])
            ->andReturn($orders);

        $result = $this->travelOrderService->getAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }
    public function testFindByIdReturnsOrder()
    {
        $order = Mockery::mock(TravelOrder::class)->makePartial();
        $this->travelOrderRepositoryMock
            ->shouldReceive('findOrderById')
            ->once()
            ->with(1)
            ->andReturn($order);

        $result = $this->travelOrderService->findById(1);

        $this->assertInstanceOf(TravelOrder::class, $result);
    }

    public function testFindByIdThrowsTravelExceptionWhenOrderNotFound()
    {
        $this->travelOrderRepositoryMock
            ->shouldReceive('findOrderById')
            ->once()
            ->with(999)
            ->andReturnNull();

        $this->expectException(TravelException::class);
        $this->expectExceptionMessage('Entity not found');

        $this->travelOrderService->findById(999);
    }
}
