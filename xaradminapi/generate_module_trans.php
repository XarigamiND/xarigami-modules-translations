<?php
/**
 * Generate module translations
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


/**
 * generate translations for the specified module
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
function translations_adminapi_generate_module_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($modid) && isset($locale));

    if (!($modinfo = xarModGetInfo($modid))) return;
    $modname = $modinfo['name'];
    $moddir = $modinfo['osdirectory'];

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

    $backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $ref_locale));
    if (!isset($backend)) return;
    if (!$backend->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) {
        $link = xarModURL('translations', 'admin', 'update_info', array('dntype' => 'module'));
        return xarTplModule('translations','user', 'errors', array('errortype' => 'missing_skels', 'link' => $link));
    }

    $gen = xarModAPIFunc('translations','admin','create_generator_instance',array('interface' => 'TranslationsGenerator', 'locale' => $locale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) return;

    $module_contexts_list[] = 'modules:'.$modname.'::common';

    $subnames = xarModAPIFunc('translations','admin','get_module_phpfiles',array('moddir'=>$moddir));
    foreach ($subnames as $subname) {
        $module_contexts_list[] = 'modules:'.$modname.'::'.$subname;
    }

    $dirnames = xarModAPIFunc('translations','admin','get_module_dirs',array('moddir'=>$moddir));
    foreach ($dirnames as $dirname) {
        if (!preg_match('!^templates!i', $dirname, $matches))
            $pattern = '/^([a-z0-9\-_]+)\.php$/i';
        else
            $pattern = '/^([a-z0-9\-_]+)\.xd$/i';
        $subnames = xarModAPIFunc('translations','admin','get_module_files',
                              array('moddir'=>"modules/$moddir/xar$dirname",'pattern'=>$pattern));
        foreach ($subnames as $subname) {
            $module_contexts_list[] = 'modules:'.$modname.':'.$dirname.':'.$subname;
        }
    }

    foreach ($module_contexts_list as $module_context) {
        list ($dntype1, $dnname1, $ctxtype1, $ctxname1) = explode(':',$module_context);
        $ctxType = 'modules:'.$ctxtype1;
        $ctxName = $ctxname1;

        if (!$backend->loadContext($ctxType, $ctxName)) return;
        if (!$gen->create($ctxType, $ctxName)) return;

        if ($ctxtype1 != '') $sName = $ctxtype1 . "::" . $ctxName;
        else $sName = $ctxName;

        $statistics[$sName] = array('entries'=>0, 'keyEntries'=>0);
        while (list($string, $translation) = $backend->enumTranslations()) {
            $statistics[$sName]['entries']++;
            $gen->addEntry($string, $translation);
        }
        while (list($key, $translation) = $backend->enumKeyTranslations()) {
            $statistics[$sName]['keyEntries']++;
            $gen->addKeyEntry($key, $translation);
        }
        $gen->close();
        $backend->clear();
    }

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];

    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}
?>
