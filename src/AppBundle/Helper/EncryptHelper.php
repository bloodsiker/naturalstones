<?php

namespace AppBundle\Helper;

/**
 * Class EncryptHelper
 */
class EncryptHelper
{
    /**
     * @param $string
     *
     * @return string
     */
    public function stringEncrypt($string)
    {
        $secret_key = 'AA74CDCC2BBRT935136HH7B63C27';
        $secret_iv = '5fgf5HJ5g27';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_encrypt($string, "AES-256-CBC", $key, 0, $iv);

        return base64_encode($output);
    }

    /**
     * @param $string
     *
     * @return false|string
     */
    public function stringDecrypt($string)
    {
        $secret_key = 'AA74CDCC2BBRT935136HH7B63C27';
        $secret_iv = '5fgf5HJ5g27';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        return openssl_decrypt(base64_decode($string), "AES-256-CBC", $key, 0, $iv);
    }
}
