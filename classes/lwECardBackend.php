<?php

class lwECardBackend
{

    public class __construct()
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        include_once(dirname(__FILE__).'/lwECardDatahandler.php');
        $this->dh = new lwECardDatahandler();
    }
    
    public class execute()
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
                        $this->reloadPage(lw_page::getInstance()->getUrl(array("cmd"=>"done")));
                    }
                }
                $this->reloadPage(lw_page::getInstance()->getUrl(array("cmd"=>"error")));
                break;
                
            case "checkFormData":
                $this->checkFormData();
                break;
                
            default:
                $this->buildECardForm();
                break;
        }
    }
    
    public class getOutput()
    {
        return $this->output;
    }

    private function buildECardForm()
    {
        $this->output = "Form";
    }
    
    private function checkFormData()
    {
        $this->output = "checkMessage";
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
}
