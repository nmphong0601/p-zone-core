<?php

if (!function_exists('pz_get_all_plugin') && !in_array('pz_get_all_plugin', config('helper_except', []))) {
    /**
     * Get all class plugin
     *
     * @param   [string]  $code  Payment, Shipping
     *
     * @return  [array]
     */
    function pz_get_all_plugin(string $code = "")
    {
        $code = pz_word_format_class($code);
        $arrClass = [];
        $dirs = array_filter(glob(app_path() . '/Plugins/' . $code . '/*'), 'is_dir');
        if ($dirs) {
            foreach ($dirs as $dir) {
                $tmp = explode('/', $dir);
                $nameSpace = '\App\Plugins\\' . $code . '\\' . end($tmp);
                if (file_exists($dir . '/AppConfig.php')) {
                    $arrClass[end($tmp)] = $nameSpace;
                }
            }
        }
        return $arrClass;
    }
}

if (!function_exists('pz_get_plugin_installed') && !in_array('pz_get_plugin_installed', config('helper_except', []))) {
    /**
     * Get all class plugin
     *
     * @param   [string]  $code  Payment, Shipping
     *
     */
    function pz_get_plugin_installed($code = null, $onlyActive = true)
    {
        return \PZone\Core\Admin\Models\AdminConfig::getPluginCode($code, $onlyActive);
    }
}




if (!function_exists('pz_get_all_plugin_actived') && !in_array('pz_get_all_plugin_actived', config('helper_except', []))) {
    /**
     * Get all class plugin actived
     *
     * @param   [string]  $code  Payment, Shipping
     *
     * @return  [array]
     */
    function pz_get_all_plugin_actived(string $code)
    {
        $code = pz_word_format_class($code);
        
        $pluginsActived = [];
        $allPlugins = pz_get_all_plugin($code);
        if (count($allPlugins)) {
            foreach ($allPlugins as $keyPlugin => $plugin) {
                if (pz_config($keyPlugin) == 1) {
                    $pluginsActived[$keyPlugin] = $plugin;
                }
            }
        }
        return $pluginsActived;
    }
}


    /**
     * Get namespace plugin controller
     *
     * @param   [string]  $code  Shipping, Payment,..
     * @param   [string]  $key  Paypal,..
     *
     * @return  [array]
     */

    if (!function_exists('pz_get_class_plugin_controller') && !in_array('pz_get_class_plugin_controller', config('helper_except', []))) {
        function pz_get_class_plugin_controller(string $code, string $key = "")
        {
            if (!$key) {
                return null;
            }
            
            $code = pz_word_format_class($code);
            $key = pz_word_format_class($key);

            $nameSpace = pz_get_plugin_namespace($code, $key);
            $nameSpace = $nameSpace . '\Controllers\FrontController';

            return $nameSpace;
        }
    }


    /**
     * Get namespace plugin config
     *
     * @param   [string]  $code  Shipping, Payment,..
     * @param   [string]  $key  Paypal,..
     *
     * @return  [array]
     */
    if (!function_exists('pz_get_class_plugin_config') && !in_array('pz_get_class_plugin_config', config('helper_except', []))) {
        function pz_get_class_plugin_config(string $code = "", string $key = "")
        {
            $code = pz_word_format_class($code);
            $key = pz_word_format_class($key);

            $nameSpace = pz_get_plugin_namespace($code, $key);
            $nameSpace = $nameSpace . '\AppConfig';

            return $nameSpace;
        }
    }

    /**
     * Get namespace module
     *
     * @param   [string]  $code  Block, Cms, Payment, shipping..
     * @param   [string]  $key  Content,Paypal, Cash..
     *
     * @return  [array]
     */
    if (!function_exists('pz_get_plugin_namespace') && !in_array('pz_get_plugin_namespace', config('helper_except', []))) {
        function pz_get_plugin_namespace(string $code = "", string $key = "")
        {
            $code = pz_word_format_class($code);
            $key = pz_word_format_class($key);
            $nameSpace = '\App\Plugins\\'.$code.'\\' . $key;
            return $nameSpace;
        }
    }

    /**
     * Check plugin and template compatibility with S-cart version
     *
     * @param   string  $versionsConfig  [$versionsConfig description]
     *
     * @return  [type]                   [return description]
     */
    if (!function_exists('pz_plugin_compatibility_check') && !in_array('pz_plugin_compatibility_check', config('helper_except', []))) {
        function pz_plugin_compatibility_check(string $versionsConfig) {
            $arrVersionPZone = explode('|', $versionsConfig);
            return in_array(config('p-zone.core'), $arrVersionPZone);
        }
    }