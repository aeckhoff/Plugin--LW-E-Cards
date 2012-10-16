<?php

class lwECardReceiver
{

    public function __construct($dh, $hash)
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        $this->dh = $dh;
        $this->hash = $hash;
    }
    
    public function execute()
    {
        if ($this->hashAvailable($this->hash)) {
            $this->buildECard();
        }
        else {
            $this->buildErrorMessage();
        }
    }
    
    public function getOutput()
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
