<?php
/**
 * Get directories list from module directory
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
 *  This function returns an array containing all the php files
 *  in a given directory that start with "xar"
 * @return array
 */
function translations_adminapi_get_module_dirs($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($moddir));

    $names = array();
    $dropit = array(
        'xardocs',
        'xarimages',
        'xarclass',
        'xardata',
        'xarwidgets',
        'xartests',
        'xarjava',
        'xarjavascript',
        'xarstyles');
    if (file_exists("modules/$moddir")) {
        $dd = opendir("modules/$moddir");
        while ($filename = readdir($dd)) {
            if (!is_dir("modules/$moddir/$filename")) continue;
            if (substr($filename,0,3) != "xar") continue;
            if (in_array($filename, $dropit)) continue;
            $names[] = preg_replace("/^xar/","",$filename);
        }
        closedir($dd);
    }
    if (file_exists("modules/$moddir/xartemplates")) {
        if (file_exists("modules/$moddir/xartemplates/includes"))
            $names[] = 'templates/includes';
        if (file_exists("modules/$moddir/xartemplates/blocks"))
            $names[] = 'templates/blocks';
        if (file_exists("modules/$moddir/xartemplates/properties"))
            $names[] = 'templates/properties';
        if (file_exists("modules/$moddir/xartemplates/objects"))
            $names[] = 'templates/objects';
    }
    return $names;
}

?>
