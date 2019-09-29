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

        'track_up_to' => \App\scripts\Now()->setTime(20, 35),

        'min_degree_calc_range' => 20,

        'min_track_range' => 80,

        'max_track_degree_diff' => 40,

        'min_track_time_arr' => [4, 30],

        'max_track_time_arr' => [23, 59, 50],

        'max_too_early_offset' => new DateInterval('PT5M'),

        'max_request_retries' => 5,

        'failed_request_delay_us' => (int)(0.1 * 1000000),

        'trafi_api_url' => 'https://www.trafi.com/api/schedules/vilnius/',

        // Warning: changing this will break DeparturesLoader
        'valid_tracks' => [
            'a-b',
            'b-a',
        ],

        'log' => true,
    ]; 
?>