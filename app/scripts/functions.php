<?php
    namespace App\scripts;
    function info($text)
    {
        echo($text . "\n");
    }

    function get_route_url($type, $route_id)
    {
        return config('vvt.trafi_api_url') . "schedule?scheduleId=" .
               $route_id . "&transportType=" . $type;
    }
    function get_departures_url($route_id, $stop_id)
    {
        $format = "https://www.trafi.com/api/times/vilnius/";
        $format .= "scheduled?scheduleId=%s&trackId=a-b&stopId=%s";
        return sprintf($format, $route_id, $stop_id);
    }
    function get_trafi_trafic_url($route_id, $dir)
    {
        $url = "https://www.trafi.com/api/realtime-vehicles/vilnius?";
        $url .= "scheduleId={$route_id}&trackId={$dir}";
        return $url;
    }
?>
