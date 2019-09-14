<?php
    class DeparturesTracker
    {
        private $request, $filtered_stops, $degrees;

        public function __construct()
        {
            $this->request = new App\scripts\HttpRequest();
        }

        public function loop()
        {
            while((new DateTime()) < config('vvt.track_up_to'))
            {
                $vehicles = $this->get_vehicles();

                foreach($vehicles as $vehicle)
                {

                }
                sleep(4);
            }
        }

        private function parse_vehicle($vehicle)
        {
            
        }

        private function load_degrees()
        {
            $degrees = \DB::table('degree_calc')->get();

            foreach($degrees as $row)
            {
                $this->degrees[$row->route_stop_id] = $row->degree;
            }
        }

        private function filter_stops()
        {
            $route_stops = \DB::table('route_stops')->get();
    
            foreach($route_stops as $rs)
            {
                $stop = \DB::table('stops')->where(
                    'stop_id', $rs->stop_id
                )->get()[0];
                $stop->direction = $rs->direction;
                unset($stop->name);
                $this->filtered_stops[$rs->route_id] = $stop;
            }
        }

        private function get_close_stops(object $coords, $route_id)
        {
            $stops = array();
            foreach($this->filtered_stops[$route_id] as $stop)
            {
                $distance = distance($coords, (object)[
                    'lat' => $stop->lat,
                    'lng' => $stop->lng
                ]);
                if($distance <= config('vvt.min_track_range'))
                {
                    $stops[] = $stop;
                }
            }
            return null;
        }

        private function get_vehicles()
        {
            $url = config('vvt.stops.lt_vehicles_url');
            $data = $this->request->get($url);
            $data = explode("\n", $data);
            $vehicles = array();

            foreach($data as $line)
            {
                $dt = explode(",", $line);
                if(count($dt) < 6) continue;
                $vehicles[] = (object)[
                    'type' => (int) $dt[0],//config('vvt.types_conversion')[$dt[0]],
                    'no' => $dt[1],
                    'coord' => (object)[
                        'lat' => ((int) $dt[3]) / 1000000,
                        'lng' => ((int) $dt[2]) / 1000000,
                    ],
                    'angle' => (int) $dt[5],
                ];
            }

            return $vehicles;
        }
    }
?>