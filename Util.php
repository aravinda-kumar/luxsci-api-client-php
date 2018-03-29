<?php

namespace LuxSciApiClient;


class Util
{
    /**
     * URL-encode text
     * @param $a
     * @return mixed
     */
    public static function url_encode($a)
    {
        $b = preg_replace("([+?&=#<> %\"']|[^\x20-\x7E])", "%" . Util::to_hex(ord('$0')), $a);
        return $b;
    }

    /**
     * Convert number to 2-digit HEX
     * @param $n
     * @return string
     */
    public static function to_hex($n)
    {
        $h = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        return $h[intval($n / 16)] . $h[intval($n % 16)];
    }

    /**
     * @param $ch
     * @return array
     */
    public static function curl_exec($ch)
    {
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'response' => $response];
    }
}