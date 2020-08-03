<?php
/**
 * Start translation process
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 */


/**
 * Entry point for beginning a translation
 *
 * @access  public
 * @return  array template data
 */
function translations_admin_start()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) return xarResponseForbidden();

    if (xarConfigGetVar('Site.MLS.TranslationsBackend') == 'xml2php') {
        $locales = $GLOBALS['xarMLS_allowedLocales'];
        foreach ($locales as $locale) {
            $l = xarMLS__parseLocaleString($locale);
            if ($l['charset'] != 'utf-8') continue;
            $list[] = $locale;
        }
        $tplData['locales'] = $list;
    } else {
        $tplData['locales'] = $GLOBALS['xarMLS_allowedLocales'];
    }
    $tplData['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    $tplData['working_locale'] = translations_working_locale();
    $tplData['dnType'] = XARMLS_DNTYPE_CORE;

    return $tplData;
}

?>