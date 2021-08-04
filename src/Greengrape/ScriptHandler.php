<?php
/**
 * ScriptHandler class file
 *
 * @package Greengrape
 */

namespace Greengrape;

/**
 * ScriptHandler
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class ScriptHandler
{
    /**
     * Create config file from -dist template
     *
     * @return void
     */
    public static function createConfigFile()
    {
        print "[greengrape] Copying config.ini-dist to config.ini...";
        copy('config.ini-dist', 'config.ini');
        print "done.\n";
    }

    /**
     * Make cache directory writable
     *
     * @return void
     */
    public static function makeCacheWritable()
    {
        print "[greengrape] Making cache directory writable...";
        chmod('cache', 0777);

        if (!file_exists('cache/content')) {
            mkdir('cache/content');
        }
        chmod('cache/content', 0777);
        print "done.\n";
    }

    /**
     * Create htaccess file
     *
     * @return void
     */
    public static function createHtaccessFile()
    {
        print "[greengrape] Copying .htaccess-dist to .htaccess...";
        copy('.htaccess-dist', '.htaccess');
        print "done.\n";
        print "[greengrape] Depending on your webserver setup you may have to "
            . " edit .htaccess file to set the RewriteBase\n";
    }
}
