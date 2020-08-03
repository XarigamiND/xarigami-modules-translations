<?php
/**
 * Generate core translation
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Caninin
 * @author Marcel van der Boom <marcel@xaraya.com>
 */


function translations_adminapi_generate_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert('isset($locale)');

    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) throw new ForbiddenOperationException();

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    if (xarConfigGetVar('Site.MLS.TranslationsBackend') == 'xml2php') {
        $l = xarLocaleGetInfo($locale);
        if ($l['charset'] == 'utf-8') {
            $ref_locale = $locale;
        } else {
            $l['charset'] = 'utf-8';
            $ref_locale = xarLocaleGetString($l);
        }
    } else {
        $ref_locale = $locale;
    }

    // Load core translations
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;

    if (!$backend->bindDomain(XARMLS_DNTYPE_CORE, 'xarigami')) {
        $link = xarModURL('translations', 'admin', 'update_info', array('dntype' => 'core'));
        return xarTplModule('translations','user', 'errors', array('errortype' => 'missing_locale_skels', 'link' => $link, 'locale' => $ref_locale));
    }
    if (!$backend->loadContext('core:', 'core')) return;

    $gen = xarModAPIFunc('translations','admin','create_generator_instance',array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_CORE, 'xarigami')) return;
    if (!$gen->create('core:', 'core')) return;

    $statistics['core'] = array('entries'=>0, 'keyEntries'=>0);

    while (list($string, $translation) = $backend->enumTranslations()) {
        $statistics['core']['entries']++;
        $gen->addEntry($string, $translation);
    }

    while (list($key, $translation) = $backend->enumKeyTranslations()) {
        $statistics['core']['keyEntries']++;
        $gen->addKeyEntry($key, $translation);
    }

    $gen->close();

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

?>
