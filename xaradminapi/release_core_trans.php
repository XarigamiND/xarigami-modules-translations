<?php
/**
 * Release core translations
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

function translations_adminapi_release_core_trans($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($locale));

    if (!$bt = xarModAPIFunc('translations','admin','release_backend_type')) return;;
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) throw new ForbiddenOperationException();

    if ($bt != 'php') {
        $msg = xarML('Unsupported backend type \'#(1)\'. Don\'t know how to generate release package for that backend.', $bt);
        throw new ClassNotFoundException(null, $msg);
    }

    $dirpath = "var/locales/$locale/php/core/";
    if (!file_exists($dirpath.'core.php')) {
        $link = xarModURL('translations', 'admin', 'update_info', array('dntype' => 'core'));
        return xarTplModule('translations','user', 'errors', array('errortype' => 'missing_translations', 'link' => $link));
    }

    // return translations_make_package('xarigami', XARCORE_VERSION_NUM, $dirpath, $locale);
    $newargs['basefilename'] = 'xarigami';
    $newargs['version'] = XARCORE_VERSION_NUM;
    $newargs['dirpath'] = $dirpath;
    $newargs['locale'] = $locale;
    $backend = xarModAPIFunc('translations','admin','make_package',$newargs);
}

?>
