<?php

namespace App\scripts;

interface Loadable
{
    public function load_from_web();
    public function load_from_database();
}

?>