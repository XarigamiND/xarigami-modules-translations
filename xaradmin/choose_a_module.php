<?php
/**
 * Choose a module page generation
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_choose_a_module()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    $installed = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_INSTALLED)));
    if (!isset($installed)) return;
    $uninstalled = xarModAPIFunc('modules', 'admin', 'getlist', array('filter' => array('State' => XARMOD_STATE_UNINITIALISED)));
    if (!isset($uninstalled)) return;

    $modlist1 = array();
    foreach($uninstalled as $term) $modlist1[$term['name']] = $term;
    $modlist2 = array();
    foreach($installed as $term) $modlist2[$term['name']] = $term;
    $modlist = array_merge($modlist1,$modlist2);
    ksort($modlist);

    $tplData = translations_create_druidbar(CHOOSE, XARMLS_DNTYPE_MODULE, '', 0);
    $tplData['modlist'] = $modlist;
    $tplData['dnType'] = XARMLS_DNTYPE_MODULE;
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    return $tplData;
}

?>