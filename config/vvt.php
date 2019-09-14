<?php
    return [

	    'transport_subtypes' => [
            'Greitasis autobusas',
            'Autobusas',
            'Troleibusas',
        ],

        'transport_types' => [
            'trolleybus',
            'bus',
        ],

        'types_conversion' => [
            'trolleybus' => 1,
            'bus' => 2,
            1 => 'trolleybus',
            2 => 'bus',
        ],

        'stops.lt_vehicles_url' => 'https://stops.lt/vilnius/gps.txt',

        'track_up_to' => (new DateTime())->setTime(23, 55),

        'min_degree_calc_range' => 10,

        'min_track_range' => 25,

        'trafi_api_url' => 'https://www.trafi.com/api/schedules/vilnius/',

        'valid_tracks' => [
            'a-b',
            'b-a',
        ],

        'log' => true,
    ]; 
?>