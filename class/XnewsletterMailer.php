<?php

namespace XoopsModules\Xnewsletter;

require_once XNEWSLETTER_ROOT_PATH . '/include/phpmailer/PHPMailerAutoload.php';

/**
 * Class XnewsletterMailer
 */
class XnewsletterMailer extends PHPMailer
{
    /**
     * @throws Exception
     * @throws phpmailerException
     * @return string
     */
    public function getMIMEHeader()
    {
        $this->preSend();

        return $this->MIMEHeader;
    }

    /**
     * @throws Exception
     * @throws phpmailerException
     * @return string
     */
    public function getMIMEBody()
    {
        $this->preSend();

        return $this->MIMEBody;
    }

    /**
     * @throws Exception
     * @throws phpmailerException
     * @return int
     */
    public function getSize()
    {
        $this->preSend();

        return mb_strlen($this->MIMEHeader . $this->MIMEBody);
    }
}
