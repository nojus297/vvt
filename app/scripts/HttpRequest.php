<?php

namespace App\scripts;

class HttpRequest
{
    private $client;

    public function get(string $url)
    {
        $url = str_replace(' ', '+', $url); //DIRTY FIX TODO
        $request = $this->client->get($url);

        return $request->getBody();
    }
    
    function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }
}

?>