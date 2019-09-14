<?php
    class DeparturesTracker
    {
        private $request;

        public function __construct()
        {
            $this->request = new App\scripts\HttpRequest();
        }

        

        public function load_stops()
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