<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Contracts\RatesImporter;

class Currencies extends Command
{
    private $importer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports currency rates to local database';

    /**
     * Create a new command instance.
     *
     * @var RatesImporter $importer
     */
    public function __construct(RatesImporter $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->importer->import();
    }
}
