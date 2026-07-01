<?php

/**
 * print_r + <pre>
 *
 * @param mix $data 內容
 * @return string
 */
if (!function_exists('pre_p')) {
    function pre_p($data) {
        echo '<pre>';
        print_r($data);
        echo '<hr>';
    }
}

/**
 *  var_dump + <pre>
 *
 * @param mix $data 內容
 * @return string
 */
if (!function_exists('pre_v')) {
    function pre_v($data) {
        echo '<pre>';
        var_dump($data);
        echo '<hr>';
    }
}

/**
 *  base64轉碼
 *
 * @param string $data 內容
 * @return string
 */
if(!function_exists('base64_reverse')){
    function base64_reverse($data) {
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        return base64_decode($data);
    }
}

/**
 * 加密
 *
 * @param string $data
 * @param string $key
 * @return string
 */
if (!function_exists('data_encode')) {
    function data_encode($data, $key) {
        $method = 'aes-256-cbc';
        if (in_array($method, openssl_get_cipher_methods())) {
            if (empty($data) || is_array($data)) {
                return '';
            } else {
                $encryption_key = base64_decode($key);
                $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
                $encrypted = openssl_encrypt($data, $method, $encryption_key, 0, $iv);
                return base64_encode($encrypted . '::' . $iv);
            }
        } else {
            die('不支持該加密算法!');
        }
    }
}

/**
 * 解密
 *
 * @param string $data
 * @param string $key
 * @return string
 */
if (!function_exists('data_decode')) {
    function data_decode($data, $key) {
        $method = 'aes-256-cbc';
        if (in_array($method, openssl_get_cipher_methods())) {
            if (empty($data) || is_array($data)) {
                return '';
            } else {
                $encryption_key = base64_decode($key);
                list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
                return openssl_decrypt($encrypted_data, $method, $encryption_key, 0, $iv);
            }
        } else {
            die('不支持該加密算法!');
        }
    }
}

/**
 * ip
 *
 * @param string $key
 * @return array
 */
if (!function_exists('get_user_ip')) {
    function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

/**
 * 亂數
 *
 * @param string $key
 * @return array
 */
if (!function_exists('random_string')) {
    function random_string($length = 4, $type = '') {
        $alpha = 'ABCDEFGHIJKLMNOPQRTUVWXYZ';
        $numeric = '123456789';
        $symbols = '!@#$%&=+-*';
        $random_string = '';

        switch ($type) {
            case 'mix':
                $rand_word = $alpha . $numeric . $symbols;
                break;

            case 'alpha':
                $rand_word = $alpha;
                break;

            case 'numeric':
                $rand_word = $numeric;
                break;

            case 'symbols':
                $rand_word = $symbols;
                break;

            default:
                $rand_word = $alpha . $numeric;
                break;
        }

        $word_array = str_split(str_shuffle($rand_word));
        $rand = array_rand($word_array, $length);

        foreach ($rand as $k) {
            $random_string .= $word_array[$k];
        }

        return $random_string;
    }
}

/**
 * 去除小數點0 + 四捨不入
 *
 * @param mixed $value
 * @param int $decimal
 * @param string $decimalpoint
 * @param string $separator
 * @return string
 */
if (!function_exists('number_format_upgrade')) {
    function number_format_upgrade($value, $decimal = 6, $decimalpoint = '.', $separator = '') {
        // 轉型 to string
        $value = (string) $value;

        // 檢查是否為科學符號
        $pattern = "/.e|.E/";
        preg_match($pattern, $value, $matches);
        if (!empty($matches)) {
            $pos = strpos($value, 'E'); // 科學符號位置
            $number = substr($value, $pos + 1);
            if ($number < 0) {
                $posPoint = strpos($value, '.'); // 小數點位置
                $afterPointCount = strlen(substr($value, $posPoint + 1, $pos - ($posPoint + 1))); // 計算小數點到科學符號間有幾位數
                $maxDecimal = abs($number) + $afterPointCount; // 轉乘絕對值 + 數字溢位長度
            } else {
                $maxDecimal = 0;
            }
        } else {
            // 檢查是否有小數點
            $posPoint = strpos($value, '.');
            if ($posPoint !== false) {
                $maxDecimal = strlen(substr($value, $posPoint + 1));
            } else {
                $maxDecimal = 0;
            }
        }

        $value = sprintf("%." . ($maxDecimal) . "f", $value); // string

        $pos = strpos($value, '.');
        if ($pos === false) {
            return number_format($value, 0, $decimalpoint, $separator);
        } else {
            $float = rtrim(substr($value, $pos + 1, $decimal), '0'); // 取小數點後數字 跟 去0
            $float = empty($float) ? '' : '.' . $float; // 判斷是否有東西
            return number_format(floor($value), 0, $decimalpoint, $separator) . $float;
        }
    }
}

/**
 * mnemonic check
 *
 * @param string $key
 * @return array
 */
if (!function_exists('mnemonic_check')) {
    function mnemonic_check($val) {
        return count(explode(' ', $val)) == 12;
    }
}
