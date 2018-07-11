<?php

/*
 * Data bundle addon for Bear Framework
 * https://github.com/ivopetkov/data-bundle-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

require __DIR__ . '/../vendor/autoload.php';

/**
 * 
 */
class BearFrameworkAddonTestCase extends PHPUnit_Framework_TestCase
{

    private $app = null;

    function getTestDir()
    {
        return sys_get_temp_dir() . '/unittests/' . uniqid() . '/';
    }

    function getApp($config = [], $createNew = false)
    {
        if ($this->app == null || $createNew) {
            $rootDir = $this->getTestDir();
            $this->app = new BearFramework\App();
            $this->createDir($rootDir . 'app/');
            $this->createDir($rootDir . 'data/');
            $this->createDir($rootDir . 'logs/');
            $this->createDir($rootDir . 'addons/');

            $initialConfig = [
                'appDir' => $rootDir . 'app/',
                'dataDir' => $rootDir . 'data/',
                'logsDir' => $rootDir . 'logs/',
                'addonsDir' => realpath($rootDir . 'addons/'),
                //'handleErrors' => false
            ];
            $config = array_merge($initialConfig, $config);
            foreach ($config as $key => $value) {
                $this->app->config->$key = $value;
            }

            $this->app->initialize();
            $this->app->request->base = 'http://example.com/www';
            $this->app->request->method = 'GET';

            $this->app->addons->add('ivopetkov/data-bundle-bearframework-addon');
        }

        return $this->app;
    }

    function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    function createFile($filename, $content)
    {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['dirname']) && $pathinfo['dirname'] !== '.') {
            if (!is_dir($pathinfo['dirname'])) {
                mkdir($pathinfo['dirname'], 0777, true);
            }
        }
        file_put_contents($filename, $content);
    }

}
