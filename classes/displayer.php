<?php

namespace classes;

class Displayer extends Packer
{
    private $html;

    /**
     * Funkcia zobrazi zoznam na zaklade filtra
     * 
     * @param array $filter 
     * @return void
    **/
    public function Display($filter = []) {
        $Handler = Handler::getInstance();

        if ($error = $Handler->hasError() || CData::getInstance()->hasData()) {
            $this->startSearchResult();

            if ($Handler->hasError()) {
                $this->showError($Handler->getError());
            } else {
                foreach (parent::gatherInformation() as $header => $catdata) {
                    if (!in_array($header, $filter) && !empty($filter))
                        continue;

                    $this->createHeader($header);

                    foreach ($catdata as $category => $data) {
                        $this->createList($category, $data);
                    }
                }
            }

            $this->endSearchResult();
            echo $this->html;
        }
    }

    private function startSearchResult() {
        $this->html = "<div class='my-3 p-3 bg-body rounded shadow-sm'>\n";
        $this->html .= "<h6 class='border-bottom pb-2 mb-0'>Výsledok vyhľadávania</h6>\n";
    }

    private function endSearchResult() {
        $this->html .= "</div>\n";
    }

    private function showError($error) {
        $this->html .= "<div class='alert alert-danger mb-0' role='alert'>".$error."</div>\n";
    }

    private function createHeader($header) {
        $header = parent::transformShortcut($header);
        $this->html .= "<h6 class='mt-3 mb-0 py-2 bg-light text-center'>".$header."</h6>\n";
    }

    private function createList($category, $data) {
        $this->html .= "<ul class='list-group list-group-horizontal border-top'>\n";
            if (strlen($category) > 2)
                $this->html .= "<li class='list-group-item border-0 w-25'>".$category."</li>\n";
            $this->html .= "<li class='list-group-item border-0 flex-fill align-self-center'>".$data."</li>\n";
        $this->html .= "</ul>\n";
    }
}