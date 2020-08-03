<?php
/**
 * Delete fuzzy file result
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

function translations_admin_delete_fuzzy_result()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $locale = translations_working_locale();
    $backend = xarModAPIFunc('translations', 'admin', 'create_backend_instance',
                             array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($backend)) return;

    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
            $ctxType = 'core:';
            break;
        case XARMLS_DNTYPE_MODULE:
            $ctxType = 'modules:';
            break;
        case XARMLS_DNTYPE_THEME:
            $ctxType = 'themes:';
            break;
    }
    $ctxName = 'fuzzy';

    if ($backend->bindDomain($dnType,$dnName)) {
        $fileName = $backend->findContext($ctxType, $ctxName);
        if (file_exists($fileName)) {
            unlink($fileName);
        }
    }

    xarResponseRedirect(xarModURL('translations', 'admin', 'translate',
        array('dnType' => $dnType,
              'dnName' => $dnName,
              'extid' => $extid)));

    return true;
}

?>