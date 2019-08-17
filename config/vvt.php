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

        'types_conversion' =>[
            'trolleybus' => 1,
            'bus' => 2,
            1 => 'trolleybus',
            2 => 'bus',
        ],

        'trafi_api_url' => 'https://www.trafi.com/api/schedules/vilnius/',

        'valid_tracks' => [
            'a-b',
            'b-a',
        ],

        'log' => true,
    ]; 
?>