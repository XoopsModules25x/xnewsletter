<?php

namespace XoopsModules\Xnewsletter;

require_once dirname(__DIR__) . '/include/phpmailer/PHPMailerAutoload.php';

/**
 * Class XnewsletterMailer
 */
class XnewsletterMailer extends \PHPMailer
{
    /**
     * @return string
     * @throws \phpmailerException
     */
    public function getMIMEHeader()
    {
        $this->preSend();

        return $this->MIMEHeader;
    }

    /**
     * @return string
     * @throws \phpmailerException
     */
    public function getMIMEBody()
    {
        $this->preSend();

        return $this->MIMEBody;
    }

    /**
     * @return int
     * @throws \phpmailerException
     */
    public function getSize()
    {
        $this->preSend();

        return mb_strlen($this->MIMEHeader . $this->MIMEBody);
    }
}
