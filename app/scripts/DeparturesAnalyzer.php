<?php

namespace App\scripts;

use DateTime;

class DeparturesAnalyzer
{
    // private $departures;
    public $departures;

    public function analyze($result)
    {
        $this->departures = [];
        $this->load_expected_times($result->started_at, $result->finished_at);

        foreach($this->departures as $rs_id => $departures)
        {
            if(!array_key_exists($rs_id, $result->departures))
            {
                continue;
            }
            $this->analyze_departures($rs_id, $result->departures[$rs_id]);
        }
    }

    public function push()
    {
        foreach($this->departures as $departures)
        {
            foreach($departures as $departure)
            {
                \DB::table('departures')
                    ->whereDate('date', $departure->date)
                    ->where('route_stop_id', $departure->route_stop_id)
                    ->whereTime('expected_time', $departure->expected_time)
                    ->update(
                        'actual_time', $departure->actual_time->format('H:i:s')
                    );
            }
        }
    }

    private function analyze_departures($rs_id, $actual_times)
    {
        for($i = 0; $i < count($this->departures[$rs_id]); $i++)
        {
            if($i == count($actual_times))
            {
                break;
            }
            $this->departures[$rs_id][$i]->actual_time
                 = $actual_times[$i]->time;
        }
    }

    private function load_expected_times($from, $to)
    {
        foreach($this->get_route_stops() as $i => $route_stop)
        {
            $this->departures[$route_stop->id] = \DB::table('departures')
                ->whereDate('date', $from->format('Y-m-d'))
                ->where('route_stop_id', $route_stop->id)
                ->whereBetween('expected_time', [
                    $from->format('H:i:s'),
                    $to->format('H:i:s'),
                ])
                ->get();
        }
    }

    private function get_route_stops()
    {
        return \DB::table('route_stops')->get();
    }
}

?>