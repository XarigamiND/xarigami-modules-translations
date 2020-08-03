<?php
/**
 * Version information for Translations
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team 
 */

function translations_userapi_getmenulinks ($args) 
{
    if (xarSecurityCheck('ReadTranslations', 0) == true) {
        $menulinks[] = array(
            'url'   => xarModURL('translations', 'user', 'show_status', array('action' => 'post')),
            'title' => xarML('Show the progress status of the locale currently being translated'),
            'label' => xarML('Progress report'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>