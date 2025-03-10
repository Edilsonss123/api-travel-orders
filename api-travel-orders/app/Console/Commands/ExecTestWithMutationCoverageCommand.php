<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecTestWithMutationCoverageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mutation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa o teste de mutação com Infection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Executando os testes com cobertura em XML para mutação...');

        $exitCode = null;
        passthru('php artisan test --env=testing --coverage --coverage-xml=report/tests/mutation/coverage-xml', $exitCode);
        if ($exitCode !== 0) {
            $this->error('[1] coverage-xml: Erro ao rodar os testes com cobertura. Execução interrompida.');
            exit(1);
        }

        $this->info('Gerando test-results.junit...');
        passthru('php artisan test --env=testing --log-junit=report/tests/mutation/coverage-xml/test-results.junit.xml', $exitCode);
        if ($exitCode !== 0) {
            $this->error('[2] log-junit: Erro ao gerar o arquivo JUnit. Execução interrompida.');
            exit(1);
        }

        $this->info('Executando os testes de mutação...');
        $command = 'vendor/bin/infection --coverage=report/tests/mutation/coverage-xml --configuration=infection.json5 --threads=4';
        passthru($command, $exitCode);
        if ($exitCode !== 0) {
            $this->error('[3] infection: Erro ao rodar os testes de mutação. Execução interrompida.');
            exit(1);
        }
    }
}
