<?php

namespace App\scripts;

class DegreeCalculator
{
    private $updated, $request, $filtered_stops;

    private function get_stop_in_range(object $coords, $route_id, $dir)
    {
        foreach($this->filtered_stops[$route_id][$dir] as $stop)
        {
            $distance = distance($coords, (object)[
                'lat' => $stop->lat,
                'lng' => $stop->lng
            ]);
            if($distance <= config('vvt.min_degree_calc_range'))
            {
                return $stop;
            }
        }
        return null;
    }

    private function update_degree($stop_id, $route_id, $dir, $degree)
    {
        $route_stop_id = \DB::table('route_stops')->where([
            'stop_id' => $stop_id,
            'route_id' => $route_id,
            'direction' => $dir,
        ])->get()[0]->id;

        $calc_table = \DB::table('degree_calc');
        $row = $calc_table->where('route_stop_id', $route_stop_id);
        info("UPDATED stop: {$stop_id}, route: {$route_id}, degree: {$degree}");

        if(count($row->get()))
        {
            $old = $row->get()[0];
            $new = $old->degree + ($degree - $old->degree) / ($old->count + 1);
            $row->update([
                'degree' => $new,
                'count' => $old->count + 1,
            ]);
        }
        else
        {
            $calc_table->insert([
                'route_stop_id' => $route_stop_id,
                'degree' => $degree,
                'count' => 1,
            ]);
        }
    }

    private function parse_vehicles($vehicles, $route_id, $dir)
    {
        foreach($vehicles as $vehicle)
        {
            $closest_stop = $this->get_stop_in_range(
                $vehicle->coordinate, $route_id, $dir);

            if($closest_stop)
            {
                $this->update_degree(
                    $closest_stop->stop_id, $route_id, $dir, $vehicle->angle
                );
            }
        }
    }

    public function load()
    {
        $db = \App\Route::all();

        foreach($db as $route)
        {
            foreach(['a-b', 'b-a'] as $dir)
            {
                $url = get_trafi_trafic_url($route->route_id, $dir);
                $raw = $this->request->get($url);
                $vehicles = json_decode($raw)->mapMarkers;
                $this->parse_vehicles($vehicles, $route->route_id, $dir);
            }
        }
    }

    public function __construct()
    {
        $this->request = new HttpRequest();
        $this->filter_stops();
    }

    private function filter_stops()
    {
        $route_stops = \DB::table('route_stops')->get();

        foreach($route_stops as $rs)
        {
            $stop = \DB::table('stops')->where(
                'stop_id', $rs->stop_id
            )->get()[0];
            $this->filtered_stops[$rs->route_id][$rs->direction][] = $stop;
        }
    }
}

?>,