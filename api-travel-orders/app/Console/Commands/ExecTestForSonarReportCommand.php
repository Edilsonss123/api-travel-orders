<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecTestForSonarReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sonar';

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
        $this->info('Executando os testes com relatorio sonarqube...');
        $exitCode = null;


        passthru('php artisan test  --testsuite=Unit --log-junit=report/tests/sonar/test-results.junit.xml', $exitCode);
        if ($exitCode !== 0) {
            $this->error('[1] coverage-clover: Executando os testes com relatorio sonarqube.');
            exit(1);
        }
        passthru('php artisan test --coverage-clover=report/tests/sonar/sonar.xml', $exitCode);
        if ($exitCode !== 0) {
            $this->error('[1] coverage-clover: Executando os testes com relatorio sonarqube.');
            exit(1);
        }
    }
}
