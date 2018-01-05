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
    public function getMIMEHeader()
    {
        $this->preSend();

        return $this->MIMEHeader;
    }

    /**
     * @return string
     * @throws Exception
     * @throws phpmailerException
     */
    public function getMIMEBody()
    {
        $this->preSend();

        return $this->MIMEBody;
    }

    /**
     * @return int
     * @throws Exception
     * @throws phpmailerException
     */
    public function getSize()
    {
        $this->preSend();

        return strlen($this->MIMEHeader . $this->MIMEBody);
    }
}
