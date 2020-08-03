<?php
/**
 * Core overview page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_core_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    $tplData = translations_create_opbar(OVERVIEW, XARMLS_DNTYPE_CORE, 'xarigami', 0);
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $tplData['verNum'] = XARCORE_VERSION_NUM;
    $tplData['verId'] = XARCORE_VERSION_ID;
    $tplData['verSub'] = XARCORE_VERSION_SUB;
    $tplData['dnType'] = XARMLS_DNTYPE_CORE;
    $tplData['dnName'] = 'xarigami';
    return $tplData;
}

?>