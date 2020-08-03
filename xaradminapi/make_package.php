<?php
/**
 * Make package
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

function translations_adminapi_make_package($args)
{
    extract($args);

    // Argument check
    assert('isset($basefilename) && isset($version) && isset($dirpath) && isset($locale)');

    if (!$archiver_path = xarModAPIFunc('translations','admin','archiver_path')) return;
    if (!file_exists($archiver_path) || !is_executable($archiver_path)) {
        $msg = xarML('Unsupported release backend : cannot execute \'#(1)\'.', $archiver_path);
        throw new ClassNotFoundException(null, $msg);
    }
    if (!$archiver_flags = xarModAPIFunc('translations','admin','archiver_flags')) return;

    if (strpos($archiver_path, 'zip') !== false) {
        $ext = 'zip';
    } elseif (strpos($archiver_path, 'tar') !== false) {
        $ext = 'tar';
        if (strpos($archiver_flags, 'z') !== false) {
            $ext .= '.gz';
        } elseif (strpos($archiver_flags, 'j') !== false) {
            $ext .= 'bz2';
        }
    } else {
        $ext = 'unknown';
    }
    $filename = "$basefilename-{$version}_i18n-$locale.$ext";
    $filepath = sys::varpath().'/cache/'.$filename;

    $archiver_flags = str_replace('%f', $filepath, $archiver_flags);
    $archiver_flags = str_replace('%d', $dirpath, $archiver_flags);

    system("$archiver_path $archiver_flags");

    return $filename;
}

?>