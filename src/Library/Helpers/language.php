<?php

use PZone\Core\Front\Models\ShopLanguage;
use Illuminate\Support\Str;

if (!function_exists('pz_language_all') && !in_array('pz_language_all', config('helper_except', []))) {
    //Get all language
    function pz_language_all()
    {
        return ShopLanguage::getListActive();
    }
}

if (!function_exists('pz_languages') && !in_array('pz_languages', config('helper_except', []))) {
    /*
    Render language
    WARNING: Dont call this function (or functions that call it) in __construct or midleware, it may cause the display language to be incorrect
     */
    function pz_languages($locale)
    {
        $languages = \PZone\Core\Front\Models\Languages::getListAll($locale);
        return $languages;
    }
}

if (!function_exists('pz_language_replace') && !in_array('pz_language_replace', config('helper_except', []))) {
    /*
    Replace language
     */
    function pz_language_replace(string $line, array $replace)
    {
        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }
        return $line;
    }
}


if (!function_exists('pz_language_render') && !in_array('pz_language_render', config('helper_except', []))) {
    /*
    Render language
    WARNING: Dont call this function (or functions that call it) in __construct or midleware, it may cause the display language to be incorrect
     */
    function pz_language_render($string, array $replace = [], $locale = null)
    {
        $locale = $locale ? $locale : pz_get_locale();
        $languages = pz_languages($locale);
        return !empty($languages[$string]) ? pz_language_replace($languages[$string], $replace): trans($string, $replace);
    }
}


if (!function_exists('pz_language_quickly') && !in_array('pz_language_quickly', config('helper_except', []))) {
    /*
    Language quickly
     */
    function pz_language_quickly($string, $default = null)
    {
        $locale = pz_get_locale();
        $languages = pz_languages($locale);
        return !empty($languages[$string]) ? $languages[$string] : (\Lang::has($string) ? trans($string) : $default);
    }
}

if (!function_exists('pz_get_locale') && !in_array('pz_get_locale', config('helper_except', []))) {
    /*
    Get locale
    */
    function pz_get_locale()
    {
        return app()->getLocale();
    }
}


if (!function_exists('pz_lang_switch') && !in_array('pz_lang_switch', config('helper_except', []))) {
    /**
     * Switch language
     *
     * @param   [string]  $lang
     *
     * @return  [mix]
     */
    function pz_lang_switch($lang = null)
    {
        if (!$lang) {
            return ;
        }

        $languages = pz_language_all()->keys()->all();
        if (in_array($lang, $languages)) {
            app()->setLocale($lang);
            session(['locale' => $lang]);
        } else {
            return abort(404);
        }
    }
}
