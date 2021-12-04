<?php
namespace App\Dto\Response;

interface ResponseDtoInterface
{
    public function getMeta(): ?array;

    public function getData(): mixed;

    public function getCookies(): array;
}
