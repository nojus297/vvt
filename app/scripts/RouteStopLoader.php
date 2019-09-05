<?php

namespace App\scripts;

class RouteStopLoader implements Loadable 
{
    private $request;
    private $loaded_route_stops;

    private function get_route_url($type, $route_id)
    {
        return config('vvt.trafi_api_url') . "schedule?scheduleId=" . $route_id. 
               "&transportType=" . $type;
    }

    public function parse_tracks(string $route_id, string $type)
    {
        $url = $this->get_route_url($type, $route_id);
        $raw = $this->request->get($url);
        $data = json_decode($raw);

        foreach($data->tracks as $track)
        {
            if(!in_array($track->id, config('vvt.valid_tracks')))
            {
                continue;
            }

            foreach($track->stops as $stop)
            {
                $temp = new RouteStop([
                    'route_id' => $route_id,
                    'stop_id' => $stop->stopId,
                    'direction' => $track->id,
                ]);
                $this->loaded_route_stops[$temp->get_hash()] = $temp;
            }
        }

    }

    public function load_from_web()
    {
        info("Warning: routes are loaded from database, not from web");

        $routes = \App\Route::all();

        foreach($routes as $i => $route)
        {
            if(config('vvt.log'))
            {
                info("{$i}/" . count($routes));
            }
            $this->parse_tracks(
                $route->route_id,
                config('vvt.types_conversion')[$route->type],
            );
        }
        
        return $this->loaded_route_stops;
    }

    public function load_from_database()
    {
        $db = \App\RouteStop::all();
        $route_stops = array();

        foreach($db as $route_stop)
        {
            $temp = new RouteStop([
                'id' => $route_stop->id,
                'route_id' => $route_stop->route_id,
                'stop_id' => $route_stop->stop_id,
                'direction' => $route_stop->direction,
            ]);

            $route_stops[$temp->get_hash()] = $temp;
        }

        return $route_stops;
    }

    public function __construct(){
        $this->request = new HttpRequest();
    }
}

?>