<?php
/**
 * Translations User GUI functions
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Volodymyr Metenchuk
 */
function translations_user_main($args)
{
    // Security Check
    if (!xarSecurityCheck('ReadTranslations')) return xarResponseForbidden();

    $data = array();
    return $data;
}
?>
