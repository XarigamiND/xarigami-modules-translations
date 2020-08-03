<?php
/**
 * Update configuration for translations module
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Update configuration
 *
 * @param string
 * @return void?
 * @todo decide whether a site admin can set allowed locales for users
 * @todo add decent validation
 */
function translations_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;

    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;
    switch ($data['tab']) {
        case 'display':
            if (!xarVarFetch('showcontext','checkbox',$showContext,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('maxreferences','int',$maxReferences,0,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('maxcodelines','int',$maxCodeLines,5,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('confirmskelsgen','checkbox',$confirmskelsgen,true,XARVAR_NOT_REQUIRED)) return;

            xarModSetVar('translations', 'showcontext',$showContext);
            xarModSetVar('translations', 'maxreferences',$maxReferences);
            xarModSetVar('translations', 'maxcodelines',$maxCodeLines);
            xarModSetVar('translations', 'confirmskelsgen', $confirmskelsgen);

            break;
        case 'release':
            if (!xarVarFetch('releasebackend','str:1:',$releaseBackend)) return;

            // xarModSetVar('translations', 'release_backend_type', $releaseBackend);

            break;
    }

    // Call updateconfig hooks
    xarModCallHooks('module','updateconfig','translations', array('module' => 'translations'));
    $msg = xarML('Configuration for successfully updated.');
     xarTplSetMessage($msg,'status');
    xarResponseRedirect(xarModURL('translations', 'admin', 'modifyconfig',array('tab' => $data['tab'])));

    return true;
}

?>