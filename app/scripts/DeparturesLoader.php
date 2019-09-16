<?php

    namespace App\scripts;

    class DeparturesLoader
    {
        private $request;

        private function parse_route_stop($rs)
        {
            // Warning: the folowing depends on vvt.valid_tracks being only a-b
            // or b-a
            $url = get_departures_url(
                $rs->route_id, $rs->stop_id, $rs->direction
            );
            $raw = $this->request->get($url);
            $timetable = json_decode($raw)->scheduled->days[0];
            $departures = [];

            if($timetable->name != "Šiandien")
            {
                throw new \Exeption('No todays timetables');
            }

            foreach($timetable->scheduledTimes as $departure)
            {
                array_push($departures, [
                    'date' => date("Y-m-d"),
                    'route_stop_id' => $rs->id,  
                    'expected_time' => $departure->exactTime,
                    'is_main' => ($departure->trackId == $rs->direction),
                ]);
            }
            $table = \DB::table('departures');
            $table->insert($departures);
        }

        public function load()
        {
            $route_stops = \App\RouteStop::all();

            foreach($route_stops as $i => $route_stop)
            {
                if(config('vvt.log'))
                {
                    info("{$i} / " . count($route_stops));
                }

                $this->parse_route_stop($route_stop);

            }
        }

        public function __construct(){
            $this->request = new HttpRequest();
        }
    }
    
?>