<?php
/**
 * Release module translations
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
 * release a translations package
 * @param $args['modid'] module registry identifier
 * @param $args['locale'] locale name
 * @returns string
 * @return the package filename
 */
function translations_adminapi_release_module_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($modid) && isset($locale));

    if (!($modinfo = xarModGetInfo($modid))) return;
    $modname = $modinfo['name'];
    $modversion = $modinfo['version'];

    if (!$bt = xarModAPIFunc('translations','admin','release_backend_type')) return;;

    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) throw new ForbiddenOperationException();

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        throw new ClassNotFoundException(null, $msg);
    }

    $dirpath = "var/locales/$locale/php/modules/$modname/";
    if (!file_exists($dirpath.'common.php')) {
        $link = xarModURL('translations', 'admin', 'update_info', array('dntype' => 'module'));
        return xarTplModule('translations','user', 'errors', array('errortype' => 'missing_translations', 'link' => $link));

    }

    $newargs['basefilename'] = $modname;
    $newargs['version'] = $modversion;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $releaseBackend = xarModAPIFunc('translations','admin','make_package',$newargs);

    return $releaseBackend;
}

?>
