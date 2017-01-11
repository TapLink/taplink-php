[![Build Status](https://semaphoreci.com/api/v1/brad/taplink-php/branches/master/shields_badge.svg)](https://semaphoreci.com/brad/taplink-php)
[![codecov](https://codecov.io/gh/bradberger/taplink-php/branch/master/graph/badge.svg)](https://codecov.io/gh/bradberger/taplink-php)

# PHP Bindings for Blind Hashing

## Installation

If you're using composer:

```bash
composer require taplink/taplink-php
```

Otherwise, include the `src/TapLink/TapLink/Client.php` and `src/TapLink/TapLink/Response.php` files.

## Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use \TapLink\TapLink\Client;

$appId = "my-app-id";
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

```
