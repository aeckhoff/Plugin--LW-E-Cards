<?php

class lwECardSender extends lw_object
{

    public function __construct($dh,$config,$plugin,$repository)
    {
        $this->request = lw_registry::getInstance()->getEntry("request");
        $this->response= lw_registry::getInstance()->getEntry("response");
        $this->dh = $dh;
        $this->config = $config;
        $this->pluginData = $repository->plugins()->loadPluginData($plugin["pluginname"], $plugin["oid"]);
        
    }
    
    public function execute()
    {
        switch($this->request->getAlnum('cmd')) {

            case "error":
                $this->showErrorMessage();
                break;
                
            case "done":
                unset($_SESSION["lw_ecard"]);
                $this->showDoneMessage();
                break;
                
            case "sendECard":
                $saveOK = $this->saveECard();
                if ($saveOK == TRUE) {
                    $sendOK = $this->sendECard();
                    if ($sendOK == TRUE) {
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
        $js.='  tinyMCE.init({'.PHP_EOL;
        $js.='      mode : "textareas",'.PHP_EOL;
        $js.='      theme : "advanced",'.PHP_EOL;
        $js.='      editor_selector : "mceEditor",'.PHP_EOL;
        $js.='      editor_deselector : "mceNoEditor",'.PHP_EOL;
        $js.='      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,",'.PHP_EOL;
        $js.='      theme_advanced_buttons2 : "undo,redo,|,link,unlink,|,forecolor,|,hr,|,sub,sup,|,charmap,|,help",'.PHP_EOL;
        $js.='      theme_advanced_buttons3 : "",'.PHP_EOL;
        $js.='      theme_advanced_toolbar_location : "top",'.PHP_EOL;
        $js.='      theme_advanced_toolbar_align : "left"'.PHP_EOL;
        $js.='  });'.PHP_EOL;
        $this->response->usejQuery();
        $this->response->addHeaderItems("jsfile", $this->config['url']['media']."tinymce/jscripts/tiny_mce/tiny_mce.js");
        $this->response->addHeaderItems("js", $js);
        
        $template = $this->pluginData["parameter"]["formular_template"];
        $tpl = new lw_te($template);
        
        if($error != false){
            foreach ($error as $e) {
                if($e != "fehler" && $e != 0){
                    $tpl->setIfVar("error".$e);
                }
            }
        }
        
        $tpl->reg("adress", lw_page::getInstance()->getUrl(array("cmd"=>"checkFormData")));
        if(array_key_exists("lw_ecard", $_SESSION)){
            $tpl->reg("valueName", $_SESSION["lw_ecard"]["name"]);
            $tpl->reg("valueAbsender", $_SESSION["lw_ecard"]["absender"]);
            $tpl->reg("valueEmpfaenger", $_SESSION["lw_ecard"]["empfaenger"]);
            $tpl->reg("valueBetreff", $_SESSION["lw_ecard"]["betreff"]);
            $tpl->reg("valueNachricht", $_SESSION["lw_ecard"]["nachricht"]);
        }        
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
        
        $this->setDataArray();
        return $error;
    }
    
    private function sendECard()
    {
        include_once(dirname(__FILE__).'/zendEmail.php');
        $zendEmail = new zendEmail($this->config);
        $empfaenger = explode(",", preg_replace('/(\r\n|\r|\n)/s',',', $_SESSION["lw_ecard"]["empfaenger"]));
        foreach ($empfaenger as $mail) {
            $boolZendMail = $zendEmail->smtpMailer($_SESSION["lw_ecard"]['absender'],$mail,$_SESSION["lw_ecard"]['betreff'], utf8_decode($this->buildMessage())); //die($this->buildMessage());
        }
        if($boolZendMail == true){
            return TRUE;
        }
    }
    
    private function saveECard()
    {
        $_SESSION["lw_ecard"]["hash"] = $this->dh->generateHash();
        $_SESSION["lw_ecard"][] = $this->pluginData["parameter"]["auto_delete"];
        return $this->dh->saveECard($_SESSION["lw_ecard"]);
    }
    
    private function showDoneMessage()
    {
        $template = $this->pluginData["parameter"]["sent_template"];
        $tpl = new lw_te($template);
        $tpl->reg("meldung", "Ihre ECard wurde versendet.");
        $tpl->reg("link", lw_page::getInstance()->getUrl());
        $this->output = $tpl->parse();
    }
    
    private function showErrorMessage()
    {
        $template = $this->pluginData["parameter"]["error_template"];
        $tpl = new lw_te($template);
        $tpl->reg("meldung", "Ihre ECard konnte nicht versendet werden.");
        $tpl->reg("link", lw_page::getInstance()->getUrl());
        $this->output = $tpl->parse();
    }
    
    private function showPreview()
    {
        $template = $this->pluginData["parameter"]["preview_template"];
        $tpl = new lw_te($template);
        
        $tpl->setIfVar("preview");
        $tpl->reg("edit_link", lw_page::getInstance()->getUrl());
        $tpl->reg("send_link", lw_page::getInstance()->getUrl(array("cmd"=>"sendECard")));
        $tpl->reg("valueName", $_SESSION["lw_ecard"]["name"]);
        $tpl->reg("valueNachricht", nl2br($_SESSION["lw_ecard"]["nachricht"]));
        
        
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
    
    function setDataArray()
    {
        $dataArray = array(
            "name" => $this->request->getAlnum("name"),
            "absender" => $this->request->getRaw("absender"),
            "empfaenger" => $this->request->getRaw("empfaenger"),
            "betreff" => $this->request->getAlnum("betreff"),
            "nachricht" => $this->request->getRaw("nachricht")
        );
        
        $_SESSION["lw_ecard"] = $dataArray;
    }
    
    function buildMessage()
    {
        $template = $this->pluginData["parameter"]["mail_template"];
        $tpl = new lw_te($template);        

        $tpl->reg('nachricht', utf8_encode($_SESSION["lw_ecard"]['nachricht']));
        $tpl->reg('link', lw_page::getInstance()->getUrl(array('hash' => $_SESSION["lw_ecard"]['hash'])));
        
        $msg = $tpl->parse();
        
        return $msg;
    }
}
