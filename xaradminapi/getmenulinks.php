<?php
/**
 * Pass admin links to the admin menu
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


/**
 * Pass individual menu items to the admin menu
 *
 * @return array containing the menulinks for the admin menu items.
 */
function translations_adminapi_getmenulinks()
{
    $menuLinks[] = array('url'   => xarModURL('translations','admin','start'),
                         'title' => xarML('Work on translations'),
                         'label' => xarML('Translate'),
                         'active' => array('start','update_info','core_overview','choose_a_module','choose_a_theme','theme_overview','module_overview','translate','delete_fuzzy','generate_skels_info'));
    $menuLinks[] = array('url'   => xarModURL('translations','admin','bulk'),
                         'title' => xarML('Perform bulk operations'),
                         'label' => xarML('Bulk'),
                         'active' => array('bulk'));
    $menuLinks[] = array('url'   => xarModURL('translations','admin','show_status'),
                         'title' => xarML('Show the progress status of the locale currently being translated'),
                         'label' => xarML('Progress report'),
                         'active'=>array('show_status'));
    $menuLinks[] = array('url'   => xarModURL('translations','admin','modifyconfig'),
                         'title' => xarML('Modify translation configuration Values'),
                         'label' => xarML('Modify Config'),
                         'active'=>array('modifyconfig'));

    return $menuLinks;
}
?>