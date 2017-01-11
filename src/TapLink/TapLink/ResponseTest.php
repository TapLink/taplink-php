<?php

namespace TapLink\TapLink;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function testNewResponse()
    {
        $resp = new Response();
        $this->assertNull($resp->err);
        $this->assertNull($resp->hash2Hex);
        $this->assertNull($resp->versionId);
        $this->assertNull($resp->newHash2Hex);
        $this->assertNull($resp->newVersionId);

        $resp = new Response([
            'err' => 'err',
            'hash2Hex' => 'hash2Hex',
            'vid' => 'versionId',
            'newHash2Hex' => 'newHash2Hex',
            'new_vid' => 'newVersionId',
            's2' => 'salt2Hex',
        ]);
        $this->assertEquals('err', $resp->err);
        $this->assertEquals('hash2Hex', $resp->hash2Hex);
        $this->assertEquals('versionId', $resp->versionId);
        $this->assertEquals('newHash2Hex', $resp->newHash2Hex);
        $this->assertEquals('newVersionId', $resp->newVersionId);
        $this->assertEquals('salt2Hex', $resp->salt2Hex);
    }
}
