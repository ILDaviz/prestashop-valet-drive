<?php

/**
 * Class PrestaShopValetDriver
 * @author David Galet
 * @user https://github.com/ILDaviz
 * @source https://gitlab.com/snippets/1717590
 * @source https://gitlab.com/-/snippets/1717590
 * @version 1.6.2
 */

namespace Valet\Drivers\Custom;

use Valet\Drivers\ValetDriver;

class PrestaShopValetDriver extends ValetDriver
{
    public static $ps_exclusions = ['ajax.php','dialog.php','ajax_products_list.php','autoupgrade/','filemanager/'];
    /**
     * Determine if the driver serves the request.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return bool
     */
    public function serves(string $sitePath,string $siteName,string $uri): bool
    {
        if(self::isPrestashop($sitePath) && self::stringContains($uri,self::$ps_exclusions)){
            return false;
        }elseif(self::isPrestashop($sitePath)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Determine if is prestashop
     * @param $sitePath
     * @return bool
     */
    public static function isPrestashop($sitePath){
        return file_exists($sitePath . '/classes/PrestashopAutoload.php');
    }

    /**
     * Check if string contains a string
     * @param $string
     * @param $doesContains
     * @return bool
     */
    public static function stringContains($string,$doesContains=null){
        if(is_array($doesContains)){
            foreach ($doesContains as $doesContain){
                if(self::stringContains($string,$doesContain)){
                    return true;
                }
            }
        }else{

            if (!function_exists('str_contains')) {
                return strpos($string, $doesContains) !== false;
            } else {
                if (str_contains($string, $doesContains)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        //RewriteRule ^([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$1$2$3.jpg [L]
        //RewriteRule ^([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$1$2$3$4.jpg [L]
        //RewriteRule ^([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$1$2$3$4$5.jpg [L]
        //RewriteRule ^([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$1$2$3$4$5$6.jpg [L]
        //RewriteRule ^([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$5/$1$2$3$4$5$6$7.jpg [L]
        //RewriteRule ^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$5/$6/$1$2$3$4$5$6$7$8.jpg [L]
        //RewriteRule ^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$3/$4/$5/$6/$7/$1$2$3$4$5$6$7$8$9.jpg [L]
        //RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/c/$1$2$3.jpg [L]
        //RewriteRule ^c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/c/$1$2.jpg [L]
        //RewriteRule ^images_ie/?([^/]+)\.(jpe?g|png|gif)$ js/jquery/plugins/fancybox/images/$1.$2 [L]

        // Basic static file
        if (is_file($staticFilePath = "{$sitePath}/{$uri}")) {
            return $staticFilePath;
        }


        //RewriteRule ^([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$1$2$3.jpg [L]
        if (preg_match('/([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[1]}{$matches[2]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }


        //RewriteRule ^([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1/$2/$1$2$3$4.jpg [L]
        if (preg_match('/([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[1]}{$matches[2]}{$matches[3]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }


        // rewrite ^/([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$1$2$3$4.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }


        // rewrite ^/([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$1$2$3$4$5.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$5/$1$2$3$4$5$6.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[5]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}{$matches[6]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$5/$6/$1$2$3$4$5$6$7.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[5]}/{$matches[6]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}{$matches[6]}{$matches[7]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$5/$6/$7/$1$2$3$4$5$6$7$8.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[5]}/{$matches[6]}/{$matches[7]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}{$matches[6]}{$matches[7]}{$matches[8]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$5/$6/$7/$8/$1$2$3$4$5$6$7$8$9.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[5]}/{$matches[6]}/{$matches[7]}/{$matches[8]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}{$matches[6]}{$matches[7]}{$matches[8]}{$matches[9]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        if (preg_match('/c\/([0-9]*)-category_default\/.*\.jpg/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/c/{$matches[1]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return string
     */
    public function frontControllerPath(string $sitePath,string $siteName,string $uri): ?string
    {
        if (preg_match('/^\/api\/?(.*)$/', $uri, $matches)){ //API
            $staticFilePath = "/webservice/dispatcher.php";
            $_GET['url'] = $matches[1];

            return $sitePath.$staticFilePath;
        }
        $parts = explode('/',$uri);
        if(!self::stringContains($uri,self::$ps_exclusions) && is_file($sitePath.$uri) && file_exists($sitePath.$uri)){
            $_SERVER['SCRIPT_FILENAME'] = $sitePath.$uri;
            return $sitePath.$uri;
        }
        if(isset($parts[1]) && $parts[1] !='' && file_exists($adminIdex = $sitePath . '/'. $parts[1] .'/index.php')){
            $_SERVER['SCRIPT_FILENAME'] = $adminIdex;
            $_SERVER['SCRIPT_NAME'] = '/'. $parts[1] .'/index.php';

            if(isset($_GET['controller']) || isset($_GET['tab'])){
                return $adminIdex;
            }
            return $adminIdex;
        }
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/index.php';
        return $sitePath . '/index.php';
    }
}
