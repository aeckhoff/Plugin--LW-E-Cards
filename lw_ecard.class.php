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
            if($this->checkIp() == true){
                include_once(dirname(__FILE__).'/classes/lwECardSender.php');
                $class = new lwECardSender($dh,  $this->config);
            }else{
                die("Zugriff verweigert!");
            }        
        }
        $class->execute();
        return $class->getOutput();
    }
    
    function checkIp()
    {
        $ip = $_SERVER['REMOTE_ADDR']; 
        foreach ($this->config["ecard"]["allowed_ip"] as $allowedIp)
        {
            $ip_substr = substr($ip, 0, strlen($allowedIp));
            if($ip_substr == $allowedIp){
                return TRUE;
            }
        }
    }
    
}
