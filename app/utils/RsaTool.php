<?php

namespace app\utils;

/**
 * RSA工具库
 */
trait RsaTool
{
    /**
     * RSA加密
     *
     * @param array $data
     * @return string
     */
    protected function encryptRSA(array $data): string
    {
        // 公钥
        $publicKey = config('rsa.public_key');
        openssl_public_encrypt(json_encode($data), $encrypted, $publicKey);
        return base64_encode($encrypted);
    }

    /**
     * RSA解密
     *
     * @param string $base64Encrypted
     * @return array
     */
    protected function decryptRSA(string $base64Encrypted): array
    {
        // 私钥
        $privateKey = config('rsa.private_key');
        $encrypted  = base64_decode($base64Encrypted);
        openssl_private_decrypt($encrypted, $decrypted, $privateKey);
        return json_decode($decrypted, true);
    }
}