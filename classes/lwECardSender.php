<?php

class lwECardSender
{

    public class __construct($dh)
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        $this->dh = $dh;
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
                
            case "preview":
                $this->showPreview();
                break;
                
            case "checkFormData":
                $error = $this->checkFormData();
                if (!$error) {
                    $this->reloadPage(lw_page::getInstance()->getUrl(array("cmd"=>"preview")));
                }
                $this->buildECardForm($error);
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

    private function buildECardForm($error)
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
    
    private function showPreview()
    {
        $this->output = "showpreview";
    }
}
