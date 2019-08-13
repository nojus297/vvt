<?php

namespace App\scripts;

interface Comparable
{
    public function get_hash();

    public function equal(Comparable $b);

    public function insert(); //insert entry to database

    public function delete(); //remove entry from database

    public function stringify();
}

?>