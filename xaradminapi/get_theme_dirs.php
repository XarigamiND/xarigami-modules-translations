<?php
/**
 * Get directories list from theme directory
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

//  This function returns an array containing all the xt files
//  in a given directory

$staticNames = array();

function searchFiles($path, $prefix, $force=0)
{
    global $staticNames;

    $path2 = preg_replace("@$prefix@","",$path);

    if ($force) {
        $staticNames[] = $path2;
        return false;
    }

    $pattern = '/^([a-z0-9\-_]+)\.xt$/i';
    $subnames = xarModAPIFunc('translations','admin','get_theme_files',
                              array('themedir'=>"$path",'pattern'=>$pattern));
    if (count($subnames) > 0) {
        $staticNames[] = $path2;
        return true;
    }
    return false;
}

function translations_adminapi_get_theme_dirs($args)
{
    global $staticNames;
    // Get arguments
    extract($args);
    $sitethemedir = xarConfigGetVar('Site.BL.ThemesDirectory');

    // Argument check
    assert(isset($themedir));
    $prefix = "$sitethemedir/$themedir/";

    if (file_exists("$sitethemedir/$themedir")) {
        $dd = opendir("$sitethemedir/$themedir");
        while ($filename = readdir($dd)) {
            if ($filename == 'blocks' || $filename == 'pages' || $filename == 'includes') {
                searchFiles("$sitethemedir/$themedir/$filename", $prefix);
            } elseif ($filename == 'modules') {
                searchFiles("$sitethemedir/$themedir/modules", $prefix, 1);
                $dd2 = opendir("$sitethemedir/$themedir/modules");
                while ($moddir = readdir($dd2)) {
                    if (($moddir == '.') || ($moddir == '..') || ($moddir == 'SCCS')) continue;
                    if (is_dir("$sitethemedir/$themedir/modules/$moddir")) {
                        $force = 0;
                        $filesBlock = false;
                        $filesIncl = false;
                        if (is_dir("$sitethemedir/$themedir/modules/$moddir/blocks")) {
                            $filesBlock = searchFiles("$sitethemedir/$themedir/modules/$moddir/blocks", $prefix);
                        }
                        if (is_dir("$sitethemedir/$themedir/modules/$moddir/includes")) {
                            $filesIncl = searchFiles("$sitethemedir/$themedir/modules/$moddir/includes", $prefix);
                        }
                        if (is_dir("$sitethemedir/$themedir/modules/$moddir/properties")) {
                            $filesIncl = searchFiles("$sitethemedir/$themedir/modules/$moddir/properties", $prefix);
                        }
                        if (is_dir("$sitethemedir/$themedir/modules/$moddir/objects")) {
                            $filesIncl = searchFiles("$sitethemedir/$themedir/modules/$moddir/objects", $prefix);
                        }
                        if ($filesBlock || $filesIncl) $force = 1;
                        searchFiles("$sitethemedir/$themedir/modules/$moddir", $prefix, $force);
                    }
                }
                closedir($dd2);
            }
        }
        closedir($dd);
    }
    sort($staticNames);
    return $staticNames;
}

?>
