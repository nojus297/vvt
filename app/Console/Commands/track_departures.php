<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use stdClass;

class track_departures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "vvt:track_departures
                            {--l|load-expected : Load today's departures}
                            {--p|push-data : Push analyzed data to database}
                            {--t|time= : Time to track up to}
                            {--r|return : Return processed data}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track, analyze and push vehicle departures';

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
        if($this->option('load-expected'))
        {
            $loader = new \App\scripts\DeparturesLoader;
            $loader->load();
        }
        $time = $this->option('time');
        if($time)
        {
            $time = \App\scripts\lt_time($time);
        }
        $tracker = new \App\scripts\DeparturesTracker;
        $result  = $tracker->get_actual_departures($time);
        $analyzer = new \App\scripts\DeparturesAnalyzer();
        $analyzer->analyze($result);

        if($this->option('push-data'))
        {
            $analyzer->push();
        }
        if($this->option('return'))
        {
            $output = new stdClass;
            $output->actual_departures = $result->departures;
            $output->analyzed = $analyzer->departures;
            $this->line(\serialize($output));
        }
    }
}
