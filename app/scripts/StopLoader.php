<?php

//use GuzzleHttp\Client;
namespace App;

class Stop {
    var $stop_id, $name, $lat, $lng;

    function __construct($stop_id, $name, $lat, $lng) {
        $this->stop_id = $stop_id;
        $this->name = $name;
        $this->lat = round($lng, 7);
        $this->lng = round($lat, 7);
    }
    function equal($a, $b) {
        return $a->stop_id == $b->stop_id && $a->name == $b->name && $a->lng == $b->lng && $a->lat == $b->lat;
    }
}

class StopLoader {
    private $loaded_stops;
    const TRAFI_API_URL = 'https://www.trafi.com/api/schedules/vilnius/';
    const TRANSPORT_SUBTYPES = array('Troleibusas', 'Autobusas', 'Greitasis autobusas');
    const TRANSPORT_TYPES = array('trolleybus', 'bus');  

    private function get($url){
        $client = new \GuzzleHttp\Client();
        $request = $client->get($url);
        return $request->getBody();
    }

    private function get_transport_url($type){
        return self::TRAFI_API_URL . "all?transportType=" . $type;
    }

    private function get_route_url($type, $route_id){
        return self::TRAFI_API_URL . "schedule?scheduleId=" . $route_id . "&transportType=" . $type;
    }

    private function parse_stops_by_route_id($type, $route_id){
        
        $url = $this->get_route_url($type, $route_id);
        $data = json_decode($this->get($url));

        echo($data->id . "\n");

        foreach($data->stops as $stop) {

            if(sizeof($this->loaded_stops) && array_key_exists($stop->id, $this->loaded_stops)){
                continue;
            }

            $loaded_stops[$stop->id] = new Stop(
                $stop->id, 
                $stop->name,
                $stop->lat,
                $stop->lng
            );

        }
    }


    private function parse_stops_by_subtype($type, $subtype){

        if(!in_array($subtype->transportName, self::TRANSPORT_SUBTYPES)){
            return;
        }
        //return $subtype->transportName;
        //print($subtype);

        foreach($subtype->schedules as $route) {
            
            $this->parse_stops_by_route_id($type, $route->scheduleId);

        }
    }


    function load_all_stops(){

        foreach (self::TRANSPORT_TYPES as $type) {

            $data = json_decode($this->get($this->get_transport_url($type)));
            //return $data->schedulesByTransportId;
            foreach($data->schedulesByTransportId as $subtype) {
                
                //return $subtype;
                $this->parse_stops_by_subtype($type, $subtype);

            }    
        }
        print($this->loaded_stops);
    }

    function __construct(){
        $this->loaded_stops = array();
    }
    
}
//$st = new StopLoader();
//$st->load_stops();