<?php

namespace AppBundle\Helper;

class EncryptHelper
{
    private const SECRET_KEY = 'AA74CDCC2BBRT935136HH7B63C27';
    private const SECRET_IV = '5fgf5HJ5g27';
    private const CIPHER = 'AES-256-CBC';

    public function stringEncrypt(string $string): string
    {
        [$key, $iv] = $this->deriveKeyAndIv();
        $output = openssl_encrypt($string, self::CIPHER, $key, 0, $iv);

        return base64_encode($output);
    }

    public function stringDecrypt(string $string): string|false
    {
        [$key, $iv] = $this->deriveKeyAndIv();

        return openssl_decrypt(base64_decode($string), self::CIPHER, $key, 0, $iv);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function deriveKeyAndIv(): array
    {
        return [
            hash('sha256', self::SECRET_KEY),
            substr(hash('sha256', self::SECRET_IV), 0, 16),
        ];
    }
}