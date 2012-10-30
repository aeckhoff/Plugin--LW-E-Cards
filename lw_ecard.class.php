<?php

class lw_ecard extends lw_plugin
{

    public function __construct()
    {
        parent::__construct();
    }
        
    public function buildPageOutput()
    {        
        
        $this->plugin = array("pluginname" => $this->getPluginName(), "oid" => $this->params['oid']);
        
        include_once(dirname(__FILE__).'/classes/lwECardDatahandler.php');
        $dh = new lwECardDatahandler();

        include_once(dirname(__FILE__).'/classes/lwECardCleaner.php');
        $cleaner = new lwECardCleaner($dh);
        $cleaner->clean();
        
        if ($this->request->getRaw('hash')) {
            include_once(dirname(__FILE__).'/classes/lwECardReceiver.php');
            $class = new lwECardReceiver($dh, $this->request->getRaw('hash'),$this->plugin,$this->repository);
        }
        else {
            if ($this->checkIp() == true) {
                include_once(dirname(__FILE__).'/classes/lwECardSender.php');
                $class = new lwECardSender($dh,$this->config,$this->plugin,$this->repository);
            } 
            else {
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
            if ($ip_substr == $allowedIp) {
                return TRUE;
            }
        }
    }
    
    function existsBackendSettings()
    {
         $plugindata = $this->repository->plugins()->loadPluginData($this->plugin["pluginname"], $this->plugin["oid"]);
         if ($plugindata["parameter"]["formular_template"] == "") {
             $parameter["formular_template"] = file_get_contents(dirname(__FILE__)."/templates/email_eingabeform.tpl.html",true);
         }
         else {
             $parameter["formular_template"] = $plugindata["parameter"]["formular_template"];
         }
         
         if ($plugindata["parameter"]["preview_template"] == "")
         {
             $parameter["preview_template"] = file_get_contents(dirname(__FILE__)."/templates/ecard.tpl.html",true);
         } 
         else {
             $parameter["preview_template"] = $plugindata["parameter"]["preview_template"];
         }
         
         if ($plugindata["parameter"]["sent_template"] == "") {
             $parameter["sent_template"] = file_get_contents(dirname(__FILE__)."/templates/done.tpl.html",true);
         }
         else{
             $parameter["sent_template"] = $plugindata["parameter"]["sent_template"];
         }
         
         if ($plugindata["parameter"]["error_template"] == "") {
             $parameter["error_template"] = file_get_contents(dirname(__FILE__)."/templates/error.tpl.html",true);
         }
         else {
             $parameter["error_template"] = $plugindata["parameter"]["error_template"];
         }
         
         if ($plugindata["parameter"]["mail_template"] == "") {
             $parameter["mail_template"] = file_get_contents(dirname(__FILE__)."/templates/email.tpl.html",true);
         }
         else {
             $parameter["mail_template"] = $plugindata["parameter"]["mail_template"];
         }

         if ($plugindata["parameter"]["auto_delete"] == "") {
             $parameter["auto_delete"] = 30;
         }
         else {
             $parameter["auto_delete"] = $plugindata["parameter"]["auto_delete"];
         }        
         $content = false;
         $this->repository->plugins()->savePluginData($this->plugin["pluginname"], $this->plugin['oid'], $parameter, $content);
    }
    
    function getOutput() 
    {
        $this->init();
        require_once dirname(__FILE__).'/classes/backend.php';
        $backend = new backend($this->config,$this->request,$this->repository,$this->getPluginName(),$this->getOid());
        if ($this->request->getAlnum("pcmd") == "save"){
            $backend->backend_save();
        }
        return $backend->backend_view();
    }
    
    function init()
    {
       $this->plugin = array("pluginname" =>$this->getPluginName(), "oid" => $this->getOid());
       $this->existsBackendSettings();
    }
    
}
