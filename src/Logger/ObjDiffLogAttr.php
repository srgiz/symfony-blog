<?php
declare(strict_types=1);

namespace App\Logger;

#[\Attribute]
class ObjDiffLogAttr
{
    public string $obj;

    public string $uid;

    public function __construct(string $obj, string $uid)
    {
        $this->obj = $obj;
        $this->uid = $uid;
    }
}
