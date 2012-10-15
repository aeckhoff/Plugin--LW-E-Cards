<?php

class lwECardReceiver
{

    public class __construct($hash)
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        include_once(dirname(__FILE__).'/lwECardDatahandler.php');
        $this->dh = new lwECardDatahandler();
    }
    
    public class execute()
    {
        if ($this->hashAvailable($this->hash)) {
            $this->buildECard();
        }
        else {
            $this->buildErrorMessage();
        }
    }
    
    public class getOutput()
    {
        return $this->output;
    }
    
    private function hashAvailable($hash)
    {
        return $bool;
    }
    
    private function buildECard()
    {
        $this->output = "ECard Output";
    }
        
    private function buildErrorMessage()
    {
        $this->output = "ErrorMessage";
    }

}
