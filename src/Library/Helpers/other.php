<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/*
String to Url
 */
if (!function_exists('pz_word_format_url') && !in_array('pz_word_format_url', config('helper_except', []))) {
    function pz_word_format_url($str = ""):string
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return strtolower(preg_replace(
            array('/[\s\-\/\\\?\(\)\~\.\[\]\%\*\#\@\$\^\&\!\'\"\`\;\:]+/'),
            array('-'),
            strtolower($str)
        ));
    }
}


if (!function_exists('pz_url_render') && !in_array('pz_url_render', config('helper_except', []))) {
    /*
    url render
     */
    function pz_url_render($string = ""):string
    {
        $arrCheckRoute = explode('route::', $string);
        $arrCheckUrl = explode('admin::', $string);

        if (count($arrCheckRoute) == 2) {
            $arrRoute = explode('::', $string);
            if (Str::startsWith($string, 'route::admin')) {
                if (isset($arrRoute[2])) {
                    return pz_route_admin($arrRoute[1], explode(',', $arrRoute[2]));
                } else {
                    return pz_route_admin($arrRoute[1]);
                }
            } else {
                if (isset($arrRoute[2])) {
                    return pz_route($arrRoute[1], explode(',', $arrRoute[2]));
                } else {
                    return pz_route($arrRoute[1]);
                }
            }
        }

        if (count($arrCheckUrl) == 2) {
            $string = Str::start($arrCheckUrl[1], '/');
            $string = PZ_ADMIN_PREFIX . $string;
            return url($string);
        }
        return url($string);
    }
}


if (!function_exists('pz_html_render') && !in_array('pz_html_render', config('helper_except', []))) {
    /*
    Html render
     */
    function pz_html_render($string = ""):string
    {
        if(!is_string($string)) {
            return $string;
        }
        $string = htmlspecialchars_decode($string);
        return $string;
    }
}

if (!function_exists('pz_word_format_class') && !in_array('pz_word_format_class', config('helper_except', []))) {
    /*
    Format class name
     */
    function pz_word_format_class($word = ""):string
    {
        if(!is_string($word)) {
            return $word;
        }
        $word = Str::camel($word);
        $word = ucfirst($word);
        return $word;
    }
}

if (!function_exists('pz_word_limit') && !in_array('pz_word_limit', config('helper_except', []))) {
    /*
    Truncates words
     */
    function pz_word_limit($word = "", int $limit = 20, string $arg = ''):string
    {
        $word = Str::limit($word, $limit, $arg);
        return $word;
    }
}

if (!function_exists('pz_token') && !in_array('pz_token', config('helper_except', []))) {
    /*
    Create random token
     */
    function pz_token(int $length = 32)
    {
        $token = Str::random($length);
        return $token;
    }
}

if (!function_exists('pz_report') && !in_array('pz_report', config('helper_except', []))) {
    /*
    Handle report
     */
    function pz_report($msg = "", array $ext = [])
    {
        $msg = pz_time_now(config('app.timezone')).' ('.config('app.timezone').'):'.PHP_EOL.$msg.PHP_EOL;
        if (!in_array('slack', $ext)) {
            if (config('logging.channels.slack.url')) {
                try {
                    \Log::channel('slack')->emergency($msg);
                } catch (\Throwable $e) {
                    $msg .= $e->getFile().'- Line: '.$e->getLine().PHP_EOL.$e->getMessage().PHP_EOL;
                }
            }
        }
        \Log::error($msg);
    }
}


if (!function_exists('pz_handle_exception') && !in_array('pz_handle_exception', config('helper_except', []))) {
    /*
    Process msg exception
     */
    function pz_handle_exception(\Throwable $exception)
    {
        $msg = "```". $exception->getMessage().'```'.PHP_EOL;
        $msg .= "```IP:```".request()->ip().PHP_EOL;
        $msg .= "*File* `".$exception->getFile()."`, *Line:* ".$exception->getLine().", *Code:* ".$exception->getCode().PHP_EOL.'URL= '.url()->current();
        if (function_exists('pz_report') && $msg) {
            pz_report($msg);
        }
    }
}


if (!function_exists('pz_push_include_view') && !in_array('pz_push_include_view', config('helper_except', []))) {
    /**
     * Push view
     *
     * @param   [string]  $position
     * @param   [string]  $pathView
     *
     */
    function pz_push_include_view($position = "", string $pathView = "")
    {
        $includePathView = config('pz_include_view.'.$position, []);
        $includePathView[] = $pathView;
        config(['pz_include_view.'.$position => $includePathView]);
    }
}


if (!function_exists('pz_push_include_script') && !in_array('pz_push_include_script', config('helper_except', []))) {
    /**
     * Push script
     *
     * @param   [string]  $position
     * @param   [string]  $pathScript
     *
     */
    function pz_push_include_script($position, $pathScript)
    {
        $includePathScript = config('pz_include_script.'.$position, []);
        $includePathScript[] = $pathScript;
        config(['pz_include_script.'.$position => $includePathScript]);
    }
}


/**
 * convert datetime to date
 */
if (!function_exists('pz_datetime_to_date') && !in_array('pz_datetime_to_date', config('helper_except', []))) {
    function pz_datetime_to_date($datetime, $format = 'Y-m-d')
    {
        if (empty($datetime)) {
            return null;
        }
        return  date($format, strtotime($datetime));
    }
}


if (!function_exists('admin') && !in_array('admin', config('helper_except', []))) {
    /**
     * Admin login information
     */
    function admin()
    {
        return auth()->guard('admin');
    }
}

if (!function_exists('pz_sync_cart') && !in_array('pz_sync_cart', config('helper_except', []))) {
    /**
     * Sync data cart
     */
    function pz_sync_cart($userId)
    {
        //Process sync cart data and session
        $cartDB = \PZone\Core\Library\ShoppingCart\CartModel::where('identifier', $userId)
            ->where('store_id', config('app.storeId'))
            ->get()->keyBy('instance');
        $arrayInstance = ['default', 'wishlist', 'compare'];
        if ($cartDB) {
            foreach ($cartDB as $instance => $cartInstance) {
                $arrayInstance = array_diff($arrayInstance, [$instance]);
                $content = json_decode($cartInstance->content, true);
                if ($content) {
                    foreach ($content as $key => $dataItem) {
                        \Cart::instance($instance)->add($dataItem);
                    }
                }
            }
        }

        if ($arrayInstance) {
            foreach ($arrayInstance as  $instance) {
                if (\Cart::instance($instance)->content()->count()) {
                    \Cart::instance($instance)->saveDatabase($userId);
                }
            }
        }
    }
}

if (!function_exists('pz_time_now') && !in_array('pz_time_now', config('helper_except', []))) {
    /**
     * Return object carbon
     */
    function pz_time_now($timezone = null)

    {
        return (new \Carbon\Carbon)->now($timezone);
    }
}

if (!function_exists('pz_request') && !in_array('pz_request', config('helper_except', []))) {
    /**
     * Return object carbon
     */
    function pz_request($key = "", $default = "", string $type = "")
    {

        if ($type == 'string') {
            if (is_array(request($key, $default))) {
                return 'array';
            }
        }

        if ($type == 'array') {
            if (is_string(request($key, $default))) {
                return [request($key, $default)];
            }
        }

        return request($key, $default);
    }
}
