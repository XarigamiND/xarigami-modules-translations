<?php
/**
 * Entry point for translations admin
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team 
 */


/**
 * Entry point for translations admin screen
 *
 * A somewhat longer description of the function which may be 
 * multiple lines, can contain examples.
 *
 * @access  public
 * @return  array template data
 */
function translations_admin_main()
{
    xarResponseRedirect(xarModURL('translations', 'admin', 'start'));
    return array();
}

?>