<?php
    return [

	    'transport_subtypes' => [
            //'Greitasis autobusas',
            //'Autobusas',
            'Troleibusas',
        ],

        'transport_types' => [
            'trolleybus',
            //'bus',
        ],

        'types_conversion' => [
            'trolleybus' => 1,
            'bus' => 2,
            1 => 'trolleybus',
            2 => 'bus',
        ],

        'stops_lt_vehicles_url' => 'https://stops.lt/vilnius/gps.txt',

        'track_up_to' => \App\scripts\Now()->setTime(23, 55),

        'min_degree_calc_range' => 20,

        'min_track_range' => 25,

        'max_track_degree_diff' => 36,

        'trafi_api_url' => 'https://www.trafi.com/api/schedules/vilnius/',

        // Warning: changing this will break DeparturesLoader
        'valid_tracks' => [
            'a-b',
            'b-a',
        ],

        'log' => true,
    ]; 
?>