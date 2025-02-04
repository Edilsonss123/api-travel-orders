<?php

namespace Tests\Unit\Services\Travel;

use App\Models\Travel\TravelOrder;
use App\Repositories\Travel\ITravelOrderRepository;
use App\Services\Travel\TravelOrderService;
use App\ValueObject\Travel\OrderStatusVO;
use App\ValueObject\Travel\TravelOrderCreateVO;
use DateTimeImmutable;
use Mockery;
use PHPUnit\Framework\TestCase;
use App\Exceptions\TravelException;

class TravelOrderServiceCreateTest extends TestCase
{
    private $travelOrderRepositoryMock;
    private $travelOrderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelOrderRepositoryMock = Mockery::mock(ITravelOrderRepository::class);
        $this->travelOrderService = new TravelOrderService($this->travelOrderRepositoryMock);
    }

    public function testCreateReturnsOrder()
    {
        $travelOrderCreateVO = new TravelOrderCreateVO(
            "Test Order",
            "test@example.com",
            (new DateTimeImmutable())->modify("+1 days"),
            (new DateTimeImmutable())->modify("+3 days"),
            OrderStatusVO::Requested
        );
        $order = Mockery::mock(TravelOrder::class)->makePartial();

        $this->travelOrderRepositoryMock
        ->shouldReceive('createOrder')
        ->once()
        ->with($travelOrderCreateVO->toArray())
        ->andReturn($order);

        $result = $this->travelOrderService->create($travelOrderCreateVO);

        $this->assertInstanceOf(TravelOrder::class, $result);
    }

    public function testCreateOrderStatusCanceledReturnException()
    {
        $this->expectException(TravelException::class);

        $travelOrderCreateVO = new TravelOrderCreateVO(
            "Test Order",
            "test@example.com",
            (new DateTimeImmutable())->modify("+1 days"),
            (new DateTimeImmutable())->modify("+3 days"),
            OrderStatusVO::Canceled
        );
        $order = Mockery::mock(TravelOrder::class)->makePartial();

        $this->travelOrderRepositoryMock
        ->shouldReceive('createOrder')
        ->once()
        ->with($travelOrderCreateVO->toArray())
        ->andReturn($order);

        $result = $this->travelOrderService->create($travelOrderCreateVO);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
