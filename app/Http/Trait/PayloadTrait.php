<?php

namespace App\Http\Trait;

use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

trait PayloadTrait
{
    public function encrypt($form)
    {
        $formJson = json_encode($form);
        $key = 'abcdefghijuklmno0123456789012345';

        // Encrypt using AES CBC
        $aes = new AES('cbc');
        $aes->setKey($key);

        // Generate IV
        $iv = Random::string($aes->getBlockLength() >> 3);
        $aes->setIV($iv);

        // Encrypt the data
        $encryptedData = $aes->encrypt($formJson);

        // Combine IV and encrypted data, then base64 encode
        $payload = base64_encode($iv . $encryptedData);

        return $payload;
    }
}

