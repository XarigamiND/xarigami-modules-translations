<?php
/**
 * Archiver flags
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

function translations_adminapi_archiver_flags($args = NULL)
{
    static $archiver_flags = NULL;
    if (isset($args['archiver_flags'])) {
        $archiver_flags = $args['archiver_flags'];
    } elseif ($archiver_flags == NULL) {
        $archiver_flags = xarModGetVar('translations', 'archiver_flags');
    }
    return $archiver_flags;
}

?>