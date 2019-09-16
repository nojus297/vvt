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
    function get_departures_url($route_id, $stop_id, $direction)
    {
        $format = "https://www.trafi.com/api/times/vilnius/";
        $format .= "scheduled?scheduleId=%s&trackId=%s&stopId=%s";
        return sprintf($format, $route_id, $direction, $stop_id);
    }
    function get_trafi_trafic_url($route_id, $dir)
    {
        $url = "https://www.trafi.com/api/realtime-vehicles/vilnius?";
        $url .= "scheduleId={$route_id}&trackId={$dir}";
        return $url;
    }
    function distance($from, $to, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($from->lat);
        $lonFrom = deg2rad($from->lng);
        $latTo = deg2rad($to->lat);
        $lonTo = deg2rad($to->lng);
      
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
      
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
?>
