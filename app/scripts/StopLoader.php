<?php

use Illuminate\Support\Facades\DB;
//use GuzzleHttp\Client;
namespace App;

class My_Stop {
    var $stop_id, $name, $lat, $lng;

    function __construct($stop_id, $name, $lat, $lng) {
        $this->stop_id = $stop_id;
        $this->name = $name;
        $this->lat = round($lng, 7);
        $this->lng = round($lat, 7);
    }


    static function equal($a, $b) {
        return $a->stop_id == $b->stop_id && $a->name == $b->name && $a->lng == $b->lng && $a->lat == $b->lat;
    }
}

class StopLoader {
    private $loaded_stops;
    const TRAFI_API_URL = 'https://www.trafi.com/api/schedules/vilnius/';
    const TRANSPORT_SUBTYPES = array('Greitasis autobusas', 'Autobusas', 'Troleibusas');
    const TRANSPORT_TYPES = array('trolleybus', 'bus');  

    private function get($url){
        $client = new \GuzzleHttp\Client();
        $request = $client->get($url);
        return $request->getBody();
    }

    private function print_stop($stop) {
        //echo json_encode($stop);
        echo $stop["stop_id"] . " | " . $stop["lat"] . " | " . $stop["lng"] . " | " . $stop["name"] . "\n"; 
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

            if(count($this->loaded_stops) && array_key_exists($stop->id, $this->loaded_stops)){
                continue;
            }
            $this->loaded_stops[$stop->id] = new My_Stop(
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

        $this->loaded_stops = array();

        foreach (self::TRANSPORT_TYPES as $type) {

            $data = json_decode($this->get($this->get_transport_url($type)));
            //return $data->schedulesByTransportId;
            foreach($data->schedulesByTransportId as $subtype) {
                
                //return $subtype;
                $this->parse_stops_by_subtype($type, $subtype);

            }    
        }
    }

    function get_diff($short = false){

        $deleted = array();
        $modified = array();
        $new = array();
        $db = stop::all();

        foreach($this->loaded_stops as $stop) {

            $res = $db->find($stop->stop_id);

            if($res == null) {
                array_push($new, (array) $stop);
            }
            else {
                if(!My_Stop::equal($stop, $res)){
                    array_push($modified, (array) $stop);
                }
            }
        }



        foreach($db as $stop) {

            if(!array_key_exists($stop->stop_id, $this->loaded_stops)){

                array_push($deleted, $stop);
            
            }
        }

        if($short) {
            echo("-----------Completed----------------\n\tDeleted stops: " . count($deleted) . "\n\tModified stops: " . 
            count($modified) . "\n\tNew stops: " . count($new) . "\n");
        }

        else {

            echo "New (" . count($new) . "):\n";
            foreach($new as $stop) {
                
                echo "+";
                $this->print_stop($stop);

            }
            echo "\nModified (" . count($modified) . "):\n";
            foreach($modified as $stop) {

                echo "+";
                $this->print_stop($stop);

                echo "-";
                $arr_stop = (array) ($db->find($stop["stop_id"]));
                $this->print_stop($db->find($stop["stop_id"]));

            }
            echo "\nDeleted (" . count($deleted) . "):\n";
            foreach($deleted as $stop) {

                echo "-";
                $this->print_stop($stop);

            }
        }

        $table = \DB::table('stops');

        $answer = readline("\nApply changes? (y/n) ");
        if($answer == "y"){
            
            $table->insert($new);
            
            foreach($modified as $stop){
                $table->where('stop_id', $stop["stop_id"])->update($stop);
            }

            foreach($deleted as $stop){
                $table->where('stop_id', $stop->stop_id)->delete();
            }
            echo "Operation completed";
        }

    }

    function __construct($auto = true){

        if($auto) {
            $this->load_all_stops();
            $this->get_diff(1);
        }

    }
    
}
//$st = new StopLoader();
//$st->load_stops();