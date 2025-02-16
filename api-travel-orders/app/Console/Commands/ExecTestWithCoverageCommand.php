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
        passthru('php artisan test --coverage --coverage-html=tests/result/coverage');
    }
}
