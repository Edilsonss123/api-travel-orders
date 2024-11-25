<?php

namespace Tests\Unit\Services\Travel;

use Illuminate\Database\Eloquent\Collection;
use Mockery;
use App\Repositories\Travel\IOrderStatusRepository;
use App\Services\Travel\OrderStatusService;
use PHPUnit\Framework\TestCase;

class OrderStatusServiceTest extends TestCase
{
    private $travelOrderService;
    private $orderStatusRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderStatusRepositoryMock = Mockery::mock(IOrderStatusRepository::class);
        $this->travelOrderService = new OrderStatusService($this->orderStatusRepositoryMock);
    }

    public function testGetAllReturnsOrdersStatus()
    {
        $this->orderStatusRepositoryMock->shouldReceive('getOrdersStatus')
            ->once()
            ->andReturn(new Collection(['status1', 'status2']));


        $result = $this->travelOrderService->getAll();
        
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);  // Verificando se a coleção tem 2 itens
        $this->assertEquals(['status1', 'status2'], $result->toArray());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
