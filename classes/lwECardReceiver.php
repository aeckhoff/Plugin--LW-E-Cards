<?php

class lwECardReceiver
{

    public class __construct($dh, $hash)
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        $this->dh = $dh;
        $this->hash = $hash;
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
