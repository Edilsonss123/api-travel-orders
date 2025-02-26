<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecTestWithCoverageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:coverage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa os testes com cobertura de código gerando um relatório HTML';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Executando os testes com cobertura em HTML...');
        $exitCode = null;
        passthru('php artisan test --env=testing --coverage --coverage-html=report/tests/coverage', $exitCode);
        if ($exitCode !== 0) {
            $this->error('[1] coverage-html: Erro ao rodar os testes com cobertura. Execução interrompida.');
            exit(1);
        }
        
        $exitCode = null;
        passthru('php artisan test --env=testing --coverage --coverage-xml=report/tests/coverage-xml/', $exitCode);
        if ($exitCode !== 0) {
            $this->error('[2] coverage-xml: Erro ao rodar os testes com cobertura. Execução interrompida.');
            exit(1);
        }
    }
}
