<?php
namespace phpspider\common;

class Secret
{

   public  function createParam($text)
    {

        $nonce   = '0CoJUm6Qyw8W8jud';
        $secKey  = 'FFFFFFFFFFFFFFFF';
        $encText = $this->AES_encrypt(
            $this->AES_encrypt($text, $nonce),
            $secKey);
        $encSecKey = '257348aecb5e556c066de214e531faadd1c55d814f9be95fd06d6bff9f4c7a41f831f6394d5a3fd2e3881736d94a02ca919d952872e7d0a50ebfa1769a7a62d512f5f1ca21aec60bc3819a9c3ffca5eca9a0dba6d6f7249b06f5965ecfff3695b54e1c28f3f624750ed39e7de08fc8493242e26dbc4484a01c76f739e135637c';
        return [
            'params'    => $encText,
            'encSecKey' => $encSecKey,
        ];
    }

    private function AES_encrypt($text, $key, $iv = '0102030405060708')
    {
        $pad       = 16 - strlen($text) % 16;
        $text      = $text . str_repeat(chr($pad), $pad);
        $encryptor = openssl_encrypt($text, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encryptor);
    }
}