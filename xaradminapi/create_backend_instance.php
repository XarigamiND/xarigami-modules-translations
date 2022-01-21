<?php
/**
 * Create backend instance
 *
 * @package modules
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations Module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
*/

function translations_adminapi_create_backend_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($interface));
    assert(isset($locale));

    if ($interface == 'ReferencesBackend') {
        $bt = xarModAPIFunc('translations','admin','work_backend_type');
    } elseif ($interface == 'TranslationsBackend') {
        $bt = xarModAPIFunc('translations','admin','release_backend_type');
    }
    if (!$bt) return;
    switch ($bt) {
    case 'php':
        xarLogMessage("MLS: Creating PHP backend");
        sys::import('xarigami.xarMLSPHPBackend');
        return new xarMLS__PHPTranslationsBackend(array($locale));
    case 'xml':
        xarLogMessage("MLS: Creating XML backend");
        sys::import('xarigami.xarMLSXMLBackend');
        return new xarMLS__XMLTranslationsBackend(array($locale));
    case 'xml2php':
        xarLogMessage("MLS: Creating XML2PHP backend");
        sys::import('xarigami.xarMLSXML2PHPBackend');
        return new xarMLS__XML2PHPTranslationsBackend(array($locale));
    }
    $msg = xarML('Backend class #(1) not existing.', $bt);
    throw new ClassNotFoundException($bt);
}

?>
