<?php
/**
 * Update the session info with how we are going to translate
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


function translations_admin_update_info()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('dntype', 'regexp:/^(core|module|theme)$/', $type)) return;

    switch ($type) {
        case 'core':
        $url = xarModURL('translations', 'admin', 'core_overview');
        break;
        case 'module':
        $url = xarModURL('translations', 'admin', 'choose_a_module');
        break;
        case 'theme':
        $url = xarModURL('translations', 'admin', 'choose_a_theme');
        break;
    }
    xarResponseRedirect($url);
}

?>