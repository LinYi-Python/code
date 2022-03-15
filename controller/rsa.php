<?php
/**
 * @version 1.0
 * RSA AES 解密类
 * @copyright denglu1 tech
 */
class EncryptUtil {

    const PUBLICKKEY = "-----BEGIN PUBLIC KEY-----
                        MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxaBF/WxVErrL1q+Txw1w
                        dlkjhbyT8eS7DXE989IJm+TT9N9bvA+lbuHThgNrKSqjOQDl1DynKg8nM3+eXS/6
                        wt+8lLi5FbINOxkwZXw9Jf/HgZ9/6AmwxJOq3d4o35mE5h0VogPGimJ4399unU16
                        AQ5/tWQDIkAZ9FTe74cdD+a/jG/8XVeD9TZh8pEA5zIGb5YLJb9Ig3OgSJOa1VC6
                        2giAsd4BC8TOXnWe/bgLddWYwF1ultpcQo8N1R2hSSjZwoWQfBfhjx87HCLePfP6
                        5TO6OtHvt8HAegJowcxbR4fskiPOTmP6BDROa6yJYh+fhd18SPDeURxDe2w8Ig8k
                        jQIDAQAB
                        -----END PUBLIC KEY-----";

    const PRIVATEKEY = "-----BEGIN PRIVATE KEY-----
                        MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDFoEX9bFUSusvW
                        r5PHDXB2WSOFvJPx5LsNcT3z0gmb5NP031u8D6Vu4dOGA2spKqM5AOXUPKcqDycz
                        f55dL/rC37yUuLkVsg07GTBlfD0l/8eBn3/oCbDEk6rd3ijfmYTmHRWiA8aKYnjf
                        326dTXoBDn+1ZAMiQBn0VN7vhx0P5r+Mb/xdV4P1NmHykQDnMgZvlgslv0iDc6BI
                        k5rVULraCICx3gELxM5edZ79uAt11ZjAXW6W2lxCjw3VHaFJKNnChZB8F+GPHzsc
                        It498/rlM7o60e+3wcB6AmjBzFtHh+ySI85OY/oENE5rrIliH5+F3XxI8N5RHEN7
                        bDwiDySNAgMBAAECggEBAIUY/kpBbcJhf+sk8Nf8myN5wUzOxq0BKWFSRTEy4jnW
                        EVI4I+Yye26ENRtqrf9ZsfgwPJmGB+dxsSsj5hpIuBAK800OY8jS08pbmpae5+fx
                        rgIE0/YIoL6j3U3z039UO4dnSyyEcnC9BT9xkWPrzCFuDGCz7NBib5h4mHSlgNM1
                        m7kK2/Tq3m7Dnj00wvdd9p5AsJzEKFdT3SbRfofYWxgMfEjAXkUpYoR2cKbASiDa
                        NZl/vs/daA5zhLB85VZQr/opaJJ2bR2KWzMqG11p6+KStPTI71DNF5mpwGR+QaWT
                        S9ykWHjV68Z9esBmbKheTKJEfwX48/J7uW1wxyb6D9kCgYEA68HOpZd4OWwyIFjE
                        VTw8lHd0kZIFeWXfVeE42/RWkwIK1a/6zmbC1NtDh1/vZkFJNg4wiovw/I8zXxc9
                        Q8yEzTR2c3qB2Yj54n8qynOLJBoUsVqektoKLwgLvlYVHegHXIpW5+k9ZuEeEUEQ
                        pMtzyjJFr6YvzEf0fdpm1Qo0r4sCgYEA1phOXr5Q29a2h9KBiVe8MBGJ6od7viLb
                        JvNCE4QgMrje3jzA7PFoPfEQrYcQx9AgGNoqDv/m50sraI/Qt8M0D6WuHyE2orLH
                        phebqzU/3zlIZybbVHUnB7Dlrtm841uxIo/SES477UFDQ0q77QpUP2S7LvRZPU6V
                        SbYS6cql/0cCgYEAlI/SeoLSnczSYZPOVK/atOv5punPTUqmy+VbATAdhhHZZgK5
                        F2umBqANE7eekl89lTSn3gaFW4GZq6hnUNwGK5GNuwNN5Bju7o2YF29sFNnihEhI
                        eN2xYSW+0LhKIkheqy/5x7YwnS3q1pCbvlz+oEHBrAgbQq1tIgmIl2MRBDUCgYAk
                        drzaXHxg3psleR+Jtt7DTDejImJkbIfQloUjy8+szr0QBkLCAEM2Q0ASPGEdkr/h
                        eTCsW89gfeViXw2YLBMslXqAz6P5AWfrUReif+nahGFJJdEfCMkZBXYBv/3YpDv1
                        I3sOcEy+g9hqFsjX/mlSXEtyLxL619+GJuoPz99lAQKBgQDDMjcILvcjfLtIFYhM
                        twxIjA+RjqFThcfhy9/nfUTDrJxMa2j17+fJOEBcQt+LJsXvf0D/jveotqXdWpVo
                        cr2WFTfsZs3M5QVfWSLFBsn7h+frbbpjp9HPXe9BqmnVnc16m+yaRbiVcxCOeu6g
                        WIGSmmI6LZwfLp/0exBqv7cWlA==
-----END PRIVATE KEY-----";

    /**
     * aes编码
     * @param  string $sPassword 密码
     * @param  string $sData     明文
     * @param  string $sIV       加密向量
     * @param  string $sMethod   加密方式(默认：AES-256-CFB)
     * @return string            密文
     */
    public static function aesEncrypt($sPassword, $sData, $sMethod = "AES-256-CFB8") {
        $sIV = chr(0x16) . chr(0x61) . chr(0x0F) . chr(0x3A) . chr(0x37) . chr(0x3D) . chr(0x1B) . chr(0x51) . chr(0x4A) . chr(0x39) . chr(0x5A) . chr(0x79) . chr(0x29) . chr(0x08) . chr(0x01) . chr(0x22);
        $sPassword = base64_decode($sPassword);
        $sPassword = substr(hash('sha256', $sPassword, true), 0, 32);
        $sEncrypted = base64_encode(openssl_encrypt($sData, $sMethod, $sPassword, OPENSSL_RAW_DATA, $sIV));
        return $sEncrypted;
    }

    /**
     * aes解码
     * @param  string $sPassword 密码
     * @param  string $sData     密文
     * @param  string $sIV       加密向量
     * @param  string $sMethod   加密方式(默认：AES-256-CFB)
     * @return string            明文
     */
    public static function aesDecrypt($sPassword, $sData, $sMethod = "AES-256-CFB8") {
        $sIV = chr(0x16) . chr(0x61) . chr(0x0F) . chr(0x3A) . chr(0x37) . chr(0x3D) . chr(0x1B) . chr(0x51) . chr(0x4A) . chr(0x39) . chr(0x5A) . chr(0x79) . chr(0x29) . chr(0x08) . chr(0x01) . chr(0x22);
        $sPassword = base64_decode($sPassword);
        $sDecrypted = openssl_decrypt(base64_decode($sData), $sMethod, $sPassword, OPENSSL_RAW_DATA, $sIV);
        return $sDecrypted;
    }

    /**
     * rsa公钥加密
     * @param  string $sPublicKey 公钥
     * @param  string $sData      明文
     * @return string             密文
     */
    public static function rsaPublicKeyEncrypt($sPublicKey, $sData) {
        $res = openssl_get_publickey($sPublicKey);
        //$sData = base64_encode($sData);
        openssl_public_encrypt($sData, $sEncrypt, $res);
        openssl_free_key($res);
        $sEncrypt = base64_encode($sEncrypt);
        return $sEncrypt;
    }

    /**
     * rsa密钥解密
     * @param  string $sPrivateKey 密钥
     * @param  string $sData       密文
     * @return string              明文
     */
    public static function rsaPrivateKeyDecrypt($sPrivateKey, $sData) {
        $res = openssl_get_privatekey($sPrivateKey);
        $sEncrypt = base64_decode($sData);
        openssl_private_decrypt($sEncrypt, $sDecrypt, $res);
        openssl_free_key($res);
        return $sDecrypt;
    }
    /**
     * @param string $sTextz            明文
     * @param string $sRasEncryptedKey  经过RSA加密后的密文
     * @return string                   明文
     */
    public static function rsa_aes_decrypt($sText, $sRasEncryptedKey) {
        return self::aesDecrypt(self::rsaPrivateKeyDecrypt(self::PRIVATEKEY, $sRasEncryptedKey), $sText);
    }
}