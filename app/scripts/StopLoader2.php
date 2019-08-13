<?php

namespace App\scripts;

class StopLoader2 implements Loadable 
{
    private $request;
    private $loaded_stops;

    const TRAFI_API_URL = 'https://www.trafi.com/api/schedules/vilnius/';
    const TRANSPORT_SUBTYPES = array('Greitasis autobusas',
                                     'Autobusas', 'Troleibusas');
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
        if(!in_array($subtype->transportName, self::TRANSPORT_SUBTYPES))
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

        foreach (self::TRANSPORT_TYPES as $type)
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