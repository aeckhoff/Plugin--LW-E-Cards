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
            include_once(dirname(__FILE__).'/classes/lwECardReceiver.php');
            $class = new lwECardreceiver($this->request->getRaw('hash'));
        }
        else {
            include_once(dirname(__FILE__).'/classes/lwECardSender.php');
            $class = new lwECardSender();
        }
        $class->execute();
        return $class->getOutput();
    }
    
}
