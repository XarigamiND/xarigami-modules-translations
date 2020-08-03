<?php
/**
 * Update working locale
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

function translations_admin_update_working_locale()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('locale', 'str:1:', $locale)) return;
    translations_working_locale($locale);
    translations_release_locale($locale);
        $msg = xarML('Working locale updated.');
     xarTplSetMessage($msg,'status');
    xarResponseRedirect(xarModURL('translations', 'admin','start'));
}

?>