<?php
/**
 * Translations page generation
 *
* @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_translate()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $opbar = translations_create_opbar(TRANSLATE, $dnType, $dnName, $extid);
    $trabar = translations_create_trabar($dnType, $dnName, $extid, '', '');
    $druidbar = translations_create_druidbar(TRAN, $dnType, $dnName, $extid);
    $tplData = array_merge($opbar, $trabar, $druidbar);
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $tplData['dnType'] = $dnType;
    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;

    return $tplData;
}

?>