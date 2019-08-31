<?php

    namespace App\scripts;

    class Syncer
    {
        private $database_data, $web_data, $loader;
        public $to_add, $to_delete;

        public function load()
        {
            $this->database_data = $this->loader->load_from_database();

            $this->web_data = $this->loader->load_from_web();
        }

        public function check_diff()
        {
            $this->to_add = array();
            $this->to_delete = array();

            if(!is_array($this->database_data) || !is_array($this->web_data))
            {
                throw new \Exception('Data is not an array. Not loaded?');
            }

            foreach($this->web_data as $item)
            {
                if(!array_key_exists($item->get_hash(), $this->database_data))
                {
                    array_push($this->to_add, $item);
                }
            }

            foreach($this->database_data as $item)
            {
                if(!array_key_exists($item->get_hash(), $this->web_data))
                {
                    array_push($this->to_delete, $item);
                }
            }

        }


        public function apply(bool $delete = true, bool $insert = true)
        {
            if(!is_array($this->to_add) || !is_array($this->to_delete))
            {
                throw new Exception('Diffs are not arrays. Not compared?');
            }

            if($delete)
            {
                foreach($this->to_delete as $item)
                {
                    $item->delete();
                }
            }

            if($insert)
            {
                foreach($this->to_add as $item)
                {
                    $item->insert();
                }
            }
        }

        function __construct(Loadable $loader)
        {
            $this->loader = $loader;
        }        
    }
    
?>