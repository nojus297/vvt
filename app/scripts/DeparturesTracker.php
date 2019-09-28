<?php

namespace App\scripts;

class DeparturesTracker
{
    private $request, $filtered_stops, $degrees, $route_ids, $departures;

    public function __construct()
    {
        $this->request = new HttpRequest();
        $this->filter_stops();
        $this->load_degrees();
        $this->load_routes();
        $this->departures = array();
        info('init completed');
    }

    public function get_actual_departures($up_to = null)
    {
        $result = new \stdClass();
        $result->started_at = Now();
        if($up_to == null)
        {
            $up_to = config('vvt.track_up_to');
        }
        // info($up_to->format('Y-m-d H:i:s'));
        while((Now()) < $up_to)
        {
            $vehicles = $this->get_vehicles();
            foreach($vehicles as $i => $vehicle)
            {
                $this->parse_vehicle($vehicle);
            }
            sleep(4);
        }
        $result->finished_at = $up_to;
        $result->departures = $this->departures;

        return $result;
    }

    private function process_departure($vehicle, $stop)
    {
        //info("{$vehicle->type} {$vehicle->no} arrived to {$stop->name}");
        $now = Now();
        if(!array_key_exists($stop->route_stop_id, $this->departures))
        {
            $departure = (object)[
                'time' => $now,
                'hash' => $this->coord_hash($vehicle),
                'stop' => $stop->name,//debug
                'no' => $vehicle->no,//
            ];
            $this->departures[$stop->route_stop_id][] = $departure;

            return;
        }
        $stop_departures = &$this->departures[$stop->route_stop_id];
        foreach(array_slice($stop_departures, -4) as $departure)
        {
            if($this->coord_hash($vehicle) == $departure->hash)
            {
                return;
            }
        }

        $start = count($stop_departures) - 1;
        for($i = $start; $i >= 0 && $start - $i < 4; $i--)
        {
            $current = $stop_departures[$i];
            $diff = $now->getTimestamp() - $current->time->getTimestamp(); 
            if($diff < 5)
            {
                continue;
            }
            $departure = (object)[
                'time' => $now,
                'hash' => $this->coord_hash($vehicle),
                'stop' => $stop->name,//debug
                'no' => $vehicle->no,//
            ];
            if($diff < 21) //update
            {
                $stop_departures[$i] = $departure;
            }
            else
            {
                $stop_departures[] = $departure;
            }

            return;
        }
    }

    private function coord_hash($coords)
    {
        return substr(md5($coords->lat . $coords->lng), 0, 5);
    }

    private function parse_vehicle($vehicle)
    {
        if(!in_array(
            config('vvt.types_conversion')[$vehicle->type],
            config('vvt.transport_types'))
        )
        {
            return;
        }
        $route_id = $this->route_ids[$vehicle->type][$vehicle->no];
        $stops = $this->get_close_stops($vehicle, $route_id);
        // if(count($stops)) info("yeayeay");

        foreach($stops as $stop)
        {
            $route_id = $this->route_ids[$vehicle->type][$vehicle->no];
            $rs_id = $stop->route_stop_id;
            $degree_diff = abs($vehicle->degree - $this->degrees[$rs_id]); 

            if($degree_diff <= config('vvt.max_track_degree_diff'))
            {
                $this->process_departure($vehicle, $stop);
            }
        }
    }

    private function load_degrees()
    {
        $degrees = \DB::table('degree_calc')->get();

        foreach($degrees as $row)
        {
            $this->degrees[$row->route_stop_id] = $row->degree;
        }
    }

    private function load_routes()
    {
        $routes = \DB::table('routes')->get();

        foreach($routes as $route)
        {
            $this->route_ids[$route->type][$route->no] = $route->route_id;
        }
    }

    private function filter_stops()
    {
        $route_stops = \DB::table('route_stops')->get();

        foreach($route_stops as $rs)
        {
            //info($rs->id);
            $stop = \DB::table('stops')->where(
                'stop_id', $rs->stop_id
            )->get()[0];
            $stop->direction = $rs->direction;
            $stop->route_stop_id = $rs->id;
            //unset($stop->name);
            $this->filtered_stops[$rs->route_id][] = $stop;
        }
    }

    private function get_close_stops(object $coords, $route_id)
    {
        $stops = array();
        foreach($this->filtered_stops[$route_id] as $stop)
        {
            $distance = distance($coords, $stop);
            if($distance <= config('vvt.min_track_range'))
            {
                $stops[] = $stop;
            }
        }
        return $stops;
    }

    private function get_vehicles()
    {
        $url = config('vvt.stops_lt_vehicles_url');
        $tries = 0;
        $data = null;
        while(++$tries <= config('vvt.max_request_retries'))
        {
            try {
                $data = $this->request->get($url);
            } catch (Exeption $e){
                \Log::warning("failed to load gps data (trie {$tries}");
                continue;
            }
            break;
        }
        if(!$data)
        {
            return [];
        }
        $data = explode("\n", $data);
        $vehicles = array();

        foreach($data as $line)
        {
            $dt = explode(",", $line);
            if(count($dt) < 6) continue;
            $vehicles[] = (object)[
                'type' => (int) $dt[0],//config('vvt.types_conversion')[$dt[0]],
                'no' => $dt[1],
                'lat' => ((int) $dt[3]) / 1000000,
                'lng' => ((int) $dt[2]) / 1000000,
                'degree' => (int) $dt[5],
            ];
        }

        return $vehicles;
    }
}
?>
