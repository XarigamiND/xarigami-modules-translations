<?php
/**
 * Choose a theme page generation
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

function translations_admin_choose_a_theme()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!($themelist = xarModAPIFunc('themes','admin','getthemelist',array('filter' => array('State' => XARTHEME_STATE_ANY))))) return;

    $tplData = translations_create_druidbar(CHOOSE, XARMLS_DNTYPE_THEME, '', 0);
    $tplData['themelist'] = $themelist;
    $tplData['dnType'] = XARMLS_DNTYPE_THEME;
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    return $tplData;
}

?>