<?php

namespace App\scripts;

class StopLoader implements Loadable 
{
    private $request;
    private $loaded_stops;

    private function get_transport_url($type)
    {
        return config('vvt.trafi_api_url') . "all?transportType=" . $type;
    }
    private function get_route_url($type, $route_id)
    {
        return config('vvt.trafi_api_url') . "schedule?scheduleId=" . $route_id. 
               "&transportType=" . $type;
    }

    private function parse_stops_by_route_id($type, $route_id)
    {
        
        $url = $this->get_route_url($type, $route_id);
        $raw = $this->request->get($url);
        $data = json_decode($raw);

        echo($data->id . "\n");

        foreach($data->stops as $stop)
        {
            $temp = new Stop(
                $stop->id, 
                $stop->name,
                $stop->lat,
                $stop->lng
            );

            $this->loaded_stops[$temp->get_hash()] = $temp;
        }
    }

    private function parse_stops_by_subtype($type, $subtype)
    {
        if(!in_array($subtype->transportName, config('vvt.transport_subtypes')))
        {
            return;
        }

        foreach($subtype->schedules as $route)
        {
            $this->parse_stops_by_route_id($type, $route->scheduleId);
        }
    }

    function load_all_stops()
    {
        $this->loaded_stops = array();

        foreach (config('vvt.transport_types') as $type)
        {
            $raw = $this->request->get($this->get_transport_url($type));
            $data = json_decode($raw);

            foreach($data->schedulesByTransportId as $subtype)
            {
                $this->parse_stops_by_subtype($type, $subtype);

            }    
        }
    }

    public function load_from_web()
    {
        $this->load_all_stops();

        return $this->loaded_stops;
    }

    public function load_from_database()
    {
        $db = \App\stop::all();
        $stops = array();

        foreach($db as $stop)
        {
            $temp = new Stop(
                $stop->stop_id, 
                $stop->name,
                $stop->lat,
                $stop->lng
            );
            $stops[$temp->get_hash()] = $temp;
        }

        return $stops;
    }

    public function __construct(){
        $this->request = new HttpRequest();
    }
}

?>