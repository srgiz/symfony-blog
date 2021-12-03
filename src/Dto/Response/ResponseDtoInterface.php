<?php
namespace App\Dto\Response;

interface ResponseDtoInterface
{
    public function getData(): mixed;

    public function getCookies(): array;
}
