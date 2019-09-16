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

        'min_degree_calc_range' => 10,

        'trafi_api_url' => 'https://www.trafi.com/api/schedules/vilnius/',

        // Warning: changing this will break DeparturesLoader
        'valid_tracks' => [
            'a-b',
            'b-a',
        ],

        'log' => true,
    ]; 
?>