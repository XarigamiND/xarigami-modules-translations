<?php
/**
 * Get filenames list from module directory
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

//  This function returns an array containing all the php files
//  in a given directory that start with "xar"
function translations_adminapi_get_module_phpfiles($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($moddir));

    $names = array();
    if (file_exists("modules/$moddir")) {
        $dd = opendir("modules/$moddir");
        while ($filename = readdir($dd)) {
//            if (is_dir("modules/$moddir/$filename") && (substr($filename,0,3) == "xar")) {
//                $names[] = preg_replace("/^xar/","",$filename);
//                continue;
//            }
            if (!preg_match('!^([a-z\-_]+)\.php$!i', $filename, $matches)) continue;
            $phpname = $matches[1];
//            if ($phpname == 'xarversion') continue;
            if ($phpname == 'xartables') continue;
            $names[] = preg_replace("/^xar/","",$phpname);
        }
        closedir($dd);
    }
    return $names;
}

?>
