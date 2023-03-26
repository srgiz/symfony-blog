<?php

namespace App\Core\Http;

use Symfony\Component\HttpFoundation\Cookie;

interface HeadersHandlerInterface
{
    public function setHeader(string $name, string $value): void;

    public function setCookie(Cookie $cookie): void;
}
