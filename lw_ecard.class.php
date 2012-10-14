<?php

class lw_ecard extends lw_plugin
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function buildPageOutput()
    {
        if ($this->request->getRaw('hash')) {
            include_once(dirname(__FILE__).'/classes/lwECardFrontend.php');
            $class = new lwECardFrontend($this->request->getRaw('hash'));
        }
        else {
            include_once(dirname(__FILE__).'/classes/lwECardBackend.php');
            $class = new lwECardBackend();
        }
        $class->execute();
        return $class->getOutput();
    }
    
}
