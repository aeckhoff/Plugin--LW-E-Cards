<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 2012 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

class backend extends lw_object
{
    function __construct($config,$request,$repository,$pluginname,$oid) 
    {
       $this->config = $config;
       $this->request = $request;
       $this->repository = $repository;
       $this->pluginname = $pluginname;
       $this->oid = $oid;
    }
    
    function backend_save()
    {
        $parameter['formular_template'] = $this->request->getRaw("formular_template");
        $parameter['preview_template'] = $this->request->getRaw("preview_template");
        $parameter['sent_template'] = $this->request->getRaw("sent_template");
        $parameter['error_template'] = $this->request->getRaw("error_template");
        $parameter['mail_template'] = $this->request->getRaw("mail_template");
        $parameter['auto_delete'] = $this->request->getAlnum("auto_delete");
        
        $content = false;
        $this->repository->plugins()->savePluginData($this->pluginname, $this->oid, $parameter, $content);
        $this->pageReload($this->buildURL(false, array("pcmd")));
    }
    
    function backend_view()
    {
        $data = $this->repository->plugins()->loadPluginData($this->pluginname, $this->oid);
        #echo $this->pluginname."------------------".  $this->oid;die();
        #print_r($data);die();
        $form = $this->_buildAdminForm();
        foreach($data['parameter'] as $key=>$value) {
            $data['parameter'][$key] = htmlentities($value);
        }
        $form->setData($data['parameter']);
        $tpl = new lw_te(file_get_contents(dirname(__FILE__) . '/../templates/backendform.tpl.html'));
        $tpl->reg("codemirrorJS", $this->config["url"]["media"]."js/codemirror-2.34/lib/codemirror.js");
        $tpl->reg("codemirrorCSS", $this->config["url"]["media"]."js/codemirror-2.34/lib/codemirror.css");
        $tpl->reg("codemirrorMODE1", $this->config["url"]["media"]."js/codemirror-2.34/mode/xml/xml.js");
        $tpl->reg("codemirrorMODE2", $this->config["url"]["media"]."js/codemirror-2.34/mode/javascript/javascript.js");
        $tpl->reg("codemirrorMODE3", $this->config["url"]["media"]."js/codemirror-2.34/mode/css/css.js");
        $tpl->reg("codemirrorMODE4", $this->config["url"]["media"]."js/codemirror-2.34/mode/htmlmixed/htmlmixed.js");
        $tpl->reg("codemirrorTHEMEcss", $this->config["url"]["media"]."js/codemirror-2.34/theme/lesser-dark.css");
        $tpl->reg("jqUI", $this->config["url"]["media"]."jquery/ui/jquery-ui-1.8.7.custom.min.js");
        $tpl->reg("jqUIcss", $this->config["url"]["media"]."jquery/ui/css/smoothness/jquery-ui-1.8.7.custom.css");
        #$tpl->reg("test", "HAAAAAAALLLO");
        $tpl->reg("form", $form->render());
        return $tpl->parse();
    }
    
    function _buildAdminForm() 
    {
        $form = new lw_fe();
        $form->setRenderer()
                ->setID('lw_listtool')
                ->setIntroduction('Basisdaten der Liste')
                ->setDefaultErrorMessage('Es sind Fehler aufgetreten!')
                ->setAction($this->buildUrl(array("pcmd"=>"save")));
        
        $form->createElement("textarea")
                ->setName('formular_template')
                ->setID('lw_formular_template');
                #->setLabel('Formular Template :');
        
        $form->createElement("textarea")
                ->setName('preview_template')
                ->setID('lw_preview_template');
                #->setLabel('Preview / ECard Template :');
        
        $form->createElement("textarea")
                ->setName('sent_template')
                ->setID('lw_sent_template');
                #->setLabel('Sent Template :');
        
        $form->createElement("textarea")
                ->setName('error_template')
                ->setID('lw_error_template');
                #->setLabel('Error Template :');
        
        $form->createElement("textarea")
                ->setName('mail_template')
                ->setID('lw_mail_template');
                #->setLabel('Mail Template :');
        
        $form->createElement("textfield")
                ->setName('auto_delete')
                ->setID('lw_auto_delete');
                #->setLabel('Auto. Löschung der ECard (in Tagen) :');
        
        $form->createElement('button')
                ->setTarget('admin.php')
                ->setValue('abbrechen');

        $form->createElement('submit')
                ->setValue('speichern');

        return $form;
    }
}