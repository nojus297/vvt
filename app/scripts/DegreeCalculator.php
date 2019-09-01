<?php

namespace App\scripts;

class DegreeCalculator
{
    private $updated, $request;

    public function load()
    {
        $db = \App\Route::all();

        foreach($db as $route)
        {
            foreach(['a-b', 'b-a'] as $dir)
            {
                $url = get_trafi_trafic_url($route->route_id, $dir);
                $raw = $this->request->get($url);
                $vehicles = json_decode($raw);
                
            }
        }
    }

    public function __construct()
    {
        $this->request = new HttpRequest();
    }
}

?>,