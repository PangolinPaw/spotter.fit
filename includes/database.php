<?php

	class MyDB extends SQLite3
    {
        function __construct()
        {
            $this->open('data/data.db'); 
        }
    }
    $db = new MyDB();

?>