<?php

namespace App\scripts;

class Route implements Comparable
{
    var $route_id, $name, $type, $no, $hash;

    public function get_hash()
    {
        return $this->hash;
    }

    public function equal(Comparable $b)
    {
        $t = $this;

        return $t->hash == $b->hash;
    }

    public function insert()
    {
        $table = \DB::table('routes');

        $route = (array) $this;

        unset($route['hash']);

        $table->insert($route);
    }

    public function delete()
    {
        $table = \DB::table('routes');

        $table->where('route_id', $this->route_id)->delete();
    }

    public function stringify()
    {
        $t = $this;
        $format = "%s %d %d | %s";

        return sprintf($format, $t->route_id, $t->type, $t->no, $t->name);
    }

    private function make_hash()
    {
        $str = $this->route_id . $this->name . $this->type . $this->no;
        $this->hash = md5($str, true);
    }

    function __construct($route_id, $type, $no, $name)
    {
        $this->route_id = $route_id;
        $this->name = $name;
        $this->type = $type;
        $this->no = $no;

        $this->make_hash();
    }

}

?>