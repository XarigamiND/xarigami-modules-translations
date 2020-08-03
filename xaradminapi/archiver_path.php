<?php
/**
 * Archiver path
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

function translations_adminapi_archiver_path($args = NULL)
{
    static $archiver_path = NULL;
    if (isset($args['archiver_path'])) {
        $archiver_path = $args['archiver_path'];
    } elseif ($archiver_path == NULL) {
        $archiver_path = xarModGetVar('translations', 'archiver_path');
    }
    return $archiver_path;
}

?>