<?php
/**
 * Release result page generation
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_release_result()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;

    $locale = translations_release_locale();
    $args = array('locale'=>$locale);
    switch ($dnType) {
        case XARMLS_DNTYPE_CORE:
        $res = xarModAPIFunc('translations','admin','release_core_trans',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = $extid;
        $res = xarModAPIFunc('translations','admin','release_module_trans',$args);
        break;
        case XARMLS_DNTYPE_THEME:
        $args['themeid'] = $extid;
        $res = xarModAPIFunc('translations','admin','release_theme_trans',$args);
        break;
    }
    if (!isset($res)) return;

    $filename = $res;
    if ($filename == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'release_info'));
    }

    $tplData['url'] = xarServerGetBaseURL().xarCoreGetVarDirPath().'/cache/'.$filename;

    $druidbar = translations_create_druidbar(REL, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(RELEASE, $dnType, $dnName, $extid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    return $tplData;
}

?>