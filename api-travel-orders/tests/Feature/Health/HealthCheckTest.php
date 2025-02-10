<?php

namespace Tests\Feature\Health;

use App\Exceptions\TravelException;
use App\Services\Health\HealthApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use PHPUnit\Framework\MockObject\MockBuilder;
use Tests\Feature\TestCase;
use Throwable;

class HealthCheckTest extends TestCase
{
    public function testHealthApiSuccess(): void
    {   

        $response = $this->get('/api/travel/health');
        $response->assertStatus(200);
        $this->assertEquals(["message", "success", "result"], array_keys($response->json()));
        $this->assertEquals(["server", "status", "pod", "ip"], array_keys($response->json()["result"]));
    }
    public function testHealthApiFail(): void
    {   
        $mockHealthApi = Mockery::mock(HealthApi::class)->makePartial();
    
        $mockHealthApi->shouldReceive('getInfoPod')
            ->andThrow(new TravelException("Erro simulado", 500));
    
        $this->app->instance(HealthApi::class, $mockHealthApi);
    
        $response = $this->get('/api/travel/health');
        $response->assertStatus(500);
    
        $responseData = $response->json();
        $this->assertEquals("Erro simulado", $responseData['message']);
        $this->assertFalse($responseData['success']);
        $this->assertEmpty($responseData['errors']);
    }
    

    public function testApiLoadGeneratorSuccess()
    {
        $response = $this->json('GET', '/api/travel/load-generator');
        $this->assertEquals(["message", "success", "result"], array_keys($response->json()));
        $this->assertEquals(["execution_time", "message"], array_keys($response->json()["result"]));
        $response->assertStatus(200);
    }

    public function testTotalTimeApiLoadGenerator()
    {
        $startTime = microtime(true);
    
        $response = $this->json('GET', '/api/travel/load-generator');
    
        $tempoExecucao = round(microtime(true) - $startTime);
        $tempoMaximoMilisegundos = 1.5;
        
        $this->assertLessThanOrEqual($tempoMaximoMilisegundos, $tempoExecucao, "A requisição demorou mais que {$tempoMaximoMilisegundos} segundos.");
        $response->assertStatus(200);
        $this->assertEquals(["message", "success", "result"], array_keys($response->json()));
        $this->assertEquals(["execution_time", "message"], array_keys($response->json()["result"]));
    }

    public function testApiLoadGeneratorFail()
    {
        $mockHealthApi = Mockery::mock(HealthApi::class)->makePartial();
    
        $mockHealthApi->shouldReceive('loadGenerator')
            ->andThrow(new TravelException("Erro simulado", 500));
    
        $this->app->instance(HealthApi::class, $mockHealthApi);
    
        $response = $this->get('/api/travel/load-generator');
        $response->assertStatus(500);
    
        $responseData = $response->json();
        $this->assertEquals("Erro simulado", $responseData['message']);
        $this->assertFalse($responseData['success']);
        $this->assertEmpty($responseData['errors']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
    
}
