<?php

namespace App\scripts;

class RouteLoader implements Loadable 
{
    private $request;
    private $loaded_routes;

    const TRAFI_API_URL = 'https://www.trafi.com/api/schedules/vilnius/';
    const TRANSPORT_SUBTYPES = ['Troleibusas'];//array('Greitasis autobusas',
                                     //'Autobusas', 'Troleibusas');
    const TRANSPORT_TYPES = array('trolleybus', 'bus');  

    private function get_transport_url($type)
    {
        return self::TRAFI_API_URL . "all?transportType=" . $type;
    }
    private function get_route_url($type, $route_id)
    {
        return self::TRAFI_API_URL . "schedule?scheduleId=" . $route_id . 
               "&transportType=" . $type;
    }

    private function parse_routes_by_subtype($type, $subtype)
    {
        if(!in_array($subtype->transportName, self::TRANSPORT_SUBTYPES))
        {
            return;
        }

        foreach($subtype->schedules as $route)
        {
            $temp = new Route(
                $route->scheduleId,
                array('trolleybus' => 1, 'bus' => 2)[$type],
                $route->name,
                $route->longName
            );
            $this->loaded_routes[$temp->get_hash()] = $temp;
        }
    }

    function load_all_routes()
    {
        $this->loaded_routes = array();

        foreach (self::TRANSPORT_TYPES as $type)
        {
            $raw = $this->request->get($this->get_transport_url($type));
            $data = json_decode($raw);

            foreach($data->schedulesByTransportId as $subtype)
            {
                $this->parse_routes_by_subtype($type, $subtype);

            }    
        }
    }

    public function load_from_web()
    {
        $this->load_all_routes();

        return $this->loaded_routes;
    }

    public function load_from_database()
    {
        $db = \App\Route::all();
        $routes = array();

        foreach($db as $route)
        {
            $temp = new Route(
                $route->route_id, 
                $route->type,
                $route->no,
                $route->name
            );
            $routes[$temp->get_hash()] = $temp;
        }

        return $routes;
    }

    public function __construct(){
        $this->request = new HttpRequest();
    }
}

?>