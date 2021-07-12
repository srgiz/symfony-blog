<?php
declare(strict_types=1);

namespace App\Logger;

#[\Attribute]
class ObjDiffLogAttr
{
    public string $obj;

    public string $uid;

    public array $exclude = [];

    public function __construct(string $obj, string $uid, array $exclude = [])
    {
        $this->obj = $obj;
        $this->uid = $uid;
        $this->exclude = $exclude;
    }
}
