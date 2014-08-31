<?php
require XNEWSLETTER_ROOT_PATH . "/include/phpmailer/PHPMailerAutoload.php";

class XnewsletterMailer extends PHPMailer {
    public function GetMIMEHeader() {
        $this->PreSend();
        return $this->MIMEHeader;
    }
    public function GetMIMEBody() {
        $this->PreSend();
        return $this->MIMEBody;
    }
    public function GetSize() {
      $this->PreSend();
      return strlen($this->MIMEHeader . $this->MIMEBody);
    }
}
