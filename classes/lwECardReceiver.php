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
        return $this->dh->loadECard($hash);
    }
    
    private function buildECard()
    {
        $dataArray = $this->dh->loadECard($this->hash);
        
        $template = file_get_contents(dirname(__FILE__) . '/../templates/ecard.tpl.html');
        $tpl = new lw_te($template);

        $tpl->reg("valueName", $dataArray["name"]);
        $tpl->reg("valueNachricht", nl2br($dataArray["nachricht"]));
    
        $this->output = $tpl->parse();
    }
        
    private function buildErrorMessage()
    {
        $template = file_get_contents(dirname(__FILE__).'/../templates/error.tpl.html');
        $tpl = new lw_te($template);
        $tpl->reg("meldung", "Ihre ECard konnte nicht geladen werden.");
        $tpl->reg("link", lw_page::getInstance()->getUrl());
        $this->output = $tpl->parse();
    }

}
