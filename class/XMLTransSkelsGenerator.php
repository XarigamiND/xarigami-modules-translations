<?php
/* @package modules
 * @copyright (C) 2002 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami Translations Module
 * @copyright (C) 2009-2011 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Canini
 * ----------------------------------------------------------------------
 */

class XMLTranslationsSkelsGenerator
{
    public $locale;
    public $fp;
    public $fileName;
    public $baseDir;

    function __construct($locale)
    {
        $this->locale = $locale;
    }

    function bindDomain($dnType, $dnName='xarigami')
    {
        $sitethemedir = xarConfigGetVar('Site.BL.ThemesDirectory');
        $localethemedir = 'themes';
        $varDir = sys::varpath();
        $locales_dir = "$varDir/locales";
        $locale_dir = "$locales_dir/{$this->locale}";
        $xml_dir = "$locale_dir/xml";
        $modules_dir = "$xml_dir/modules";
        $themes_dir = "$xml_dir/$localethemedir";
        $core_dir = "$xml_dir/core";

        $canWrite = 1;
        if (file_exists($locales_dir)) {
            if (file_exists($locale_dir)) {
                if (file_exists($xml_dir)) {
                    if (file_exists($modules_dir) && file_exists($themes_dir) &&
                        file_exists($core_dir)) {
                        if (!is_writeable($modules_dir)) $canWrite = 0;
                        if (!is_writeable($themes_dir)) $canWrite = 0;
                        if (!is_writeable($core_dir)) $canWrite = 0;
                    } else {
                        if (is_writeable($xml_dir)) {
                            if (file_exists($modules_dir)) {
                                if (!is_writeable($modules_dir)) $canWrite = 0;
                            } else {
                                mkdir($modules_dir, 0777);
                            }
                            if (file_exists($themes_dir)) {
                                if (!is_writeable($themes_dir)) $canWrite = 0;
                            } else {
                                mkdir($themes_dir, 0777);
                            }
                            if (file_exists($core_dir)) {
                                if (!is_writeable($core_dir)) $canWrite = 0;
                            } else {
                                mkdir($core_dir, 0777);
                            }
                        } else {
                            $canWrite = 0; // var/locales/LOCALE/xml is unwriteable
                        }
                    }
                } else {
                    if (is_writeable($locale_dir)) {
                        mkdir($xml_dir, 0777);
                        mkdir($modules_dir, 0777);
                        mkdir($themes_dir, 0777);
                        mkdir($core_dir, 0777);
                    } else {
                        $canWrite = 0; // var/locales/LOCALE is unwriteable
                    }
                }
            } else {
                if (is_writeable($locales_dir)) {
                    mkdir($locale_dir, 0777);
                    mkdir($xml_dir, 0777);
                    mkdir($modules_dir, 0777);
                    mkdir($themes_dir, 0777);
                    mkdir($core_dir, 0777);
                } else {
                    $canWrite = 0; // var/locales is unwriteable
                }
            }
        } else {
            $canWrite = 0; // var/locales missed
        }

        if (!$canWrite) {
            return xarTplModule('base','user','errors',array('errortype' => 'not_writeable','var1'=>$locales_dir));
        }

        switch ($dnType) {
            case XARMLS_DNTYPE_MODULE:
            $this->baseDir = "$modules_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);

            $dirnames = xarModAPIFunc('translations','admin','get_module_dirs',array('moddir'=>$dnName));
            foreach ($dirnames as $dirname) {
                if (file_exists($this->baseDir.$dirname)) continue;
                if (!file_exists("modules/$dnName/xar$dirname")) continue;
                mkdir($this->baseDir.$dirname, 0777);
            }
            break;
            case XARMLS_DNTYPE_THEME:
            $this->baseDir = "$themes_dir/$dnName/";
            if (!file_exists($this->baseDir)) mkdir($this->baseDir, 0777);
            //if (!file_exists($this->baseDir.'templates')) mkdir($this->baseDir.'templates', 0777);
            $dirnames = xarModAPIFunc('translations','admin','get_theme_dirs',array('themedir'=>$dnName));
            foreach ($dirnames as $dirname) {
                //check locale directory exists
                if (file_exists($this->baseDir.$dirname)) continue;
                //check the theme directory exists
                if (!file_exists("$sitethemedir/$dnName/$dirname")) continue;
                mkdir($this->baseDir.$dirname, 0777);
            }
            break;
            case XARMLS_DNTYPE_CORE:
            $this->baseDir = $core_dir.'/';
        }
        return true;
    }

    function create($ctxType, $ctxName)
    {
        assert(!empty($this->baseDir));
        $this->fileName = $this->baseDir;

        if (!preg_match("/^[a-z]+:$/", $ctxType)) {
           list($prefix,$directory) = explode(':',$ctxType);
           if ($directory != "") $this->fileName .= $directory . "/";
        }
        $this->fileName .= $ctxName . ".xml";

        $this->fp = fopen($this->fileName.'.swp', 'w');

        // XML files are always encoded in utf-8
        // The utf-8 encoding is guarateed by the fact that translations module
        // has to run in UNBOXED MULTI language mode
        fwrite($this->fp, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
        // FXIME: <marco> Add schema reference
        fwrite($this->fp, "<translations xmlns=\"http://xarigami.com/2011/ns/translations\" locale=\"{$this->locale}\">\n");
        return true;
    }

    function open($ctxType, $ctxName)
    {
        assert(!empty($this->baseDir));
        $this->fileName = $this->baseDir;

        if (!preg_match("/^[a-z]+:$/", $ctxType)) {
           list($prefix,$directory) = explode(':',$ctxType);
           if ($directory != "") $this->fileName .= $directory . "/";
        }
        $this->fileName .= $ctxName . ".xml";

        if (file_exists($this->fileName)) {
            $lines = file($this->fileName);
            $this->fp = fopen($this->fileName.'.swp', 'w');
            foreach ($lines as $line_num => $line) {
                if (!strncmp($line, '</translations>', 15)) continue;
                if (strncmp($line, '<translations ', 14) == 0) {
                    fwrite($this->fp, "<translations xmlns=\"http://xarigami.com/2011/ns/translations\" locale=\"{$this->locale}\">\n");
                } else {
                    fwrite($this->fp, $line);
                }
            }
        } else {
            $this->fp = fopen($this->fileName.'.swp', 'w');
            // Translations XML files are always encoded in utf-8
            fwrite($this->fp, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
            // FXIME: <marco> Add schema reference
            fwrite($this->fp, "<translations xmlns=\"http://xarigami.com/2011/ns/translations\" locale=\"{$this->locale}\">\n");
        }

        return true;
    }

    function close()
    {
        fwrite($this->fp, "</translations>\n");
        fclose($this->fp);
        // I don't know why I can't rename this file under Windows :(
        // rename($this->fileName.'.swp', $this->fileName);
        copy($this->fileName.'.swp', $this->fileName);
        unlink($this->fileName.'.swp');
        return true;
    }

    function deleteIfExists($ctxType, $ctxName)
    {
        assert(!empty($this->baseDir));
        $this->fileName = $this->baseDir;

        if (!preg_match("/^[a-z]+:$/", $ctxType)) {
           list($prefix,$directory) = explode(':',$ctxType);
           if ($directory != "") $this->fileName .= $directory . "/";
        }
        $this->fileName .= $ctxName . ".xml";
        if (file_exists($this->fileName)) unlink($this->fileName);
        return true;
    }

    function addEntry($string, $references, $translation = '')
    {
        // string and translation are already encoded in utf-8
        /*$string = utf8_encode(htmlspecialchars($string));
        $translation = utf8_encode($translation);*/
        //Allow html tags
        $translation = htmlspecialchars($translation);
        $string = htmlspecialchars($string);
        fwrite($this->fp, "<entry>");
        fwrite($this->fp, "<string>".$string."</string>");
        fwrite($this->fp, "<translation>".$translation."</translation>");
        if (xarModGetVar('translations', 'maxreferences')) {
            fwrite($this->fp, "\t\t<references>\n");
            foreach($references as $reference) {
                fwrite($this->fp, "\t\t\t<reference file=\"$reference[file]\" line=\"$reference[line]\" />\n");
            }
            fwrite($this->fp, "\t\t</references>\n");
        }
        fwrite($this->fp, "</entry>\n");
    }

    function addKeyEntry($key, $references, $translation = '')
    {
        // translation is already encoded in utf-8
        //$translation = utf8_encode($translation);

        fwrite($this->fp, "<keyEntry>");
        fwrite($this->fp, "<key>".$key."</key>");
        fwrite($this->fp, "<translation>".$translation."</translation>");
        if (xarModGetVar('translations', 'maxreferences')) {
            fwrite($this->fp, "\t\t<references>\n");
            foreach($references as $reference) {
                fwrite($this->fp, "\t\t\t<reference file=\"$reference[file]\" line=\"$reference[line]\" />\n");
            }
            fwrite($this->fp, "\t\t</references>\n");
        }
        fwrite($this->fp, "\t</keyEntry>\n");
    }

}

?>
