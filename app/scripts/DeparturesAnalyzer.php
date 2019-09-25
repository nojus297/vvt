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
                if(!$departure->actual_time)
                {
                    continue;
                }
                \DB::table('departures')
                    ->whereDate('date', $departure->date)
                    ->where('route_stop_id', $departure->route_stop_id)
                    ->whereTime('expected_time', $departure->expected_time)
                    ->update(
                        ['actual_time' => $departure->actual_time->format('H:i:s')]
                    );
            }
        }
    }

    private function analyze_departures($rs_id, $actual_times)
    {
        if(!count($actual_times) || !count($this->departures[$rs_id]))
        {
            return;
        }
        $i_ac = $i_dp = 0;
        //echo($rs_id . '|');
        // check too early
        //if($rs_id == 30)
        //{
       //     echo "there";
        //}
        if(count($this->departures[$rs_id]) == 0){
            
        }
        while(
            $actual_times[$i_ac]->time
            ->diff($this->departures[$rs_id][$i_dp]->exp) 
            > config('vvt.max_too_early_offset'))
        {
            $i_dp++;
            if($i_dp > count($this->departures[$rs_id]))
            {
                return;
            }
        }
        $this->departures[$rs_id][$i_dp]->actual_time = $actual_times[$i_ac]->time;
        $i_ac++;
        $i_dp++;


        for(; $i_ac < count($actual_times); $i_ac++)
        {
            // is next departure expected time less than actual?
            if($i_dp + 1 < count($this->departures[$rs_id]) &&
               $this->departures[$rs_id][$i_dp]->exp <= $actual_times[$i_ac]->time)
            {
                continue;
            }
            if($i_dp < count($this->departures[$rs_id]))
            {
                $this->departures[$rs_id][$i_dp]->actual_time
                 = $actual_times[$i_ac]->time;
                $i_dp++;
            }
            
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
            foreach($this->departures[$route_stop->id] as $dep)
            {
                $dep->exp = new DateTime(
                    $from->format('Y-m-d') . ' ' . $dep->expected_time
                );
            }
        }
    }

    private function get_route_stops()
    {
        return \DB::table('route_stops')->get();
    }
}

?>