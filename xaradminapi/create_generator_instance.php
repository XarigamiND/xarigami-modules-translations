<?php
/**
 * Create generator instance
 *
 * @copyright (C) 2003 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_adminapi_create_generator_instance($args)
{
    // Get arguments
    extract($args);

    // Argument check
    assert(isset($interface));
    assert(isset($locale));

    if ($interface == 'ReferencesGenerator') {
        $bt = xarModAPIFunc('translations','admin','work_backend_type');
    } elseif ($interface == 'TranslationsGenerator') {
        $bt = xarModAPIFunc('translations','admin','release_backend_type');
    }
    if (!$bt) return;
    switch ($bt) {
        case 'php':
            sys::import('modules.translations.class.PHPTransGenerator');
            return new PHPTranslationsGenerator($locale);
        case 'xml':
            sys::import('modules.translations.class.XMLTransSkelsGenerator');
            return new XMLTranslationsSkelsGenerator($locale);
        case 'xml2php':
            sys::import('modules.translations.class.PHPTransGenerator');
            return new PHPTranslationsGenerator($locale);
    }
    $msg = xarML('Backend class #(1) not existing.', $bt);
    throw new ClassNotFoundException($bt);
}

?>
