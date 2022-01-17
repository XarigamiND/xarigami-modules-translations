<?php
/**
 * Generate skeletons for a theme
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
/**
 * generate translations XML skels for a specified theme
 * @param $args['themeid'] theme registry identifier
 * @param $args['locale'] locale name
 * @returns array
 * @return statistics on generation process
 */
function translations_adminapi_generate_theme_skels($args)
{
    // To suppress an error in safe mode we supply a @ here, no other way i think
    @set_time_limit(0);

    // Get arguments
    extract($args);

    // Argument check
    assert('isset($themeid) && isset($locale)');

    if (!$modinfo = xarModGetInfo($themeid,'theme')) return;
    $themename = $modinfo['name'];
    $themedir = $modinfo['osdirectory'];
     $sitethemedir = xarConfigGetVar('Site.BL.ThemesDirectory');
    if (!xarSecurityCheck('AdminTranslations')) throw new ForbiddenOperationException();

    // {ML_dont_parse 'modules/translations/class/TPLParser.php'}
    sys::import('modules.translations.class.TPLParser');

    $time = explode(' ', microtime());
    $startTime = $time[1] + $time[0];

    // Load core translations
    $core_backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($core_backend)) return;
    if (!$core_backend->bindDomain(XARMLS_DNTYPE_CORE, 'xarigami')) {
        $link = xarModURL('translations', 'admin', 'update_info', array('dntype' => 'core'));
        return xarTplModule('translations','user', 'errors', array('errortype' => 'missing_core_skels', 'link' => $link, 'type' => xarML('theme'), 'name' => $themename));

    }
    if (!$core_backend->loadContext('core:', 'core')) return;


    // Parse files
    $transEntriesCollection = array();
    $transKeyEntriesCollection = array();

    $dirnames = xarModAPIFunc('translations','admin','get_theme_dirs',array('themedir'=>$themedir));

    foreach ($dirnames as $dirname) {
        ${$dirname . "names"} = array();
        $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
        $xtype = 'xt';
        $subnames = xarModAPIFunc('translations','admin','get_theme_files',
                         array('themedir'=>"$sitethemedir/$themedir/$dirname",'pattern'=>$pattern));

        foreach ($subnames as $subname) {
            $theme_contexts_list[] = 'themes:'.$themename.':'.$dirname.':'.$subname;
            $parser = new TPLParser();
            $parser->parse("$sitethemedir/$themedir/$dirname/$subname.$xtype");
            ${$dirname . "names"}[] = $subname;

            $transEntriesCollection[$dirname.'::'.$subname] = $parser->getTransEntries();
            $transKeyEntriesCollection[$dirname.'::'.$subname] = $parser->getTransKeyEntries();
        }
    }

    $transEntriesCollection = translations_gather_common_entries($transEntriesCollection);
    $transKeyEntriesCollection = translations_gather_common_entries($transKeyEntriesCollection);

    $subnames[] = 'common';
    // Load previously made translations
    $backend = xarModAPIFunc('translations','admin','create_backend_instance',array('interface' => 'ReferencesBackend', 'locale' => $locale));
    if (!isset($backend)) return;

    if ($backend->bindDomain(XARMLS_DNTYPE_THEME, $themedir)) {
        if ($backend->hasContext('themes:','common')){
            if (!$backend->loadContext('themes:','common')) return;
        }
        foreach ($theme_contexts_list as $theme_context) {
            list ($dntype1, $dnname1, $ctxtype1, $ctxname1) = explode(':',$theme_context);
            if ($backend->hasContext('themes:'.$ctxtype1,$ctxname1)){
                if (!$backend->loadContext('themes:'.$ctxtype1,$ctxname1)) return;
            }
        }
    }

    // Load KEYS
    $filename = "themes/$themedir/KEYS";
    $KEYS = array();
    if (file_exists($filename)) {
        $lines = file($filename);
        foreach ($lines as $line) {
            if ($line[0] == '#') continue;
            list($key, $value) = explode('=', $line);
            $key = trim($key);
            $value = trim($value);
            $KEYS[$key] = $value;
        }
    }

    // Create skels
    $subnames = array_keys($transEntriesCollection);
    if (xarConfigGetVar('Site.MLS.TranslationsBackend') == 'xml2php') {
        if (!$parsedLocale = xarMLS__parseLocaleString($locale)) return false;
        $genLocale = $parsedLocale['lang'].'_'.$parsedLocale['country'].'.utf-8';
    } else {
        $genLocale = $locale;
    }
    //create the relevant directory and theme/module subdirectory
    $gen = xarModAPIFunc('translations','admin','create_generator_instance',array('interface' => 'ReferencesGenerator', 'locale' => $genLocale));
    if (!isset($gen)) return;
    if (!$gen->bindDomain(XARMLS_DNTYPE_THEME, $themedir)) return;
    foreach ($subnames as $subname) {
        if (preg_match('/(.*)::(.*)/', $subname, $matches)) {
           list ($ctxtype1, $ctxname1) = explode('::',$subname);
        } else {
            $ctxtype1 = '';
            $ctxname1 = $subname;
        }

        $statistics[$subname] = array('entries'=>0, 'keyEntries'=>0);

        $fileAlreadyOpen = false;
        // Avoid creating entries for the same locale
        if ($locale != 'en_US.utf-8') {
            foreach ($transEntriesCollection[$subname] as $string => $references) {

                // Check if string appears in core translations
                $entry = $core_backend->getEntry($string);
                if (isset($entry)) continue;

                $statistics[$subname]['entries']++;
                // Get previous translation, it's void if not yet translated
                $translation = $backend->translate($string);
                $marked = $backend->markEntry($string);

                if (!$fileAlreadyOpen) {
                    if (!$gen->create('themes:'.$ctxtype1,$ctxname1)) return;
                    $fileAlreadyOpen = true;
                }
                // Add entry
                $gen->addEntry($string, $references, $translation);
            }
        }

        foreach ($transKeyEntriesCollection[$subname] as $key => $references) {

            // Check if key appears in core translations
            $keyEntry = $core_backend->getEntryByKey($key);
            if (isset($keyEntry)) continue;

            $statistics[$subname]['keyEntries']++;
            // Get previous translation, it's void if not yet translated
            $translation = $backend->translateByKey($key);
            $marked = $backend->markEntryByKey($key);

            // Get the original translation made by developer if any
            if (!$translation && isset($KEYS[$key])) $translation = $KEYS[$key];

            if (!$fileAlreadyOpen) {
                if (!$gen->create('themes:'.$ctxtype1,$ctxname1)) return;
                $fileAlreadyOpen = true;
            }
            // Add key entry
            $gen->addKeyEntry($key, $references, $translation);
        }

        if ($fileAlreadyOpen) {
            $gen->close();
        } else {
            $gen->deleteIfExists('modules:'.$ctxtype1,$ctxname1);
        }
    }
    if (!$gen->open('themes:','fuzzy')) return;
    $fuzzyEntries = $backend->getFuzzyEntries();
    foreach ($fuzzyEntries as $ind => $fuzzyEntry) {
        // Add entry to fuzzy file only if a translation exists
        if (!empty($fuzzyEntry['translation']))
        $gen->addEntry($fuzzyEntry['string'], $fuzzyEntry['references'], $fuzzyEntry['translation']);
    }
    $fuzzyKeys = $backend->getFuzzyEntriesByKey();
    foreach ($fuzzyKeys as $ind => $fuzzyKey) {
        if (!empty($fuzzyKey['translation']))
        $gen->addKeyEntry($fuzzyKey['key'], $fuzzyKey['references'], $fuzzyKey['translation']);
    }
    $gen->close();

    $time = explode(' ', microtime());
    $endTime = $time[1] + $time[0];
    return array('time' => $endTime - $startTime, 'statistics' => $statistics);
}

/* PRIVATE FUNCTIONS */
function translations_gather_common_entries($transEntriesCollection)
{
    $commonEntries = array();
    $subnames = array_keys($transEntriesCollection);
    foreach ($subnames as $subname) {
        foreach ($transEntriesCollection[$subname] as $string => $references) {

            $refs_inserted = false;
            foreach ($subnames as $other_subname) {
                if ($other_subname == $subname) continue;

                if (isset($transEntriesCollection[$other_subname][$string])) {
                    // Found a duplicated ML string
                    if (!isset($commonEntries[$string])) {
                        $commonEntries[$string] = array();
                    }

                    if (!$refs_inserted) {
                        // Insert once the references in $transEntriesCollection[$subname][$string]
                        foreach ($references as $reference) {
                            $ref_exists = false;
                            foreach ($commonEntries[$string] as $existant_refs) {
                                if ($reference['file'] == $existant_refs['file'] &&
                                    $reference['line'] == $existant_refs['line']) {
                                        $ref_exists = true;
                                }
                            }
                            if (!$ref_exists) {
                                $commonEntries[$string][] = $reference;
                            }
                        }
                        $refs_inserted = true;
                    }

                    // Insert the references in $transEntriesCollection[$other_subname][$string]
                    $other_references = $transEntriesCollection[$other_subname][$string];
                    foreach ($other_references as $reference) {
                        $ref_exists = false;
                        foreach ($commonEntries[$string] as $existant_refs) {
                            if ($reference['file'] == $existant_refs['file'] &&
                                $reference['line'] == $existant_refs['line']) {
                                    $ref_exists = true;
                            }
                        }
                        if (!$ref_exists) {
                            $commonEntries[$string][] = $reference;
                        }
                    }

                    // FIXME: This is a workaround for bug #2423, not a fix
                    unset($transEntriesCollection[$subname][$string]);
                    unset($transEntriesCollection[$other_subname][$string]);
                }
            }
        }
    }
    $transEntriesCollection['common'] = $commonEntries;
    return $transEntriesCollection;
}

?>
