<?php

namespace TapLink\TapLink;

class Response
{
    public $err;
    public $matched;

    private $vid;
    private $s2;
    private $new_vid;
    private $new_s2;

    public $hash2Hex;
    public $newHash2Hex;

    function __construct(Array $params = [])
    {
        foreach($params as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     * @SuppressWarnings("NPath")
     * @SuppressWarnings("CyclomaticComplexity")
     */
    public function __get($key)
    {
        // Return binary versions of properties if requested.
        if ($key === "hash2") return $this->hash2Hex ? hex2bin($this->hash2Hex) : null;
        if ($key === "newSalt2") return $this->new_s2 ? hex2bin($this->new_s2) : null;
        if ($key === "salt2") return $this->s2 ? hex2bin($this->s2) : null;

        // Map keys to their internal name.
        if ($key === "newSalt2Hex") return $this->new_s2;
        if ($key === "salt2Hex") return $this->s2;
        if ($key === "versionId") return $this->vid;
        if ($key === "newVersionId") return $this->new_vid;

        return property_exists($this, $key) ? $this->{$key} : null;
    }
}
