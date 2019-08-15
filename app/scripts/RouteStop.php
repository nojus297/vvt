<?php

namespace App\scripts;

class RouteStop implements Comparable
{
    var $id, $route_id, $stop_id, $direction, $degree, $hash;

    public function get_hash()
    {
        return $this->hash;
    }

    public function equal(Comparable $b)
    {
        return $this->hash == $b->hash;
    }

    public function insert()
    {
        $table = \DB::table('route_stops');

        $stop = (array) $this;
        unset($stop['hash']);
        unset($stop['degree']);

        $table->insert($stop);
    }

    public function delete()
    {
        $table = \DB::table('route_stops');

        $table->where('id', $this->id)->delete();
    }

    public function stringify()
    {
        $t = $this;
        $format = "%s %s %s %d";

        return sprintf
        (
            $format,
            $t->route_id,
            $t->stop_id,
            $t->direction,
            $t->degree
        );
    }

    private function make_hash()
    {
        $t = $this;
        $str = $t->route_id . $t->stop_id . $t->direction;
        $this->hash = md5($str, true);
    }

    function __construct(array $route_stop)
    {
        $this->route_id = $route_stop['route_id'];
        $this->stop_id = $route_stop['stop_id'];
        $this->direction = $route_stop['direction'];
        $this->make_hash();
    }

}

?>