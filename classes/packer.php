<?php

namespace classes;

class Packer
{
    private $data; 

    /**
     * Funkcia vrati vsetky kategorie s udajmi
     *
     * @return array
    **/
    public function gatherInformation() {
        $this->data = CData::getInstance()->getData();

        $packer_data = [
            'ZAU' => $this->primaryInformation(),
            'PSU' => $this->registersInformation(),
            'ROR' => $this->mregisterInformation(),
            'RRZ' => $this->zregisterInformation(),
            'NCE' => $this->naceInformation(),
            'PPI' => $this->businnesTypes(),
            'OBC' => $this->activityFields()
        ];
        
        return $packer_data;
    }

    /**
     * Funkcia vrati vsetky zakladne udaje
     *
     * @return array
    **/
    private function primaryInformation() {
        $return = $this->informationLooper("AA", true);
        $return['Pocet zamestnancu'] = $this->data['KPP'][0];

        return $return;
    }

    /**
     * Funkcia vrati vsetky udaje o stavoch v registroch
     *
     * @return array
    **/
    private function registersInformation() {
        return $this->data['PSU'];
    }

    /**
     * Funkcia vrati vsetky udaje o stavoch v obchodnych registroch
     *
     * @return array
    **/
    private function mregisterInformation() {
        if (!isset($this->data['ROR']))
            return [];

        $return = [
            'Soud' => $this->data['ROR']['SZ']['SD']['T'],
            'Kod soudu' => $this->data['ROR']['SZ']['SD']['K'],
            'Spis' => $this->data['ROR']['SZ']['OV'],
        ];

        $second = $this->informationLooper($this->data['ROR']['SOR']);
        $second = array_map(array($this, 'strvar'), $second);
        $return = array_merge($return, $second);

        return $return;
    }

    /**
     * Funkcia vrati vsetky udaje v zivnostenskom registry
     *
     * @return array
    **/
    private function zregisterInformation() {
        return $this->informationLooper($this->data['RRZ'], true);
    }

    /**
     * Funkcia vrati vsetky udaje o klasifikacii v ekonomickych cinnostiach
     *
     * @return array
    **/
    private function naceInformation() {
        return $this->informationLooper($this->data['Nace']['NACE']);
    }

    /**
     * Funkcia vrati vsetky predmety podnikania
     *
     * @return array
    **/
    private function businnesTypes() {
        $return = $this->informationLooper($this->data['PPI']);
        if (is_array($return['PP'])) {
            $return[] = $return['PP'];
            unset($return['PP']);
            $return = array_merge(...$return);
        }

        return $return;
    }

    /**
     * Funkcia vrati vsetky obory cinnosti v registry zivnostenskeho podnikania
     *
     * @return array
    **/
    private function activityFields() {
        return $this->informationLooper($this->data['Obory_cinnosti']['Obor_cinnosti']);
    }

    /**
     * Funkcia vrati informacie ziskane z xml ktore vyzadujeme
     *
     * @param string|array $datapoint zdroj dat, ktore chceme ziskat
     * @param boolean $full zaisti kompletny zdroj dat zo zdroja
     * @return array
    **/
    private function informationLooper($datapoint, $full = false) {
        $loop = is_array($datapoint) ? $datapoint : $this->data;
        $return = [];

        foreach ($loop as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key_s => $value_s) {
                    if (!$full && $key_s == 0 || $full && is_numeric($key_s))
                        $key_s = $key;

                    if (is_array($value_s)) {
                        foreach ($value_s as $key_t => $value_t) {
                            if (!$full && $key_t == 0 || $full && is_numeric($key_t))
                                $key_t = $key_s;

                            $key_t = $this->transformShortcut($key_t);
                            $return[$key_t] = $value_t;
                        }
                    } else {
                        $key_s = $this->transformShortcut($key_s);
                        $return[$key_s] = $value_s;
                    }
                }
            } else {
                $key_f = $this->transformShortcut($key);
                $return[$key_f] = $value;
            }

            if (!is_array($datapoint) && $key == $datapoint)
                break;
        }

        return $return;
    }

    /**
     * Funkcia vrati informacie o stave v registroch do citatelnej podoby
     *
     * @link https://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.cz
     *       Informacie pod paragrafom s textom "Další informace o subjektu:"
     *
     * @param string $status cely string statusov
     * @return array
    **/
    public function subjectStatus($status) {
        $shortc = Handler::getShortcuts();
        $status = str_split($status);
        $reserved = [
            1, 8, 13, 16, 17, 18, 19,
            20, 24, 26, 27, 28, 29, 30
        ];

        $formatted = array();

        for ($i=0; $i < count($status); $i++) { 
            if (in_array($i+1, $status) || $status[$i] === "N")
                continue;

            switch ($status[$i]) {
                case 'A':
                    $status[$i] = "Platná registrace";
                    break;
                case 'S':
                    $status[$i] = "Skupinová registrace DPH";
                    break;
                case 'P':
                    $status[$i] = "Pozastavená činnost";
                    break;
                case 'E':
                    $status[$i] = "Insolvenční rejstřík";
                    break;
                default:
                    $status[$i] = "Zaniklá registrace";
                    break;
            }

            $formatted[$shortc['p'.($i+1)]] = $status[$i];
        }

        return $formatted;
    }

    /**
     * Funkcia pretransformuje skratku do jednoducheho textu
     *
     * Dodatok: V pripade, ze skratka nema priradeny text,
     *          vrati sa znenie skratky. 
     *
     * @param string $shorcut
     * @return string
    **/
    public function transformShortcut($shorcut) {
        $shortc = Handler::getShortcuts();
        if (isset($shortc[$shorcut]))
            return str_replace("_", " ", $shortc[$shorcut]);

        return $shorcut;
    }

    /**
     * Funkcia pretransformuje zaporne hodnoty do textu
     *
     * @param string $value
     * @return string
    **/
    private function strvar($value) {
        return $value ? $value : "Není";
    }
}