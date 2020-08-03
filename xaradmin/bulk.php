<?php
/**
 * Translations bulk operations
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team 
 */


/**
 * Entry point for translations bulk operations
 *
 * @access  public
 * @return  array template data
 */
function translations_admin_bulk()
{
    $data['authid'] = xarSecGenAuthKey();
    $data['menulinks'] = xarModAPIFunc('translations','admin','getmenulinks');
    return $data;
}

?>