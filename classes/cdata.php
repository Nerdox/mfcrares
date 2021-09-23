<?php

namespace classes;

class CData extends Singleton
{
    private $data = [];
    protected static $instance;

    /**
     * Funkcia ulozi nove data do novej premennej 
     * 
     * @param array $data
    **/
    public function initData($data) {
        $this->data = $data;
    }

    /**
     * Funkcia ulozi data do premennej 
     * 
     * @param string $parent
     * @param string $key 
     * @param string $data
    **/
    public function saveData($parent, $key, $data) {
        $parent = strval($parent);
        $key = strval($key);
        
        if (!isset($this->data[$parent]))
            $this->data[$parent] = [];

        if ($parent == $key)
            $this->data[$parent] = $data;
        else
            $this->data[$parent][$key] = $data;
    }

    /**
     * Funkcia zistuje, ci mame ulozene data
     * 
     * @return boolean
    **/
    public function hasData() {
        return !empty($this->data);
    }

    /**
     * Funkcia ziska vsetky ulozene data
     * 
     * @return array
    **/
    public function getData() {
        return $this->data;
    }
}