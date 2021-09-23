<?php

namespace classes;

class Handler extends Singleton
{
    private $error;
    private static $shortcuts = [];

    /**
     * Funkcia ziska informacie informacie o spolocnosti,
     *      transformuje ich do array-a a ulozi ich
     * 
     * @param integer $ico ico spolocnosti, ktoru chceme vyhladat
     * @return void
    **/
    public function getCompany($ico) {
        if (!is_numeric($ico) || mb_strlen($ico) < 8 || is_array($ico)) {
            $this->error = "Bad format";
            return;
        }

        $url = sprintf('http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=%s', $ico);
        $xml = file_get_contents($url);
        
        $response = simplexml_load_string($xml);
        if (!$response) {
            $this->error = "No response available!";
            return;
        }

        $names = $response->getDocNamespaces();
        $data = $response->children($names['are'])->children($names['D']);

        if (isset($data->E)) {
            $this->error = $data->E->ET;
            return;
        }

        $elements = $data->VBAS;
        $CData = CData::getInstance();

        $CData->initData(json_decode(json_encode($elements), true));
        $CData->saveData("AA", "AU", (array)$elements->AA->AU->children($names['U']));
        $CData->saveData("PSU", "PSU", Packer::subjectStatus($elements->PSU));
    }

    /**
     * Funkcia ziska error, ktory ako prvy vznikol pri ziskavani dat
     * 
     * @return string
    **/
    public function getError() {
        return $this->error;
    }

    /**
     * Funkcia zistuje, ci sa vyskytol problem
     * 
     * @return boolean
    **/
    public function hasError() {
        return !empty($this->error);
    }

    /**
     * Funkcia zabezpecuje ziskavanie skratiek zo subora / premennej
     * 
     * @link http://wwwinfo.mfcr.cz/ares/xml_doc/schemas/documentation/zkr_103.txt
     *
     * @return array
    **/
    public static function getShortcuts() {
        if (empty(self::$shortcuts)) {
            $shorts = file("helper/shortcuts.txt");

            foreach ($shorts as $i => $line) {
                $line_arr = explode("/", $line);
                self::$shortcuts[$line_arr[0]] = $line_arr[1];
            }
        }

        return self::$shortcuts;
    }
}