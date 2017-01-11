<?php

namespace TapLink\TapLink;

use PHPUnit\Framework\TestCase;
use TapLink\TapLink\Client;

class ClientTest extends TestCase
{

    protected $testAppId;
    protected $client;

    protected function setUp()
    {
        $this->testAppId = "7ddf60de9250dce2f9f9a4ff1f5be257eb42e81d872a9381271edddae1fb83f2f99b89f138354fb8098d1e9b6681d6b0a58bbd2b26637b545c1c32607e85d7cf";
        $this->client = new Client($this->testAppId);
    }

    public function testInvalidURL() {
        $resp = $this->invokeMethod($this->client, 'get', ['/foobar']);
        $this->assertNotNull($resp->err);
    }

    public function testGetServer() {
        $this->client->servers = [];
        $this->assertEquals(Client::$defaultServer, $this->invokeMethod($this->client, 'getServer'));

        $this->client->servers = [Client::$defaultServer];
        $this->assertEquals(Client::$defaultServer, $this->invokeMethod($this->client, 'getServer'));

        $this->client->servers = ['foo.com', 'bar.com'];
        $this->assertEquals('foo.com', $this->invokeMethod($this->client, 'getServer', [0]));
        $this->assertEquals('bar.com', $this->invokeMethod($this->client, 'getServer', [1]));
    }

    public function testVectorsV3() {

        $hash1Hex = hash_hmac(Client::$hashAlgorithm, "secret", hex2bin("4cb78a1a60599df9c3bd9e4ac741a5f15feec1812b22a5f15bbad978039f2765f00dd82d97272eb3674cd164a0cc7024bbfd3704c6df6e2cb17a6562bd96ecb7"));

        $resp = $this->client->newPassword($hash1Hex);
        $this->assertNotNull($resp);
        $this->assertNull($resp->err);
        $this->assertEquals("9a4893d65a8eec23e520d0c7abe9c170ba61548c754b4805226e48d7519c55ed7f0daec920c5a99019042745007b99822e6853b8620be67955610b6d25f4b2f9", $resp->hash2Hex);

        $resp = $this->client->getSalt($hash1Hex);
        $this->assertNotNull($resp);
        $this->assertNull($resp->err);
        $this->assertEquals(3, $resp->versionId);
        $this->assertNull($resp->newVersionId);
        $this->assertNull($resp->newSalt2Hex);

        $sum = hash_hmac(Client::$hashAlgorithm, hex2bin($hash1Hex), hex2bin($resp->salt2Hex));
        $this->assertEquals('9a4893d65a8eec23e520d0c7abe9c170ba61548c754b4805226e48d7519c55ed7f0daec920c5a99019042745007b99822e6853b8620be67955610b6d25f4b2f9', $sum);
    }

    public function testVectorsV2()
    {
        $hash1Hex = hash_hmac(Client::$hashAlgorithm, "secret", hex2bin("4cb78a1a60599df9c3bd9e4ac741a5f15feec1812b22a5f15bbad978039f2765f00dd82d97272eb3674cd164a0cc7024bbfd3704c6df6e2cb17a6562bd96ecb7"));

        $resp = $this->client->getSalt($hash1Hex, 2);
        $this->assertNotNull($resp);
        $this->assertNull($resp->err);
        $this->assertEquals(2, $resp->versionId);
        $this->assertEquals(3, $resp->newVersionId);
        $this->assertEquals("080b64a980fe49664e6e29e7532ce4dab19a070da0618e32b20d7d0578e120458c1fcf7f3de0a9da7bbf7ba49cacabc05230c605f7032ab51323992ff3c35895", $resp->newSalt2Hex);

        $sum = hash_hmac(Client::$hashAlgorithm, hex2bin($hash1Hex), hex2bin($resp->salt2Hex));
        $this->assertEquals("d883c376526904dd90bd69709d259e7d4ac4fe1ee3ff65a2b6ed2920c8baad326b0c2043c6bb7750c6ad02284c2365d3c61298649107924cc44e60450031fbd2", $sum);

        $resp = $this->client->verifyPassword($hash1Hex, $sum, 2);
        $this->assertNotNull($resp);
        $this->assertTrue($resp->matched);
        $this->assertEquals(2, $resp->versionId);
        $this->assertEquals(3, $resp->newVersionId);
        $this->assertEquals("9a4893d65a8eec23e520d0c7abe9c170ba61548c754b4805226e48d7519c55ed7f0daec920c5a99019042745007b99822e6853b8620be67955610b6d25f4b2f9", $resp->newHash2Hex);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
