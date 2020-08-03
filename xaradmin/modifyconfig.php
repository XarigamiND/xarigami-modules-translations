<?php
/**
 * Providing Configuration Data for template
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team 
 */


function translations_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'display', XARVAR_NOT_REQUIRED)) return;
    $data['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $data['translationsBackend'] = xarConfigGetVar('Site.MLS.TranslationsBackend');
    $data['releaseBackend'] = xarModGetVar('translations', 'release_backend_type');
    $data['showcontext'] = xarModGetVar('translations', 'showcontext');
    $data['maxreferences'] = xarModGetVar('translations', 'maxreferences');
    $data['maxcodelines'] = xarModGetVar('translations', 'maxcodelines');
    $data['confirmskelsgen'] = xarModGetVar('translations', 'confirmskelsgen');

    $data['authid'] = xarSecGenAuthKey();
    $data['updatelabel'] = xarML('Update Translations Configuration');

    return $data;

}

?>