<?php
/**
 * Get work backend type
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

function translations_adminapi_work_backend_type($args = NULL)
{
    static $type = NULL;
    if (isset($args['type'])) {
        $type = $args['type'];
    } elseif ($type == NULL) {
        $type = xarModGetVar('translations', 'work_backend_type');
    }
    return $type;
}

?>