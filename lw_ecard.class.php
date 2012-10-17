<?php

class lw_ecard extends lw_plugin
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function buildPageOutput()
    {
        include_once(dirname(__FILE__).'/classes/lwECardDatahandler.php');
        $dh = new lwECardDatahandler();

        include_once(dirname(__FILE__).'/classes/lwECardCleaner.php');
        $cleaner = new lwECardCleaner($dh);
        $cleaner->clean();
        
        if ($this->request->getRaw('hash')) {
            include_once(dirname(__FILE__).'/classes/lwECardReceiver.php');
            $class = new lwECardReceiver($dh, $this->request->getRaw('hash'));
        }
        else {
            include_once(dirname(__FILE__).'/classes/lwECardSender.php');
            $class = new lwECardSender($dh,  $this->config);
        }
        $class->execute();
        return $class->getOutput();
    }
    
}
