<?php

class lwECardSender extends lw_object
{

    public function __construct($dh)
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        $this->dh = $dh;
    }
    
    public function execute()
    {
        switch($this->request->getAlnum('cmd')) {

            case "error":
                $this->showErrorMessage();
                break;
                
            case "done":
                $this->showDoneMessage();
                break;
                
            case "sendECard":
                $saveOK = $this->saveECard();
                if ($saveOK) {
                    $sendOK = $this->sendECard();
                    if ($saveOK) {
                        $this->pageReload(lw_page::getInstance()->getUrl(array("cmd"=>"done")));
                    }
                }
                $this->pageReload(lw_page::getInstance()->getUrl(array("cmd"=>"error")));
                break;
                
            case "preview":
                $this->showPreview();
                break;
                
            case "checkFormData":
                $error = $this->checkFormData();
                if ($error["fehler"] == "kein fehler") {
                     $this->pageReload(lw_page::getInstance()->getUrl(array("cmd"=>"preview")));
                }
                $this->buildECardForm($error);
                break;
                
            default:
                $this->buildECardForm();
                break;
        }
    }
    
    public function getOutput()
    {
        return $this->output;
    }

    private function buildECardForm($error = false)
    {
        $template = file_get_contents(dirname(__FILE__) . '/../templates/email_eingabeform.tpl.html');
        $tpl = new lw_te($template);
        
        if($error != false){
            foreach ($error as $e) {
                if($e != "fehler" && $e != 0){
                    $tpl->setIfVar("error".$e);
                }
            }
        }
        
        $tpl->reg("adress", lw_page::getInstance()->getUrl(array("cmd"=>"checkFormData")));
        $tpl->reg("valueName", $this->request->getRaw("name"));
        $tpl->reg("valueAbsender", $this->request->getRaw("absender"));
        $tpl->reg("valueEmpfaenger", $this->request->getRaw("empfaenger"));
        $tpl->reg("valueBetreff", $this->request->getRaw("betreff"));
        $tpl->reg("valueNachricht", $this->request->getRaw("nachricht"));
        
        $this->output = $tpl->parse();
    }
    
    private function checkFormData()
    {
        $error = array(
            "fehler" => "kein fehler",
            "name" => 0,
            "absender" => 0,
            "empfaenger" => 0,
            "betreff" => 0,
            "nachricht" => 0
        );
        
        if($this->request->getAlnum("name") == "" || strlen($this->request->getAlnum("name")) > 255){
            $error["fehler"] = "fehler";
            $error["name"] = 1;
        }
        
        if($this->isEmail($this->request->getRaw("absender")) == false){
            $error["fehler"] = "fehler";
            $error["absender"] = 2;
        };
        
        $empfaenger = explode(",", preg_replace('/(\r\n|\r|\n)/s',',', $this->request->getRaw("empfaenger")));
        foreach ($empfaenger as $mail) {
            if($this->isEmail($mail) == false){
                $error["fehler"] = "fehler";
                $error["empfaenger"] = 3;
            }
        };
        
        if($this->request->getAlnum("betreff") == "" || strlen($this->request->getAlnum("betreff")) > 255){
            $error["fehler"] = "fehler";
            $error["betreff"] = 4;
        }
        
        if($this->request->getRaw("nachricht") == "" || strlen($this->request->getRaw("nachricht")) > 4000){
            $error["fehler"] = "fehler";
            $error["nachricht"] = 5;
        }
        
        return $error;
    }
    
    private function sendECard()
    {
        return $ok;
    }
    
    private function saveECard()
    {
        return $ok;
    }
    
    private function showDoneMessage()
    {
        $this->output = "doneMessage";
    }
    
    private function showErrorMessage()
    {
        $this->output = "errorMessage";
    }
    
    private function showPreview()
    {
        $template = file_get_contents(dirname(__FILE__) . '/../templates/ecard.tpl.html');
        $tpl = new lw_te($template);
        
        $tpl->reg("edit_link", lw_page::getInstance()->getUrl());
        $tpl->reg("send_link", lw_page::getInstance()->getUrl(array("cmd"=>"sendECard")));
        $tpl->reg("valueName", $this->request->getRaw("name"));
        $tpl->reg("valueNachricht", $this->request->getRaw("nachricht"));
        
        
        $this->output = $tpl->parse();
    }
    
    static function isEmail($email)
    {
        if ($email == ""){
            return FALSE;
        }
        elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == TRUE){
            return TRUE;
        }
        return FALSE;
    }
}
