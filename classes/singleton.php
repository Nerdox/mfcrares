<?php

namespace classes;

class Singleton
{
    protected static $instances = [];

    /**
     * Funkcia zabezpecuje ziskanie instancie z
     * z vyvolanej classy
     * 
     * @return instance
    **/
    public static function getInstance() {
        $class = get_called_class();

        if (!isset(self::$instances[$class]))
            self::$instances[$class] = new $class;

        return self::$instances[$class];
    }
}

include "classes/handler.php";
include "classes/cdata.php";
include "classes/packer.php";
include "classes/displayer.php";