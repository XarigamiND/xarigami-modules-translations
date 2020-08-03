<?php
/**
 * Generate skels information
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

function translations_admin_generate_skels_info()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $druidbar = translations_create_druidbar(GENSKELS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_SKELS, $dnType, $dnName, $extid);
    $tplData = array_merge($druidbar, $opbar);
    $tplData['dnType'] = $dnType;

    if ($dnType == XARMLS_DNTYPE_CORE) $dnTypeText = 'core';
    elseif ($dnType == XARMLS_DNTYPE_THEME) $dnTypeText = 'theme';
    elseif ($dnType == XARMLS_DNTYPE_MODULE) $dnTypeText = 'module';
    else $dnTypeText = '';
    $tplData['dnTypeText'] = $dnTypeText;
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;
    $tplData['confirmskelsgen'] = xarModGetVar('translations', 'confirmskelsgen');
    $tplData['authid']          = xarSecGenAuthKey();
    $tplData['redirecturl']     = xarModURL('translations', 'admin','generate_skels_result',
                                    array('dnType' => $dnType,
                                          'dnName' => $dnName,
                                          'extid'  => $extid,
                                          'authid' => $tplData['authid']));
    return $tplData;
}

?>