<?php
// fis.baidu.com

if (!defined('__DIR__')) define ('__DIR__', dirname(__FILE__));

require_once(__DIR__ . '/../constant.var.php');

require_once(__DIR__ . '/../log/Log.class.php');

require(__DIR__ . '/Util.class.php');
require(__DIR__ . '/File.class.php');
require(__DIR__ . '/filetype/PHP.class.php');
require(__DIR__ . '/filetype/JSON.class.php');

class Mock {
    static public $logger = null; //logger
    static public $encoding = 'utf-8';
    static public $testPath;
    static public $wwwRoot;
    static public $filetype = array(
        '.php',
        '.json'
    );

    static public function init($root, $encoding = 'utf-8', array $opt = array()) {
        Mock::$logger = Log::getLogger();
        $root = Util::normalizePath($root);
        Mock::$wwwRoot = $root;
        Mock::$testPath = $root . '/test';

        //set encoding
        Mock::setEncoding($encoding);
    }

    static public function setEncoding($encoding) {
        Mock::$encoding = $encoding;
    }

    static public function setTestPath($root) {
        $root = Util::normalizePath($root);
        Mock::$testPath = $root;
    }

    static public function getData($subpath) {
        $subpath = Util::normalizePath($subpath);
        Mock::$logger->debug('start get data path: %s', $subpath);
        $file = new File($subpath);
        $ret = array();
        foreach (Mock::$filetype as $type) {
            $testFilePath = Util::normalizePath(Mock::$testPath . '/' . $file->getFilePathNoExt() . $type);
            Mock::$logger->debug('fetch test data path: %s type: %s', $testFilePath, $type);
            if (file_exists($testFilePath)) {
                if ($type == '.php') {
                    $testFile = new PHP($testFilePath);
                } else if ($type == '.json') {
                    $testFile = new JSON($testFilePath, array(
                        'encoding' => Mock::$encoding
                    ));
                }
                $ret = $testFile->getData();
                break;
            }
        }
        return $ret;
    }
}
