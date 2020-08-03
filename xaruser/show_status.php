<?php
/**
 * Status report for the current translation
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_user_show_status()
{
    return xarModFunc('translations', 'admin', 'show_status');
}
?>