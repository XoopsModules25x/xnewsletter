<?php namespace XoopsModules\Xnewsletter;

use Xmf\Request;
use XoopsModules\Xnewsletter;
use XoopsModules\Xnewsletter\Common;

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------
}
