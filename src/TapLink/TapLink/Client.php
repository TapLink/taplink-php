<?php

namespace TapLink\TapLink;

use \GuzzleHttp\Client as HTTPClient;
use \GuzzleHttp\TransferStats;

class Client
{
    public $appID;
    public $userAgent;
    public $servers;

    public static $hashAlgorithm = 'sha512';
    public static $defaultServer = 'api.taplink.co';

    function __construct($appID)
    {
        $this->appID = $appID;
        $this->userAgent = 'TapLink/1.0 php/'.phpversion();
        $this->servers = [self::$defaultServer];
    }

    public function getSalt($hash1Hex, $versionID = null)
    {
        return $this->get(sprintf('%s/%s/%s', $this->appID, $hash1Hex, $versionID ?: ''));
    }

    public function verifyPassword($hash1Hex, $hash2ExpectedHex, $versionId = null)
    {
        $res = $this->getSalt($hash1Hex, $versionId);
        if (!$res->err) {
            $res->hash2Hex = hash_hmac(self::$hashAlgorithm, hex2bin($hash1Hex),  hex2bin($res->salt2Hex));
        }
        $res->matched = !$res->err && $res->hash2Hex === $hash2ExpectedHex;
        if ($res->matched && $res->newVersionId && $res->newSalt2Hex) {
            $res->newHash2Hex = hash_hmac(self::$hashAlgorithm, hex2bin($hash1Hex), hex2bin($res->newSalt2Hex));
        }
        return $res;
    }

    public function newPassword($hash1Hex)
    {
        $res = $this->getSalt($hash1Hex);
        if (!$res->err) {
            $res->hash2Hex = hash_hmac(self::$hashAlgorithm, hex2bin($hash1Hex),  hex2bin($res->salt2Hex));
        }
        return $res;
    }

    private function getServer($attempts = 0)
    {
        if (empty($this->servers)) {
            return self::$defaultServer;
        }
        if (!$attempts) {
            return $this->servers[0];
        }
        return $this->servers[$attempts%count($this->servers)];
    }

    private function makeURL($url, $attempts = 0)
    {
        return sprintf('https://%s/%s', trim($this->getServer($attempts), '/'), ltrim($url, '/'));
    }

    private function get($url)
    {
        $ch = curl_init($this->makeURL($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: '.$this->userAgent,
            'Accept: application/json',
        ));
        $res = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status !== 200) {
            return new Response(['err' => $res]);
        }
        return new Response(json_decode($res, true));
    }
}
