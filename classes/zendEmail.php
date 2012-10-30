<?php
class zendEmail
{
    function __construct($config) {
        $this->config = $config;
    }
    /**
     *send e-mail 
     * @param string $absender
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return boolean
     * @throws Exception 
     */
    function smtpMailer($absender,$to, $subject, $message) 
    {
        require_once($this->config['path']['framework'] . "Zend/Mail.php");
        require_once($this->config['path']['framework'] . "Zend/Mail/Transport/Smtp.php");
        $mconfig = array();

        $from = $this->config['ecard']['from']; // absender gmx
        $mconfig["auth"] = "login";
        if ($this->config['ecard']['ssl']) {
            //$mconfig["ssl"] = $this->configuration['ecard']['ssl'];
        }
        $mconfig["username"] = $this->config['ecard']['username']; // username gmx
        $mconfig["password"] = $this->config['ecard']['password']; // pw gmx
        $mconfig["port"] = $this->config['ecard']['port'];
        $server = $this->config['ecard']['server']; // smtp.gmx.net

        $subject = trim($subject);
        $transport = new Zend_Mail_Transport_Smtp($server, $mconfig);
        $mail = new Zend_Mail();
        try {
            $mail->setBodyText($message, null, Zend_Mime::ENCODING_BASE64);
            $mail->setFrom($from, $from);
            $mail->setReplyTo($absender, $name=null);
            $mail->addTo($to, $to);
            $mail->setSubject($subject);
            $mail->send($transport);
            return true;
        } 
        catch (Zend_Mail_Exception $e) {
            die ($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
