<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class track_departures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vvt:track_departures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track vehicle departures';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tracker = new \App\scripts\DeparturesTracker;
        $result  = $tracker->get_actual_departures();
        $analyzer = new \App\scripts\DeparturesAnalyzer();
        $analyzer->analyze($result);
        // $analyzer->push();
    }
}
