<?php
/**
 * Get filenames list from specified directory in module
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_adminapi_get_module_files($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($moddir) && isset($pattern));

    $names = array();
    if (file_exists($moddir)) {
        $dd = opendir($moddir);
        while ($filename = readdir($dd)) {
            if (!preg_match($pattern, $filename, $matches)) continue;
            $names[] = $matches[1];
        }
        closedir($dd);
    }
    return $names;
}

?>
