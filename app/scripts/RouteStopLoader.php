<?php

namespace App\scripts;

class RouteStopLoader implements Loadable 
{
    private $request;
    private $loaded_route_stops;

    const TRAFI_API_URL = 'https://www.trafi.com/api/schedules/vilnius/';
    const TRANSPORT_TYPES = array('trolleybus', 'bus');
    const VALID_TRACKS = array('a-b', 'b-a');

    private function get_route_url($type, $route_id)
    {
        return self::TRAFI_API_URL . "schedule?scheduleId=" . $route_id . 
               "&transportType=" . $type;
    }

    public function parse_tracks(string $route_id, string $type)
    {
        $url = $this->get_route_url($route_id, $type);
        $raw = $this->request->get($url);
        $data = json_decode($raw);

        foreach($data->tracks as $track)
        {
            if(!in_array($track->id, self::VALID_TRACKS))
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
                $loaded_route_stops[$temp->get_hash()] = $temp;
            }
        }

    }

    public function load_from_web()
    {
        echo("Warning: routes are loaded from database, not from web");

        $routes = \App\Route::all();

        foreach($routes as $route)
        {
            $this->parse_tracks(
                $route->route_id,
                [1  => 'trolleybus', 2 => 'bus'][$route->type],
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
            $temp = new RouteStop((array) $route_stop);

            $route_stops[$temp->get_hash()] = $temp;
        }

        return $route_stops;
    }

    public function __construct(){
        $this->request = new HttpRequest();
    }
}

?>