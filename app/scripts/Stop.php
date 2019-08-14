<?php

namespace App\scripts;

class Stop implements Comparable
{
    const COORD_PRECISION = 7;
    var $stop_id, $name, $lat, $lng, $hash;

    public function get_hash()
    {
        return $this->hash;
    }

    public function equal(Comparable $b)
    {
        $t = $this;

        return $t->hash == $b->hash && $t->stop_id == $b->stop_id &&
               $t->name == $b->name && $t->lat == $b->lat &&
               $t->lng == $b->lng;
    }

    public function insert()
    {
        $table = \DB::table('stops');

        $stop = (array) $this;

        unset($stop['hash']);

        $table->insert($stop);
    }

    public function delete()
    {
        $table = \DB::table('stops');

        $table->where('stop_id', $this->stop_id)->delete();
    }

    public function stringify()
    {
        $t = $this;
        $p = self::COORD_PRECISION;
        $format = "%s %.{$p} %.{$p} | %s";

        return sprintf($format, $t->stop_id, $t->lat, $t->lng, $t->name);
    }

    private function make_hash()
    {
        $str = $this->stop_id . $this->name;
        $str .= number_format($this->lat, self::COORD_PRECISION);
        $str .= number_format($this->lng, self::COORD_PRECISION);
        $this->hash = md5($str, true);
    }

    function __construct($stop_id, $name, $lat, $lng)
    {
        $this->stop_id = $stop_id;
        $this->name = $name;
        $this->lat = round($lat, self::COORD_PRECISION);
        $this->lng = round($lng, self::COORD_PRECISION);

        $this->make_hash();
    }

}

?>