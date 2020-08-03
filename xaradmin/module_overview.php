<?php
/**
 * Module overview
 *
* @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 */

function translations_admin_module_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('extid', 'id', $modid)) return;

    if (!($tplData = xarModGetInfo($modid))) return;
    $tplData['dnType'] = XARMLS_DNTYPE_MODULE;
    $tplData['dnName'] = $tplData['name'];
    $tplData['modid'] = $modid;
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $druidbar = translations_create_druidbar(INFO, XARMLS_DNTYPE_MODULE, $tplData['name'], $modid);
    $opbar = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_MODULE, $tplData['name'], $modid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>