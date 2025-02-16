<?php

namespace Tests\Unit\Services\Travel;

use App\Exceptions\TravelException;
use App\Models\Travel\TravelOrder;
use App\Repositories\Travel\ITravelOrderRepository;
use App\Services\Travel\TravelOrderService;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $paginator = Mockery::mock(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);
        $paginator->shouldReceive('items')->andReturn([
            Mockery::mock(TravelOrder::class)->makePartial(),
            Mockery::mock(TravelOrder::class)->makePartial()
        ]);
        $paginator->shouldReceive('currentPage')->andReturn(1);
        $paginator->shouldReceive('lastPage')->andReturn(1);
        $paginator->shouldReceive('count')->andReturn(2);

        $this->travelOrderRepositoryMock
            ->shouldReceive('getOrders')
            ->once()
            ->with([], 100)
            ->andReturn($paginator);

        $result = $this->travelOrderService->getAll([], 100);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->count());
    }

    /**
     * @dataProvider providerPerPageValues
     */
    public function testGetAllAppliesLimitCorrectly($perPage, $expected)
    {
        $this->travelOrderRepositoryMock->shouldReceive('getOrders')
            ->withAnyArgs()
            ->andReturnUsing(fn ($filters, $perPage) => new LengthAwarePaginator([], 0, $perPage));

        $result = $this->travelOrderService->getAll([], $perPage);
        $this->assertEquals($expected, $result->perPage());
    }

    public static function providerPerPageValues()
    {
        $limite = TravelOrderService::LIMITE_POR_PAGINA;

        return [
            'perPage = 0 deve ser alterado para LIMITE_POR_PAGINA' => [0, $limite],
            'perPage maior que LIMITE_POR_PAGINA deve ser ajustado' => [$limite + 10, $limite],
            'perPage dentro do limite deve manter o valor' => [$limite - 10, $limite - 10],
            'perPage igual ao LIMITE_POR_PAGINA deve manter o valor' => [$limite, $limite],
            'perPage negativo deve ser ajustado para LIMITE_POR_PAGINA' => [-10, $limite],
        ];
    }

    public function testGetAllReturnsEmptyWhenNoOrdersFoundForStatus()
    {
        $paginator = Mockery::mock(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class);

        $paginator->shouldReceive('items')->andReturn([]);
        $paginator->shouldReceive('currentPage')->andReturn(1);
        $paginator->shouldReceive('lastPage')->andReturn(1);
        $paginator->shouldReceive('count')->andReturn(0);

        $filters = ['status' => 999];

        $this->travelOrderRepositoryMock
            ->shouldReceive('getOrders')
            ->once()
            ->with($filters, 100)
            ->andReturn($paginator);

        $result = $this->travelOrderService->getAll($filters, 100);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);

        $this->assertCount(0, $result->items());
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
        $this->expectExceptionCode(404);

        $this->travelOrderService->findById(999);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
