<?php

namespace App\Services;

class EncryptionService
{
    protected string $cipher = 'AES-256-CBC';

    /**
     * Encrypt the given value.
     *
     * @param string $value
     * @param string $key The derived encryption key (32 bytes for AES-256)
     * @return array{encrypted: string, iv: string}
     */
    public function encrypt(string $value, string $key): array
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($value, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);

        return [
            'encrypted' => base64_encode($encrypted),
            'iv' => bin2hex($iv), // 16 bytes * 2 = 32 hex chars
        ];
    }

    /**
     * Decrypt the given value.
     *
     * @param string $encryptedValue Base64 encoded
     * @param string $iv Hex encoded
     * @param string $key
     * @return string|false
     */
    public function decrypt(string $encryptedValue, string $iv, string $key): string|false
    {
        $decodedIv = hex2bin($iv);
        $decodedValue = base64_decode($encryptedValue);

        return openssl_decrypt($decodedValue, $this->cipher, $key, OPENSSL_RAW_DATA, $decodedIv);
    }
}
