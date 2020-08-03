<?php
/**
 * Update configuration for translations module
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage Xarigami Translations module
 * @copyright (C) 2009-2010 2skies.com
 * @link http://xarigami.com/project/xarigami_translations
 * @author Xarigami Team
 * @author Marco Canini
 * Purpose of file: PHP files parsing
 */

class PHPParser
{
    public $transEntries = array();
    public $transKeyEntries = array();
    public $includedFiles = array();
    public $parsedFiles = array();
    public $notToParseFiles = array();

    public $_fd;
    public $_offs;
    public $_pos;
    public $_len;
    public $_buf;
    public $_line;
    public $_token;
    public $_right;
    public $_string;
    public $filename;

    public $tokenarray;
    public $endtokenarray;
    public $tokenarraytype;
    public $iskeytokenarray;
    public $strlentokenarray;
    public $strlenendtokenarray;
    public $lasttokenarray;

    function PHPParser()
    {
        $this->tokenarray = array("xarML('", "xarMLByKey('", 'xarML("', 'xarMLByKey("', '{ML_dont_parse', '{ML_include', '{ML_add_string', '{ML_add_key', "include_once '", "include '", "require_once '", "require '", 'include_once "', 'include "', 'require_once "', 'require "', 'include_once(', 'include(', 'require_once(', 'require(', 'sys::import(');
        $this->endtokenarray = array(array("')","',"), array("')","',"), array('")','",'), array('")','",'), array('}'), array('}'), array('}'), array('}'), array("';"), array("';"), array("';"), array("';"), array('";'), array('";'), array('";'), array('";'), array(');'), array(');'), array(');'), array(');'), array(');'));
        $this->tokenarraytype = array('function', 'function', 'function', 'function', 'ML_dont_parse', 'ML_include', 'ML_add_string', 'ML_add_key', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'include', 'import');
        $this->iskeytokenarray = array(0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->strslasharray = array(1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->strlentokenarray = array(7, 12, 7, 12, 14, 11, 14, 11, 14, 9, 14, 9, 14, 9, 14, 9, 13, 8, 13, 8, 12);
        $this->strlenendtokenarray = array(2, 2, 2, 2, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);
    }

    function getTransEntries()
    {
        return $this->transEntries;
    }

    function getTransKeyEntries()
    {
        return $this->transKeyEntries;
    }

    function _get_token()
    {
        $found = false;
        // if (defined('PHPPARSERDEBUG'))
        // printf("Getting line %d\n"."for %s token %d<br />\n", $this->_line, $this->_right?'end':'begin', $this->_token);
        $this->_pos = -1;
        foreach( $this->lasttokenarray as $n => $v )
        {
            $p = strpos( $this->_buf, $v, $this->_offs );
            if ($p===FALSE)
                continue;
            if (($p<$this->_pos)||($this->_pos==-1)) {
                $this->_pos = $p;
                if ($this->_right != true) $this->_token = $n;
            }
        }
        if ($this->_pos != -1) {
            // if (defined('PHPPARSERDEBUG'))
                // printf("Found %s token %s[%d] at pos %d<br />\n", $this->_right?'end':'begin', htmlspecialchars(substr($this->_buf, $this->_pos, strlen($this->tokenarray[$this->_token]))), $this->_token, $this->_pos);
            if ($this->_right)
                $this->_string .= substr($this->_buf, $this->_offs, $this->_pos - $this->_offs);
            if ($this->_right)
                $this->_offs = $this->_pos + $this->strlenendtokenarray[$this->_token];
            else
                $this->_offs = $this->_pos + $this->strlentokenarray[$this->_token];
            if ($this->_offs > $this->_len) $this->_offs = $this->_len;
            $found = true;
            if (!$this->_right) {
                // ParseClose
                $this->_string ='';
                $this->_right = true;
                $this->lasttokenarray = $this->endtokenarray[$this->_token];
                $token = $this->_token;
                if ($this->_get_token()) {
                    // if (defined('PHPPARSERDEBUG'))
                       // printf("Result: %s<br />\n", $this->_string);
                    switch ($this->tokenarraytype[$token]) {
                    case 'function':
                        // Delete extra whitespaces and spaces around newline
                        $this->_string = trim($this->_string);
                        $this->_string = preg_replace('/[\t ]+/',' ',$this->_string);
                        $this->_string = preg_replace('/\s*\n\s*/',"\n",$this->_string);
                        if ($this->strslasharray[$token]) {
                            $this->_string = str_replace('\\\'','\'',$this->_string);
                        }
                        if ($this->iskeytokenarray[$token]) {
                            if (!isset($this->transKeyEntries[$this->_string])) {
                                $this->transKeyEntries[$this->_string] = array();
                            }
                            $this->transKeyEntries[$this->_string][] = array('line' => $this->_line, 'file' => $this->filename);
                        } else {
                            if (!isset($this->transEntries[$this->_string])) {
                                $this->transEntries[$this->_string] = array();
                            }
                            $this->transEntries[$this->_string][] = array('line' => $this->_line, 'file' => $this->filename);
                        }
                        break;
                    case 'ML_dont_parse':
                        $this->_string = trim($this->_string, " \t'");
                        $this->notToParseFiles[$this->_string] = true;
                        break;
                    case 'ML_include':
                        $this->_string = trim($this->_string, " \t'");
                        $this->includedFiles[$this->_string] = true;
                        break;
                    case 'ML_add_string':
                        $this->_string = trim($this->_string, " \t'");
                        if (!isset($this->transEntries[$this->_string])) {
                            $this->transEntries[$this->_string] = array();
                        }
                        $this->transEntries[$this->_string][] = array('line' => $line, 'file' => $filename);
                        break;
                    case 'ML_add_key':
                        $this->_string = trim($this->_string, " \t'");
                        if (!isset($this->transKeyEntries[$this->_string])) {
                            $this->transKeyEntries[$this->_string] = array();
                        }
                        $this->transKeyEntries[$this->_string][] = array('line' => $line, 'file' => $filename);
                        break;
                    case 'include':
                        $this->_string = trim($this->_string, " \t\"'");
                        //disregrd loggers
                        if (!preg_match('/loggers/', $this->_string, $match)) {
                            $this->includedFiles[$this->_string] = true;
                        }
                        break;
                    case 'import':
                        $this->_string = trim($this->_string, " \t\"'");
                        //disregrd loggers
                        if (!preg_match('/loggers/', $this->_string, $match) && !preg_match('/modules/', $this->_string, $match)) {
                            // A simple copy of precore sys::import. Can probably do better than that.
                            // note: we don't want to have get any core module files.
                    
                            $this->_string = sys::lib() . str_replace('.', '/', $this->_string) . '.php';
                            $this->includedFiles[$this->_string] = true;
                        }
                        break;
                    }
                    $this->lasttokenarray = $this->tokenarray;
                    $this->_right = false;
                    $this->_get_token();
                }
            }
        } else {
            $found = false;
            if ($this->_right) {
                $this->_string .= substr($this->_buf, $this->_offs);
                while (!feof($this->_fd)) {
                    $this->_buf = fgets($this->_fd, 1024);
                    $this->_len = strlen($this->_buf);
                    $this->_line++;
                    $this->_offs = 0;
                    if (!$this->_get_token()) continue;
                    $found = true;
                    break;
                }
            }
            $this->_offs = 0;
        }
        return $found;
    }

    function parse($filename)
    {

        $this->parseFile($filename);

        $this->parsedFiles[$filename] = true;
        $includedFiles = $this->includedFiles;
        $this->includedFiles = array();

        foreach($includedFiles as $ifilename => $t) {
            if (!isset($this->parsedFiles[$ifilename]) &&
                !isset($this->notToParseFiles[$ifilename])) {

                $this->parse($ifilename);
            }
        }
    }

    function parseFile($filename)
    {
        if (!file_exists($filename)) return;
        $this->filename = $filename;
        $this->_fd = fopen($filename, 'r');
        if (!$this->_fd) {
            $msg = xarML('Cannot open the file #(1)', $filename);
            throw new FileNotFoundException(null, $msg);
        }
        if (!$filesize = filesize($filename)) return;

        $this->_offs = 0;
        $this->_len = 0;
        $this->_right = false;
        $this->_line = 0;
        $this->lasttokenarray = $this->tokenarray;

        while (!feof($this->_fd)) {
            $this->_buf = fgets($this->_fd, 1024);
            $this->_len = strlen($this->_buf);
            $this->_line++;
            $this->_offs = 0;
            $this->_get_token();
        }

        fclose($this->_fd);
    }
}
?>
