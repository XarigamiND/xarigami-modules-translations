<?php
/**
 * Theme overview
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

function translations_admin_theme_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('extid', 'id', $themeid)) return;

    if (!($tplData = xarThemeGetInfo($themeid))) return;
    $tplData['dnType'] = XARMLS_DNTYPE_THEME;
    $tplData['dnName'] = $tplData['directory'];
    $tplData['themeid'] = $themeid;
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $druidbar = translations_create_druidbar(INFO, XARMLS_DNTYPE_THEME, $tplData['directory'], $themeid);
    $opbar = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_THEME, $tplData['directory'], $themeid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>