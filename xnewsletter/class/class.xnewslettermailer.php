<?php
require XNEWSLETTER_ROOT_PATH . '/include/phpmailer/PHPMailerAutoload.php';

/**
 * Class XnewsletterMailer
 */
class XnewsletterMailer extends PHPMailer
{
    /**
     * @return string
     * @throws Exception
     * @throws phpmailerException
     */
    public function GetMIMEHeader()
    {
        $this->PreSend();

        return $this->MIMEHeader;
    }

    /**
     * @return string
     * @throws Exception
     * @throws phpmailerException
     */
    public function GetMIMEBody()
    {
        $this->PreSend();

        return $this->MIMEBody;
    }

    /**
     * @return int
     * @throws Exception
     * @throws phpmailerException
     */
    public function GetSize()
    {
        $this->PreSend();

        return strlen($this->MIMEHeader . $this->MIMEBody);
    }
}
