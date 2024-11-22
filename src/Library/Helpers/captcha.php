<?php

if (!function_exists('pz_captcha_method') && !in_array('pz_captcha_method', config('helper_except', []))) {
    function pz_captcha_method()
    {
        //If function captcha disable or dont setup
        if (empty(pz_config('captcha_mode'))) {
            return null;
        }

        // If method captcha selected
        if (!empty(pz_config('captcha_method'))) {
            $moduleClass = pz_config('captcha_method');
            //If class plugin captcha exist
            if (class_exists($moduleClass)) {
                //Check plugin captcha disable
                $key = (new $moduleClass)->configKey;
                if (pz_config($key)) {
                    return (new $moduleClass);
                } else {
                    return null;
                }
            }
        }
        return null;
    }
}

if (!function_exists('pz_captcha_page') && !in_array('pz_captcha_page', config('helper_except', []))) {
    function pz_captcha_page():array
    {
        if (empty(pz_config('captcha_page'))) {
            return [];
        }

        if (!empty(pz_config('captcha_page'))) {
            return json_decode(pz_config('captcha_page'));
        }
    }
}

if (!function_exists('pz_get_plugin_captcha_installed') && !in_array('pz_get_plugin_captcha_installed', config('helper_except', []))) {
    /**
     * Get all class plugin captcha installed
     *
     * @param   [string]  $code  Payment, Shipping
     *
     */
    function pz_get_plugin_captcha_installed($onlyActive = true)
    {
        $listPluginInstalled =  \PZone\Core\Admin\Models\AdminConfig::getPluginCaptchaCode($onlyActive);
        $arrPlugin = [];
        if ($listPluginInstalled) {
            foreach ($listPluginInstalled as $key => $plugin) {
                $keyPlugin = pz_word_format_class($plugin->key);
                $pathPlugin = app_path() . '/Plugins/Other/'.$keyPlugin;
                $nameSpaceConfig = '\App\Plugins\Other\\'.$keyPlugin.'\AppConfig';
                if (file_exists($pathPlugin . '/AppConfig.php') && class_exists($nameSpaceConfig)) {
                    $arrPlugin[$nameSpaceConfig] = pz_language_render($plugin->detail);
                }
            }
        }
        return $arrPlugin;
    }
}
