<?php
/**
 * Generate translations result
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 */

function translations_admin_generate_trans_result()
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
        $res = xarModAPIFunc('translations','admin','generate_core_trans',$args);
        break;
        case XARMLS_DNTYPE_MODULE:
        $args['modid'] = $extid;
        $res = xarModAPIFunc('translations','admin','generate_module_trans',$args);
        break;
        case XARMLS_DNTYPE_THEME:
        $args['themeid'] = $extid;
        $res = xarModAPIFunc('translations','admin','generate_theme_trans',$args);
        break;
    }

    if (!isset($res)) return;
    $tplData = $res;

    if ($tplData == NULL) {
        xarResponseRedirect(xarModURL('translations', 'admin', 'generate_trans_info'));
    }

    $druidbar = translations_create_druidbar(GENTRANS, $dnType, $dnName, $extid);
    $opbar = translations_create_opbar(GEN_TRANS, $dnType, $dnName, $extid);
    $tplData = array_merge($tplData, $druidbar, $opbar);

    $tplData['dnType'] = $dnType;
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    if ($dnType == XARMLS_DNTYPE_CORE) $dnTypeText = 'core';
    elseif ($dnType == XARMLS_DNTYPE_THEME) $dnTypeText = 'theme';
    elseif ($dnType == XARMLS_DNTYPE_MODULE) $dnTypeText = 'module';
    else $dnTypeText = '';
    $tplData['dnTypeText'] = $dnTypeText;

    $tplData['dnName'] = $dnName;
    $tplData['extid'] = $extid;

    return $tplData;
}

?>