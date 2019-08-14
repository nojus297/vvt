<?php

namespace App\scripts;

class RouteStop implements Comparable
{
    var $id, $type, $no, $stop_id, $direction, $degree, $hash;

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
        $format = "%d %s %s %s %d";

        return sprintf
        (
            $format,
            $t->type,
            $t->no,
            $t->stop_id,
            $t->direction,
            $t->degree
        );
    }

    private function make_hash()
    {
        $t = $this;
        $str = $t->type . $t->no . $t->stop_id . $t->direction;
        $this->hash = md5($str, true);
    }

    function __construct($type, $no, $stop_id, $direction)
    {
        $this->type = $type;
        $this->no = $no;
        $this->stop_id = $stop_id;
        $this->direction = $direction;
        $this->make_hash();
    }

}

?>