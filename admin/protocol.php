<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
 * ****************************************************************************
 *  XNEWSLETTER - MODULE FOR XOOPS
 *  Copyright (c) 2007 - 2012
 *  Goffy ( wedega.com )
 *
 *  You may not change or alter any portion of this comment or credits
 *  of supporting developers from this source code or any supporting
 *  source code which is considered copyrighted (c) material of the
 *  original comment or credit authors.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  ---------------------------------------------------------------------------
 *  @copyright  Goffy ( wedega.com )
 *  @license    GPL 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op = XoopsRequest::getString('op', 'list');

$protocolAdmin = new ModuleAdmin();
$letterAdmin = new ModuleAdmin();

switch ($op) {
    case 'list':
        echo $letterAdmin->addNavigation($currentFile);
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $letterCriteria = new CriteriaCompo();
        $letterCriteria->setSort('letter_id');
        $letterCriteria->setOrder('DESC');
        $lettersCount = $xnewsletter->getHandler('letter')->getCount();
        $start = XoopsRequest::getInt('start', 0);
        $letterCriteria->setStart($start);
        $letterCriteria->setLimit($limit);
        $letterObjs = $xnewsletter->getHandler('letter')->getAll($letterCriteria);
        if ($lettersCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
          $pagenav = new XoopsPageNav($lettersCount, $limit, $start, 'start', 'op=list');
          $pagenav = $pagenav->renderNav(4);
        } else {
          $pagenav = '';
        }

        // View Table
        if ($lettersCount > 0) {
            echo "
                <table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_LETTER_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_LETTER_TITLE . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_LAST_STATUS . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_CREATED . "</th>
                        <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . '</th>
                    </tr>';

            $class = 'odd';

            //first show misc protocol items
            echo "<tr class='" . $class . "'>";
            $class = ($class === 'even') ? 'odd' : 'even';
            echo "<td class='center'> - </td>";
            echo "<td class='center'>" . _AM_XNEWSLETTER_PROTOCOL_MISC . '</td>';

            $protocolCriteria = new CriteriaCompo();
            $protocolCriteria->add(new Criteria('protocol_letter_id', '0'));
            $protocolCriteria->setSort('protocol_id');
            $protocolCriteria->setOrder('DESC');
            $protocolsCount = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);
            $protocolCriteria->setLimit(2);
            $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
            $protocol_status = '';
            $protocol_created = '';
            $p = 0;
            foreach ($protocolObjs as $protocol_id => $protocolObj) {
                ++$p;
                if (count($protocolObjs)>1) $protocol_status .="($p) ";
                $protocol_status .= $protocolObj->getVar('protocol_status') . '<br/>';
                $protocol_created .= formatTimestamp($protocolObj->getVar('protocol_created'), 'M') . '<br/>';
            }
            if ($protocolsCount > 2) $protocol_status .= '...';
            echo "
                    <td class='center'>
                        <a href='?op=list_letter&letter_id=0'>" . $protocol_status . "</a>
                    </td>
                    <td class='center'>" . $protocol_created . "</td>
                    <td class='center width5'>
                        <a href='?op=list_letter&letter_id=0'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_details.png alt='" . _AM_XNEWSLETTER_DETAILS . "' title='" . _AM_XNEWSLETTER_DETAILS . "' /></a>
                    </td>
                </tr>";

            foreach (array_keys($letterObjs) as $i) {
                $protocolCriteria = new CriteriaCompo();
                $protocolCriteria->add(new Criteria('protocol_letter_id', $letterObjs[$i]->getVar('letter_id')));
                $protocolCriteria->setSort('protocol_id');
                $protocolCriteria->setOrder('DESC');
                $protocolsCount = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);
                if ($protocolsCount > 0) {
                    $protocolCriteria->setLimit(2);
                    $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
                    $protocol_status = '';
                    $protocol_created = '';

                    echo "<tr class='" . $class. "'>";
                    $class = ($class === 'even') ? 'odd' : 'even';
                    echo "<td class='center'>" . $i . '</td>';
                    echo "<td class='center'>" . $letterObjs[$i]->getVar('letter_title') . '</td>';

                    $p = 0;
                    foreach ($protocolObjs as $protocol) {
                        ++$p;
                        if (count($protocolObjs)>1) $protocol_status .="($p) ";
                        $protocol_status .= $protocol->getVar('protocol_status') . '<br/>';
                        $protocol_created .= formatTimestamp($protocol->getVar('protocol_created'), 'M') . '<br/>';
                    }
                    if ($protocolsCount > 2) $protocol_status .= '...';
                    echo "
                            <td class='center'>
                                <a href='?op=list_letter&letter_id=" . $i . "'>" . $protocol_status . "</a>
                            </td>
                            <td class='center'>" . $protocol_created . "</td>
                            <td class='center width5'>
                                <a href='?op=list_letter&letter_id=" . $i . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_details.png alt='" . _AM_XNEWSLETTER_DETAILS . "' title='" . _AM_XNEWSLETTER_DETAILS . "' /></a>
                            </td>
                        </tr>";
                }
            }
            echo '</table><br /><br />';
            echo "<br /><div class='center'>" . $pagenav . '</div><br />';
        } else {
            echo "
                <table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_LETTER_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_LETTER_TITLE . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_LAST_STATUS . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_CREATED . "</th>
                        <th class='center width5'>"._AM_XNEWSLETTER_FORMACTION . '</th>
                    </tr>
                </table><br /><br />';
        }
    break;
    case 'list_letter':
        $letter_id = isset($_REQUEST['letter_id']) ? $_REQUEST['letter_id'] :'0';
        echo $protocolAdmin->addNavigation($currentFile);
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, '?op=list', 'list');
        //
        if ($letter_id > '0') $protocolAdmin->addItemButton(_AM_XNEWSLETTER_LETTER_DELETE_ALL, '?op=delete_protocol_list&letter_id=' . $letter_id, 'delete');
        echo $protocolAdmin->renderButton();
        $limit = $xnewsletter->getConfig('adminperpage');

        $protocolCriteria = new CriteriaCompo();
        $protocolCriteria->add(new Criteria('protocol_letter_id', $letter_id));
        $protocolCriteria->setSort('protocol_id');
        $protocolCriteria->setOrder('DESC');
        $protocolsCount = $xnewsletter->getHandler('protocol')->getCount($protocolCriteria);
        $start = XoopsRequest::getInt('start', 0);
        $protocolCriteria->setStart($start);
        $protocolCriteria->setLimit($limit);
        $protocolObjs = $xnewsletter->getHandler('protocol')->getAll($protocolCriteria);
        if ($protocolsCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new XoopsPageNav($protocolsCount, $limit, $start, 'start', 'op=list_letter&letter_id=' . $letter_id);
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($protocolsCount > 0) {
            $letterObj = $xnewsletter->getHandler('letter')->get($letter_id);
            echo "<h2 style='text-align:center'>" . $letterObj->getVar('letter_title') . '</h2>';
            echo "
                <table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_PROTOCOL_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_STATUS . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_SUCCESS . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_SUBMITTER . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_CREATED . "</th>
                        <th class='center width10'>" . _AM_XNEWSLETTER_FORMACTION . '</th>
                    </tr>';

            $class = 'odd';
            foreach ($protocolObjs as $protocol_id => $protocolObj) {
                echo "<tr class='" . $class . "'>";
                $class = ($class === 'even') ? 'odd' : 'even';
                echo "<td class='center'>" . $protocol_id . '</td>';
                $subscrObj = $xnewsletter->getHandler('subscr')->get($protocolObj->getVar('protocol_subscriber_id'));
                $subscriber = ($subscrObj) ? $subscrObj->getVar('subscr_email') : _AM_XNEWSLETTER_PROTOCOL_NO_SUBSCREMAIL;
                if ($subscriber == '') $subscriber = '-';
                $success = ($protocolObj->getVar('protocol_success') == 1) ? XNEWSLETTER_IMG_OK : XNEWSLETTER_IMG_FAILED;
                echo "<td class='center'>" . $subscriber . '</td>';
                echo "<td class='center'>" . $protocolObj->getVar('protocol_status') . '</td>';
                echo "<td class='center'>" . $success . '</td>';
                echo "<td class='center'>" . XoopsUser::getUnameFromId($protocolObj->getVar('protocol_submitter'), 'S') . '</td>';
                echo "<td class='center'>" . formatTimestamp($protocolObj->getVar('protocol_created'), 'L') . '</td>';

                echo "
                    <td class='center width5'>
                        <a href='?op=delete_protocol&protocol_id=" . $protocol_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>
                    </td>";
                echo '</tr>';
            }
            echo '</table><br /><br />';
            echo "<br /><div class='center'>" . $pagenav . '</div><br />';
        } else {
            echo "
                <table class='outer width100' cellspacing='1'>
                    <tr>
                        <th class='center width2'>" . _AM_XNEWSLETTER_PROTOCOL_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_LETTER_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_SUBSCRIBER_ID . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_STATUS . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_SUBMITTER . "</th>
                        <th class='center'>" . _AM_XNEWSLETTER_PROTOCOL_CREATED . "</th>
                        <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . '</th>
                    </tr>
                </table>
                <br />
                <br />';
        }
    break;

    case 'new_protocol':
        echo $protocolAdmin->addNavigation($currentFile);
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, '?op=list', 'list');
        echo $protocolAdmin->renderButton();
        //
        $protocolObj = $xnewsletter->getHandler('protocol')->create();
        $form = $protocolObj->getForm();
        $form->display();
    break;

    case 'save_protocol':
        if ( !$GLOBALS['xoopsSecurity']->check() ) {
           redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (isset($_REQUEST['protocol_id'])) {
           $protocolObj = $xnewsletter->getHandler('protocol')->get($_REQUEST['protocol_id']);
        } else {
           $protocolObj = $xnewsletter->getHandler('protocol')->create();
        }

        $protocolObj->setVar('protocol_letter_id', $_REQUEST['protocol_letter_id']);
        $protocolObj->setVar('protocol_subscriber_id', $_REQUEST['protocol_subscriber_id']);
        $protocolObj->setVar('protocol_status', $_REQUEST['protocol_status']);
        $protocolObj->setVar('protocol_success', $_REQUEST['protocol_success']);
        $protocolObj->setVar('protocol_submitter', $_REQUEST['protocol_submitter']);
        $protocolObj->setVar('protocol_created', strtotime($_REQUEST['protocol_created']));

        if ($xnewsletter->getHandler('protocol')->insert($protocolObj)) {
            redirect_header('?op=list', 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $protocolObj->getHtmlErrors();
        $form = $protocolObj->getForm();
        $form->display();
    break;

    case 'edit_protocol':
        echo $protocolAdmin->addNavigation($currentFile);
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_NEWPROTOCOL, '?op=new_protocol', 'add');
        $protocolAdmin->addItemButton(_AM_XNEWSLETTER_PROTOCOLLIST, '?op=list', 'list');
        echo $protocolAdmin->renderButton();
        //
        $protocolObj = $xnewsletter->getHandler('protocol')->get($_REQUEST['protocol_id']);
        $form = $protocolObj->getForm();
        $form->display();
    break;

    case 'delete_protocol':
        $protocolObj = $xnewsletter->getHandler('protocol')->get($_REQUEST['protocol_id']);
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if ( !$GLOBALS['xoopsSecurity']->check() ) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('protocol')->delete($protocolObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $protocolObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => 1, 'protocol_id' => $_REQUEST['protocol_id'], 'op' => 'delete_protocol'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $protocolObj->getVar('protocol_id')));
        }
    break;

    case 'delete_protocol_list':
        $letter_id = isset($_REQUEST['letter_id']) ? $_REQUEST['letter_id'] : 0;
        if ($letter_id > 0) {
            $letterObj = $xnewsletter->getHandler('letter')->get($_REQUEST['letter_id']);
            if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
                if ( !$GLOBALS['xoopsSecurity']->check() ) {
                    redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                $sql = 'DELETE FROM `' . $xoopsDB->prefix('xnewsletter_protocol') . "` WHERE `protocol_letter_id`=$letter_id;";
                $result = $xoopsDB->query($sql);
                if ($result) {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
                } else {
                    redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELNOTOK);
                }
            } else {
                xoops_confirm(['ok' => 1, 'letter_id' => $letter_id, 'op' => 'delete_protocol_list'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL_LIST, $letterObj->getVar('letter_title')));
            }
        }
    break;
}
include_once __DIR__ . '/admin_footer.php';
