<?php

require_once __DIR__ . '/vendor/autoload.php';

use \TapLink\TapLink\Client;

$appId = "7ddf60de9250dce2f9f9a4ff1f5be257eb42e81d872a9381271edddae1fb83f2f99b89f138354fb8098d1e9b6681d6b0a58bbd2b26637b545c1c32607e85d7cf";
$taplink = new Client($appId);

$hash = hash_hmac(Client::$hashAlgorithm, "secret", hex2bin("4cb78a1a60599df9c3bd9e4ac741a5f15feec1812b22a5f15bbad978039f2765f00dd82d97272eb3674cd164a0cc7024bbfd3704c6df6e2cb17a6562bd96ecb7"));
$res = $taplink->getSalt($hash);
if ($res->err) {
    echo "An error occurred: ".$res->err;
    return;
}

echo "===================================\n";
echo "Version: {$res->versionId}\n";
echo "New version id: {$res->newVersionId}\n";
echo "Salt2Hex: {$res->salt2Hex}\n";
echo "New Salt2Hex: {$res->newSalt2Hex}\n";
echo "===================================\n";

$res = $taplink->newPassword($hash);
if ($res->err) {
    echo "An error occurred: ".$res->err;
    return;
}

echo "Version: {$res->versionId}\n";
echo "Salt2Hex: {$res->salt2Hex}\n";
echo "Hash2Hex: {$res->hash2Hex}\n";
echo "New version id: {$res->newVersionId}\n";
echo "New Salt2Hex: {$res->newSalt2Hex}\n";
echo "===================================\n";

$res = $taplink->verifyPassword($hash, "expected-hash");
if ($res->err) {
    echo "An error occurred: ".$res->err;
    return;
}
echo sprintf("Match: %s\n", $res->matched ? "true" : "false");
echo "Version: {$res->versionId}\n";
echo "Salt2Hex: {$res->salt2Hex}\n";
echo "Hash2Hex: {$res->hash2Hex}\n";
echo "New version id: {$res->newVersionId}\n";
echo "New Salt2Hex: {$res->newSalt2Hex}\n";
echo "===================================\n";
