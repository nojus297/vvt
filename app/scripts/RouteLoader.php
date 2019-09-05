<?php

namespace App\scripts;

class RouteLoader implements Loadable 
{
    private $request;
    private $loaded_routes;

    private function get_transport_url($type)
    {
        return config('vvt.trafi_api_url') . "all?transportType=" . $type;
    }
    private function get_route_url($type, $route_id)
    {
        return config('vvt.trafi_api_url') . "schedule?scheduleId=" . $route_id. 
               "&transportType=" . $type;
    }

    private function parse_routes_by_subtype($type, $subtype)
    {
        if(!in_array($subtype->transportName, config('vvt.transport_subtypes')))
        {
            return;
        }

        foreach($subtype->schedules as $route)
        {
           // if(strpos($route->scheduleId, '(') !== false)
          //  {
                //continue; //exclude tik/nuo/iki marsrutus
            //}
            //else continue;

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

        foreach (config('vvt.transport_types') as $type)
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