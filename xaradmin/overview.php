<?php
/**
 * Overview for Translations
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 */

/**
 * Overview displays standard Overview page
 */
function translations_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
        $data['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    return xarTplModule('translations', 'admin', 'main', $data, 'main');
}

?>