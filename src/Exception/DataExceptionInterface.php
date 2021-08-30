<?php
namespace App\Exception;

interface DataExceptionInterface
{
    public function getData(): ?array;
}
